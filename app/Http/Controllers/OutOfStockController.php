<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListOutOfStockProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Products')]
class OutOfStockController extends Controller
{
    /**
     * List out-of-stock products
     *
     * Requires 'list out of stock products' capability
     *
     * @param ListOutOfStockProductsRequest $request
     * @return LengthAwarePaginator
     */
    #[Authenticated]
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, paginate: 10)]
    public function index(ListOutOfStockProductsRequest $request)
    {
        $perPage = $request->input('per_page', 100);
        $page = $request->input('page', 1);

        $query = Product::where('amount', '<=', 0);

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage(null, $perPage), Product::count(), $perPage, $page);
    }
}
