<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchProductRequest $request)
    {
        $perPage = 10;

        // start with dummy query
        $query = Product::whereRaw('1=1');

        // exact matching attributes
        $exactMatchAttributes = ['sku', 'name', 'category'];
        foreach ($exactMatchAttributes as $attribute) {
            if ($request->has($attribute)) {
                $query = $query->where($attribute, '=', $request->input($attribute));
            }
        }

        // lower bound attributes
        $lowerBoundAttributes = [
            'rating_higher_than' => 'avg_rating',
            'min_price' => 'price'
        ];
        foreach ($lowerBoundAttributes as $requestKey => $attribute) {
            if ($request->has($requestKey)) {
                $query = $query->where($attribute, '>=', $request->input($requestKey));
            }
        }

        // upper bound attributes
        $lowerBoundAttributes = [
            'max_price' => 'price',
        ];
        foreach ($lowerBoundAttributes as $requestKey => $attribute) {
            if ($request->has($requestKey)) {
                $query = $query->where($attribute, '<=', $request->input($requestKey));
            }
        }

        // tags
        if ($request->has('tags') && count($request->input('tags')) > 0) {
            $query = $query->withAnyTags($request->input('tags'));
        }

        $collection = ProductResource::collection($query->paginate($perPage));

        return new LengthAwarePaginator($collection->forPage(null, $perPage), Product::count(), $perPage);
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
