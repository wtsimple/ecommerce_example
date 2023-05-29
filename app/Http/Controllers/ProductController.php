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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchProductRequest $request, ProductSearchService $service)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $query = $service->buildSearchQuery($request);

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage($page, $perPage), $query->count(), $perPage, $page);
    }

    public function count(SearchProductRequest $request, ProductSearchService $service)
    {
        $query = $service->buildSearchQuery($request);

        return response(['count' => $query->count()]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->all());
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return response(['data' => new ProductResource($product)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->can(Role::DELETE_PRODUCT)) {
            abort(403);
        }
        $product->delete();
    }
}
