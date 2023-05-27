<?php

namespace Tests\Feature;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    public function test_it_can_find_product_by_exact_match_attributes()
    {
        $products = Product::factory(5)->create();

        // exact match attributes
        $res = $this->call('GET', '/api/product', [
            'sku' => $products[0]->sku,
            'name' => $products[0]->name,
            'category' => $products[0]->category,
        ]);

        $res->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(function (AssertableJson $json) use ($products) {
                $json->etc();
                $expectedData = (new ProductResource($products[0]))->toArray([]);
                foreach ($expectedData as $key => $val) {
                    $json->where('data.0.' . $key, $val);
                }
            });
    }

    public function test_it_can_find_products_by_tags()
    {

    }
}
