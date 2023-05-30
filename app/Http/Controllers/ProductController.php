<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Role;
use App\Services\ProductSearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group('Products')]
class ProductController extends Controller
{
    /**
     * Product search
     */
    public function index(SearchProductRequest $request, ProductSearchService $service)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $query = $service->buildSearchQuery($request);

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage($page, $perPage), $query->count(), $perPage, $page);
    }

    /**
     * Count products matching search
     */
    public function count(SearchProductRequest $request, ProductSearchService $service)
    {
        $query = $service->buildSearchQuery($request);

        return response(['count' => $query->count()]);
    }


    /**
     * Get single product
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Create product
     */
    #[Authenticated]
    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->all());
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Update product
     */
    #[Authenticated]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return response(['data' => new ProductResource($product)]);
    }

    /**
     * (Soft) delete product
     */
    #[Authenticated]
    public function destroy(Product $product)
    {
        if (!Auth::user()->can(Role::DELETE_PRODUCT)) {
            abort(403);
        }
        $product->delete();
    }
}
