@extends('admin.maindesign')

@section('add_product')

@if(session('product_message'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('product_message') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid mt-4">
    <div class="card bg-dark border-0 shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h4 class="card-title text-center text-light mb-4">Add New Product</h4>

            <form action="{{ route('admin.postaddproduct') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label text-light">Product Title</label>
                    <input type="text" name="product_title" class="form-control bg-dark text-light border-secondary" placeholder="Enter product title" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Product Description</label>
                    <textarea name="product_description" class="form-control bg-dark text-light border-secondary" rows="4" placeholder="Product description..." required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Quantity</label>
                        <input type="number" name="product_quantity" class="form-control bg-dark text-light border-secondary" placeholder="Enter product quantity" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Price</label>
                        <input type="number" name="product_price" class="form-control bg-dark text-light border-secondary" placeholder="Enter product price" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Upload Product Image</label>
                    <input type="file" name="product_image" class="form-control bg-dark text-light border-secondary">
                </div>

                <div class="mb-4">
                    <label class="form-label text-light">Select Category</label>
                    <select name="product_category" class="form-select bg-dark text-light border-secondary">
                        @foreach($categories as $category)
                            <option value="{{ $category->category }}">{{ $category->category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
