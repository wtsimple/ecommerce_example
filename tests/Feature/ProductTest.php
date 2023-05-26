<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
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

    // admin user can create/edit/delete product
    // editor user can create/edit/delete product
    // basic user cannot create/edit/delete product
    // guest user cannot create/edit/delete product
}
