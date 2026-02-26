@extends('admin.maindesign')

@section('view_product')

{{-- Notifikasi --}}
@if (session('deletecategory_message'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    {{ session('deletecategory_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('deleteproduct_message'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    {{ session('deleteproduct_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Form Pencarian --}}
<div class="mb-4">
    <form action="{{ route('admin.searchproduct') }}" method="post" class="d-flex gap-2">
        @csrf
        <input type="text" name="search" class="form-control" placeholder="What are you searching for...">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

{{-- Tabel Produk --}}
<div class="table-responsive">
    <table class="table text-center" style="background-color: #1e1f26; color: #ffffff;">
        <thead style="background-color: #d9d9d9; color: #000;">
            <tr>
                <th>ID</th>
                <th>Product Title</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products->sortBy('id') as $product)
            <tr style="border-bottom: 1px solid #444;">
                <td>{{ $product->id }}</td>
                <td class="text-start" style="max-width: 250px;">{{ $product->product_title }}</td>
                <td>{{ $product->product_category }}</td>
                <td>{{ $product->product_quantity }}</td>
                <td>$ {{ number_format($product->product_prices, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('admin.updateproduct', $product->id) }}"
                       class="btn btn-success btn-sm me-1">
                        Update
                    </a>
                    <a href="{{ route('admin.deleteproduct', $product->id) }}"
                       onclick="return confirm('Are you sure you want to delete this product?')"
                       class="btn btn-danger btn-sm">
                        Delete
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
