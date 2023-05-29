<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductSalesTest extends TestCase
{
    public function test_it_can_sell_a_product_that_is_in_stock() {
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

    // cannot buy out of stock product

    // unauthenticated user is not allowed to make purchase

    // admins can see the list of purchases over a period of time
    // admins can see total revenue ove a period of time
    // admins can see a list of products out of stock
}
