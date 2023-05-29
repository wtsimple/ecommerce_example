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
        list($res, $resCount) = $this->makeCall([
            'sku' => $this->products[0]->sku,
            'name' => $this->products[0]->name,
            'category' => $this->products[0]->category,
        ]);

        $this->assertContainsSingleProduct($res, $resCount, $this->products[0]);
    }

    public function test_it_can_find_products_by_tags()
    {
        $this->products[1]->syncTags(['my-super-tag-1', 'my-super-tag-2']);

        list($res, $resCount) = $this->makeCall([
            'tags' => ['my-super-tag-1', 'my-super-tag-2']
        ]);

        $this->assertContainsSingleProduct($res, $resCount, $this->products[1]);
    }

    public function test_it_can_find_products_by_ratings()
    {
        foreach ($this->products as $product) {
            $product->avg_rating = 3.5;
        }
        $this->products[2]->avg_rating = 4.5;
        $this->products->map(function ($prod) {$prod->save();});

        list($res, $resCount) = $this->makeCall([
            'rating_higher_than' => 4.0
        ]);

        $this->assertContainsSingleProduct($res, $resCount, $this->products[2]);
    }

    public function test_it_can_find_products_by_price_range()
    {
        foreach ($this->products as $product) {
            $product->price = 200;
        }
        $this->products[2]->price = 300;
        $this->products->map(function ($prod) {$prod->save();});

        list($res, $resCount) = $this->makeCall([
            'min_price' => 250,
            'max_price' => 400,
        ]);

        $this->assertContainsSingleProduct($res, $resCount, $this->products[2]);
    }

    public function test_it_can_find_products_by_text_search()
    {
        $this->products[3]->description = 'This is a shiny new awesome description that other products lack';
        $this->products[3]->save();

        list($res, $resCount) = $this->makeCall( [
            'text_query' => 'awesome description',
        ]);

        $this->assertContainsSingleProduct($res, $resCount, $this->products[3]);
    }



    private function assertContainsSingleProduct(TestResponse $res, TestResponse $resCount, Product $product): void
    {
        $resCount->assertOk()->json(['count' => 1]);

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

    /**
     * @param array $params
     * @return TestResponse[]
     */
    private function makeCall(array $params): array
    {
        $res = $this->call('GET', '/api/product', $params);
        $resCount = $this->call('GET', '/api/product/count', $params);

        return [$res, $resCount];
    }
}
