<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductSalesTest extends TestCase
{
    public function test_it_can_sell_a_product_that_is_in_stock()
    {
        $user = User::factory()->createOne();
        $product = Product::factory()->createOne(['amount' => 3]);
        // make a sale
        Sanctum::actingAs($user);
        $res = $this->postJson('/api/purchase', ['sku' => $product->sku]);
        $res->assertStatus(201);
        // assert the product stock has decreased
        $product->refresh();
        $this->assertEquals(2, $product->amount);
        // assert the purchase was registered in db
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id, 'sku' => $product->sku, 'amount' => 1, 'total_paid' => $product->price
        ]);
    }

    public function test_it_cannot_buy_out_of_stock_product()
    {
        $user = User::factory()->createOne();
        $product = Product::factory()->createOne(['amount' => 0]);
        // make a sale
        Sanctum::actingAs($user);
        $res = $this->postJson('/api/purchase', ['sku' => $product->sku]);
        $res->assertStatus(400)
            ->assertJson(['error' => true, 'error_msg' => "Product {$product->name} is out of stock"]);
        // assert the product stock has not decreased
        $product->refresh();
        $this->assertEquals(0, $product->amount);
        // assert the purchase was registered in db
        $this->assertDatabaseMissing('purchases', ['sku' => $product->sku]);
    }

    public function test_guest_user_cannot_make_purchase()
    {
        $product = Product::factory()->createOne(['amount' => 10]);
        $res = $this->postJson('/api/purchase', ['sku' => $product->sku]);
        $res->assertStatus(401);
    }

    public function test_admins_can_see_purchases_and_total_revenue_over_a_time_period()
    {
        $purchases = Purchase::factory(5)->create(['updated_at' => now(), 'created_at' => now()]);

        $admin = UserFactory::createOneAdmin();
        Sanctum::actingAs($admin);
        $res = $this->call('GET', '/api/purchase', [
            'per_page' => 100,
            'from' => now('utc')->subMinutes(30)->toIso8601String(),
            'to' => now('utc')->addMinutes(30)->toIso8601String(),
        ]);

        $res->assertOk()
            ->assertJsonCount($purchases->count(), 'data')
            ->assertJson(function (AssertableJson $json) use ($purchases) {
                $json->etc();
                for ($i=0; $i<count($purchases); $i++) {
                    $json->where("data.$i.total_paid", $purchases[$i]->total_paid);
                }
            });

        $resTotalRevenue = $this->call('GET', '/api/purchase/revenue', [
            'from' => now('utc')->subMinutes(30)->toIso8601String(),
            'to' => now('utc')->addMinutes(30)->toIso8601String(),
        ]);
        $resTotalRevenue->assertOk()->assertJson([
            'data' => ['total_revenue' => $purchases->sum('total_paid')]
        ]);
    }
    // admins can see a list of products out of stock
}
