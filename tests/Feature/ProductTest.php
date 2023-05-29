<?php

namespace Tests\Feature;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    private array $privilegedUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = UserFactory::createOneAdmin();
        $editor = UserFactory::createOneEditor();
        $this->privilegedUsers = [
            'admin' => $admin,
            'editor' => $editor
        ];
    }

    public function test_guest_user_can_read_product()
    {
        $product = Product::factory()->createOne();

        $res = $this->getJson("/api/product/sku/$product->sku");

        $res->assertOk()->assertJson(function (AssertableJson $json) use ($product) {
            $json->where('data.sku', $product->sku);
            $json->where('data.name', $product->name);
            $json->where('data.price', $product->price);
            $json->where('data.amount', $product->amount);
            $json->where('data.description', $product->description);
            $json->where('data.additional_info', $product->additional_info);
            $json->where('data.avg_rating', $product->avg_rating);
        });
    }

    public function test_privileged_users_can_delete_product()
    {
        foreach ($this->privilegedUsers as $user) {
            Sanctum::actingAs($user);

            $productToDelete = Product::factory()->createOne();
            $this->assertDatabaseHas('products', ['sku' => $productToDelete->sku]);
            $res = $this->deleteJson("/api/product/sku/$productToDelete->sku");
            $res->assertOk();
            // verify product was (soft) deleted
            $productsInDB = Product::where(['sku' => $productToDelete->sku])->count();
            $this->assertEquals(0, $productsInDB);
        }

    }

    public function test_privileged_users_can_create_product()
    {
        foreach ($this->privilegedUsers as $user) {
            Sanctum::actingAs($user);
            $createData = Product::factory()->makeOne()->toArray();
            $res = $this->postJson('/api/product', $createData);
            $res->assertStatus(201)->assertJson(function (AssertableJson $json) use ($createData) {
                foreach ($createData as $key => $val) {
                    $json->where('data.' . $key, $val);
                }
            });
            $this->assertDatabaseHas('products', $createData);
        }
    }

    public function test_privileged_users_can_update_product()
    {
        foreach ($this->privilegedUsers as $user) {
            Sanctum::actingAs($user);

            $productToUpdate = Product::factory()->createOne();
            $updateData = Product::factory()->makeOne(['sku' => $productToUpdate->sku])->toArray();
            $res = $this->patchJson("/api/product/sku/$productToUpdate->sku", $updateData);
            $res->assertOk()->assertJson(function (AssertableJson $json) use ($updateData) {
                foreach ($updateData as $key => $val) {
                    $json->where('data.' . $key, $val);
                }
            });
            $this->assertDatabaseHas('products', $updateData);
        }
    }

    public function test_admin_can_list_out_of_stock_products()
    {
        Sanctum::actingAs($this->privilegedUsers['admin']);
        $products = Product::factory(5)->create(['amount' => 0]);

        $res = $this->getJson('/api/product/out-of-stock');
        $res->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(function (AssertableJson $json) use ($products) {
                $json->etc();
                foreach ($products as $prodKey => $product) {
                    $expectedData = (new ProductResource($product))->toArray([]);
                    foreach ($expectedData as $prodAttribute => $val) {
                        $json->where("data.$prodKey.$prodAttribute", $val);
                    }
                }
            });
    }



    public function test_normal_user_cannot_create_delete_update_products()
    {
        $user = User::factory()->createOne();
        $unprivilegedUsers = [
            // BEWARE the order here matters
            // we need to start unauthenticated
            // or otherwise, once we use actingAs,
            // all further requests will be authenticated
            ['user' => 'guest', 'expected_error' => 401],
            ['user' => $user, 'expected_error' => 403],
        ];

        foreach ($unprivilegedUsers as $userData) {
            $expectedError = $userData['expected_error'];
            if ($userData['user'] !== 'guest') {
                Sanctum::actingAs($userData['user']);
            }

            // deletion fails
            $productToDelete = Product::factory()->createOne();
            $this->assertDatabaseHas('products', ['sku' => $productToDelete->sku]);
            $res = $this->deleteJson("/api/product/sku/$productToDelete->sku");
            $res->assertStatus($expectedError);
            $this->assertDatabaseHas('products', ['sku' => $productToDelete->sku]);

            // creation fails
            $createData = Product::factory()->makeOne()->toArray();
            $res = $this->postJson('/api/product', $createData);
            $res->assertStatus($expectedError);
            $this->assertDatabaseMissing('products', ['sku' => $createData['sku']]);

            // update fails
            $productToUpdate = Product::factory()->createOne();
            $updateData = Product::factory()->makeOne(['sku' => $productToUpdate->sku])->toArray();
            $res = $this->patchJson("/api/product/sku/$productToUpdate->sku", $updateData);
            $res->assertStatus($expectedError);
            $beforeUpdateData = $productToUpdate->toArray();
            unset($beforeUpdateData['updated_at']);
            unset($beforeUpdateData['created_at']);
            $this->assertDatabaseHas('products', $beforeUpdateData);
        }
    }
}
