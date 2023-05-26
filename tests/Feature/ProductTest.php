<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Factories\UserFactory;
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
        $this->privilegedUsers = [$admin, $editor];
    }

    public function test_guest_user_can_read_product()
    {
        $product = Product::factory()->createOne();

        $res = $this->getJson("/api/product/$product->sku");

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
            $res = $this->deleteJson("/api/product/$productToDelete->sku");
            $res->assertOk();
            $this->assertDatabaseMissing('products', ['sku' => $productToDelete->sku]);
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
            $updateData = Product::factory()->makeOne()->toArray();
            $updateData['sku'] = $productToUpdate->sku; // same sku is needed to update
            $res = $this->patchJson("/api/product/$productToUpdate->sku", $updateData);
            $res->assertOk()->assertJson(function (AssertableJson $json) use ($updateData) {
                foreach ($updateData as $key => $val) {
                    $json->where('data.' . $key, $val);
                }
            });
            $this->assertDatabaseHas('products', $updateData);
        }
    }


    // basic user cannot create/edit/delete product
    // guest user cannot create/edit/delete product
}
