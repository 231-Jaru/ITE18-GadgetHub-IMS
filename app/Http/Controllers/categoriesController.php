<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    // Get all categories
    public function index()
    {
        $categories = Categories::all();
        
        if (request()->is('api/*')) {
            return response()->json($categories, 200);
        }
        
        return view('categories.index', compact('categories'));
    }

    // Show form to create new category
    public function create()
    {
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Create category form'], 200);
        }
        
        return view('categories.create');
    }

    // Get single category by ID
    public function show($id)
    {
        $category = Categories::find($id);
        
        if (!$category) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return redirect('/categories')->with('error', 'Category not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($category, 200);
        }
        
        return view('categories.show', compact('category'));
    }

    // Show form to edit category
    public function edit($id)
    {
        $category = Categories::find($id);
        
        if (!$category) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return redirect('/categories')->with('error', 'Category not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($category, 200);
        }
        
        return view('categories.edit', compact('category'));
    }

    // Create new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'CategoryName' => 'required|string|max:100|unique:categories,CategoryName',
        ]);

        $category = Categories::create($validated);
        
        if (request()->is('api/*') || $request->expectsJson()) {
            return response()->json($category, 201);
        }
        
        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Categories::find($id);
        
        if (!$category) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return redirect('/categories')->with('error', 'Category not found');
        }

        $validated = $request->validate([
            'CategoryName' => 'required|string|max:100|unique:categories,CategoryName,' . $id . ',CategoryID',
        ]);

        $category->update($validated);
        
        if (request()->is('api/*')) {
            return response()->json($category, 200);
        }
        
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    // Delete category
    public function destroy($id)
    {
        $category = Categories::find($id);
        
        if (!$category) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return redirect('/categories')->with('error', 'Category not found');
        }

        // Check if category has gadgets
        if ($category->gadgets()->count() > 0) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Cannot delete category with existing gadgets'], 400);
            }
            return redirect('/categories')->with('error', 'Cannot delete category with existing gadgets');
        }

        $category->delete();
        
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Category deleted successfully'], 200);
        }
        
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}