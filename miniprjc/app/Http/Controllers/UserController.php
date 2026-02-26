<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCart;
use App\Models\Order;

class UserController extends Controller
{
    private function getCartCount()
    {
        return Auth::check()
            ? ProductCart::where('user_id', Auth::id())->count()
            : '';
    }

    public function home()
    {
        $count = $this->getCartCount();
        $products = Product::latest()->take(2)->get();

        return view('users.index', compact('products', 'count'));
    }

    public function index()
    {
        $count = $this->getCartCount();

        if (Auth::check() && Auth::user()->user_type == "user") {
            return view('users.dashboard', compact('count'));
        } elseif (Auth::user()->user_type == "admin") {
            return view('admin.dashboard', compact('count'));
        }
    }

    public function productDetails($id)
    {
        $count = $this->getCartCount();
        $product = Product::findOrFail($id);

        return view('product_details', compact('product', 'count'));
    }

    public function allProducts()
    {
        $count = $this->getCartCount();
        $products = Product::all();

        return view('allproducts', compact('products', 'count'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $userId = Auth::id();

        $existingCart = ProductCart::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        $qty = (int) ($request->quantity ?? 1);
        if ($qty < 1) $qty = 1;

        if ($existingCart) {
            $existingCart->quantity += $qty;
            $existingCart->save();
        } else {
            $newCart = new ProductCart();
            $newCart->user_id = $userId;
            $newCart->product_id = $product->id;
            $newCart->quantity = $qty;
            $newCart->save();
        }

        return redirect()->back()->with('cart_message', 'Added to cart successfully!');
    }

    public function cartProducts()
    {
        $count = $this->getCartCount();
        $cart = Auth::check()
            ? ProductCart::where('user_id', Auth::id())->get()
            : collect();

        return view('viewcartproducts', compact('cart', 'count'));
    }

    public function removeCartProducts($id)
    {
        $cartProduct = ProductCart::findOrFail($id);
        $cartProduct->delete();

        return redirect()->back()->with('cart_message', 'Product removed from cart.');
    }

    public function confirmOrder(Request $request)
    {
        $userId = Auth::id();
        $cartProducts = ProductCart::where('user_id', $userId)->get();

        if ($cartProducts->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $address = $request->input('receiver_address');
        $phone = $request->input('receiver_phone');

        DB::transaction(function () use ($cartProducts, $userId, $address, $phone) {
            foreach ($cartProducts as $cartProduct) {
                $product = Product::find($cartProduct->product_id);
                if (!$product) continue;

                Order::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $cartProduct->quantity,
                    'receiver_address' => $address,
                    'receiver_phone' => $phone,
                    'status' => 'pending',
                    'total_price' => $cartProduct->quantity * $product->product_prices,
                ]);
            }

            ProductCart::where('user_id', $userId)->delete();
        });

        return redirect()->back()->with('confirm_order', 'Order Confirmed!');
    }

    public function myOrders()
    {
        $count = $this->getCartCount();
        $orders = Order::where('user_id', Auth::id())->get();
        return view('users.viewmyorders', compact('orders', 'count'));
    }
}
