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
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Products')]
class ProductController extends Controller
{
    /**
     * Product search
     */
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, paginate: 10)]
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
    #[Response(["count" => 3], status: 200, description: "Count matched products")]
    public function count(SearchProductRequest $request, ProductSearchService $service)
    {
        $query = $service->buildSearchQuery($request);

        return response(['count' => $query->count()]);
    }


    /**
     * Get single product
     */
    #[ResponseFromApiResource(ProductResource::class, Product::class)]
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Create product
     *
     * Requires 'create product' capabilities
     */
    #[Authenticated]
    #[ResponseFromApiResource(ProductResource::class, Product::class)]
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
     *
     * Requires 'update product' capabilities
     */
    #[Authenticated]
    #[ResponseFromApiResource(ProductResource::class, Product::class)]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return response(['data' => new ProductResource($product)]);
    }

    /**
     * (Soft) delete product
     *
     * Requires 'delete product' capabilities
     */
    #[Authenticated]
    #[Response(['message' => "Product 'WONDERFUL-T-SHIRT' deleted"], status: 200, description: "Product correctly (soft) deleted")]
    public function destroy(Product $product)
    {
        if (!Auth::user()->can(Role::DELETE_PRODUCT)) {
            abort(403);
        }
        $product->delete();
        return response(['message' => "Product $product->sku deleted"]);
    }
}
