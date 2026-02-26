<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    // ============ CATEGORY ============ //
    public function addCategory()
    {
        return view('admin.addcategory');
    }

    public function postAddCategory(Request $request)
    {
        $category = new Category();
        $category->category = $request->category;
        $category->save();

        return redirect()->back()->with('category_message', 'Category added successfully!');
    }

    public function viewCategory()
    {
        $categories = Category::all();
        return view('admin.viewcategory', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('deletecategory_message', 'Deleted successfully!');
    }

    public function updateCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.updatecategory', compact('category'));
    }

    public function postUpdateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->category = $request->category;
        $category->save();

        return redirect()->back()->with('category_updated_message', 'Category updated successfully!');
    }

    // ============ PRODUCT ============ //
    public function addProduct()
    {
        $categories = Category::all();
        return view('admin.addproduct', compact('categories'));
    }

    public function postAddProduct(Request $request)
    {
        $product = new Product();
        $product->product_title = $request->product_title;
        $product->product_description = $request->product_description;
        $product->product_quantity = $request->product_quantity;
        $product->product_prices = $request->product_price;
        $product->product_category = $request->product_category;

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('products'), $imagename);
            $product->product_image = $imagename;
        }

        $product->save();

        return redirect()->back()->with('product_message', 'Product added successfully!');
    }

    public function viewProduct()
    {
        $products = Product::all();
        return view('admin.viewproduct', compact('products'));
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $image_path = public_path('products/' . $product->product_image);

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $product->delete();

        return redirect()->back()->with('deleteproduct_message', 'Product deleted successfully!');
    }

    public function updateProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.updateproduct', compact('product', 'categories'));
    }

    public function postUpdateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->product_title = $request->product_title;
        $product->product_description = $request->product_description;
        $product->product_quantity = $request->product_quantity;
        $product->product_prices = $request->product_price;
        $product->product_category = $request->product_category;

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $oldImagePath = public_path('products/' . $product->product_image);

            if ($product->product_image && file_exists($oldImagePath) && !is_dir($oldImagePath)) {
                unlink($oldImagePath);
            }

            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('products'), $imagename);
            $product->product_image = $imagename;
        }

        $product->save();

        return redirect()->back()->with('product_message', 'Product updated successfully!');
    }

    // ============ ORDERS ============ //
    public function viewOrders()
    {
        $orders = Order::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.vieworders', compact('orders'));
    }

    public function changeStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('confirm_order', 'Order status updated successfully!');
    }
}
