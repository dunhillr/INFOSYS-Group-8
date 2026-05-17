<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $allProducts = Product::where('is_active', true)->orderBy('product_name')->get();
        return view('products.create', compact('allProducts'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $product = Product::create($data);
        ActivityLogService::log(Auth::id(), 'create', 'products', 'Created product #'.$product->id, $request);
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $allProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->orderBy('product_name')
            ->get();
        return view('products.edit', compact('product', 'allProducts'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);
        ActivityLogService::log(Auth::id(), 'update', 'products', 'Updated product #'.$product->id, $request);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        if (!Auth::user()->isOwner()) {
            abort(403, 'Unauthorized action.');
        }
        $id = $product->id;
        $product->delete();
        ActivityLogService::log(Auth::id(), 'delete', 'products', 'Deleted product #'.$id, $request);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
