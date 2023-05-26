<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Factories\UserFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
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

    public function test_admin_user_can_crud_product()
    {
        $admin = UserFactory::createOneAdmin();

        Sanctum::actingAs($admin);

        // delete
        $productToDelete = Product::factory()->createOne();
        $this->assertDatabaseHas('products', ['sku' => $productToDelete->sku]);
        $res = $this->deleteJson("/api/product/$productToDelete->sku");
        $res->assertOk();
        $this->assertDatabaseMissing('products', ['sku' => $productToDelete->sku]);

        //create
        $createData = [
            'sku' => $this->faker->uuid,
            'name' => $this->faker->text(50),
            'price' => $this->faker->numberBetween(1, 200),
            'amount' => $this->faker->numberBetween(0, 100),
            'description' => $this->faker->text(),
            'additional_info' => $this->faker->text(),
            'avg_rating' => $this->faker->randomFloat(2, 0, 5)
        ];
        $res = $this->postJson('/api/product', $createData);
        $res->assertStatus(201)->assertJson(function (AssertableJson $json) use ($createData) {
            foreach ($createData as $key => $val) {
                $json->where('data.' . $key, $val);
            }
        });
        $this->assertDatabaseHas('products', $createData);

        // update
        $productToUpdate = Product::factory()->createOne();
        $updateData = [
            'sku' => $productToUpdate->sku,
            'name' => $this->faker->text(50),
            'price' => $this->faker->numberBetween(1, 200),
            'amount' => $this->faker->numberBetween(0, 100),
            'description' => $this->faker->text(),
            'additional_info' => $this->faker->text(),
            'avg_rating' => $this->faker->randomFloat(2, 0, 5)
        ];
        $res = $this->patchJson("/api/product/$productToUpdate->sku", $updateData);
        $res->assertOk()->assertJson(function (AssertableJson $json) use ($updateData) {
            foreach ($updateData as $key => $val) {
                $json->where('data.' . $key, $val);
            }
        });
        $this->assertDatabaseHas('products', $updateData);
    }
    // editor user can create/edit/delete product
    // basic user cannot create/edit/delete product
    // guest user cannot create/edit/delete product
}
