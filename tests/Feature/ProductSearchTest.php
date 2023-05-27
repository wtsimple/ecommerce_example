<?php

namespace Tests\Feature;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    private Collection $products;

    protected function setUp(): void
    {
        parent::setUp();

        $this->products = Product::factory(5)->create();
    }

    public function test_it_can_find_product_by_exact_match_attributes()
    {
        // exact match attributes
        $res = $this->call('GET', '/api/product', [
            'sku' => $this->products[0]->sku,
            'name' => $this->products[0]->name,
            'category' => $this->products[0]->category,
        ]);

        $this->assertContainsSingleProduct($res, $this->products[0]);
    }

    public function test_it_can_find_products_by_tags()
    {
        $this->products[1]->syncTags(['my-super-tag-1', 'my-super-tag-2']);

        $res = $this->call('GET', '/api/product', [
            'tags' => ['my-super-tag-1', 'my-super-tag-2']
        ]);

        $this->assertContainsSingleProduct($res, $this->products[1]);
    }

    public function test_it_can_find_products_by_ratings()
    {
        foreach ($this->products as $product) {
            $product->avg_rating = 3.5;
        }
        $this->products[2]->avg_rating = 4.5;
        $this->products->map(function ($prod) {$prod->save();});

        $res = $this->call('GET', '/api/product', [
            'rating_higher_than' => 4.0
        ]);

        $this->assertContainsSingleProduct($res, $this->products[2]);
    }



    private function assertContainsSingleProduct(TestResponse $res, Product $product): void
    {
        $res->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(function (AssertableJson $json) use ($product) {
                $json->etc();
                $expectedData = (new ProductResource($product))->toArray([]);
                foreach ($expectedData as $key => $val) {
                    $json->where('data.0.' . $key, $val);
                }
            });
    }
}
