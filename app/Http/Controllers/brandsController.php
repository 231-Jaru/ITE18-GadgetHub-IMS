<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    // Get all brands
    public function index()
    {
        $brands = Brands::all();
        
        if (request()->is('api/*')) {
            return response()->json($brands, 200);
        }
        
        return view('brands.index', compact('brands'));
    }

    // Show form to create new brand
    public function create()
    {
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Create brand form'], 200);
        }
        
        return view('brands.create');
    }

    // Get single brand by ID
    public function show($id)
    {
        $brand = Brands::find($id);
        
        if (!$brand) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Brand not found'], 404);
            }
            return redirect('/brands')->with('error', 'Brand not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($brand, 200);
        }
        
        return view('brands.show', compact('brand'));
    }

    // Show form to edit brand
    public function edit($id)
    {
        $brand = Brands::find($id);
        
        if (!$brand) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Brand not found'], 404);
            }
            return redirect('/brands')->with('error', 'Brand not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($brand, 200);
        }
        
        return view('brands.edit', compact('brand'));
    }

    // Create new brand
    public function store(Request $request)
    {
        $validated = $request->validate([
            'BrandName' => 'required|string|max:100|unique:brands,BrandName',
        ]);

        $brand = Brands::create($validated);
        
        if (request()->is('api/*') || $request->expectsJson()) {
            return response()->json($brand, 201);
        }
        
        return redirect()->route('brands.index')->with('success', 'Brand created successfully!');
    }

    // Update brand
    public function update(Request $request, $id)
    {
        $brand = Brands::find($id);
        
        if (!$brand) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Brand not found'], 404);
            }
            return redirect('/brands')->with('error', 'Brand not found');
        }

        $validated = $request->validate([
            'BrandName' => 'required|string|max:100|unique:brands,BrandName,' . $id . ',BrandID',
        ]);

        $brand->update($validated);
        
        if (request()->is('api/*')) {
            return response()->json($brand, 200);
        }
        
        return redirect()->route('brands.index')->with('success', 'Brand updated successfully!');
    }

    // Delete brand
    public function destroy($id)
    {
        $brand = Brands::find($id);
        
        if (!$brand) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Brand not found'], 404);
            }
            return redirect('/brands')->with('error', 'Brand not found');
        }

        // Check if brand has gadgets
        if ($brand->gadgets()->count() > 0) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Cannot delete brand with existing gadgets'], 400);
            }
            return redirect('/brands')->with('error', 'Cannot delete brand with existing gadgets');
        }

        $brand->delete();
        
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Brand deleted successfully'], 200);
        }
        
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully!');
    }
}