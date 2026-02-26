@extends('admin.maindesign')

@section('add_category')

@if(session('category_message'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('category_message') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid mt-4">
    <div class="card bg-dark border-0 shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="card-title text-center text-light mb-4">Add New Category</h4>

            <form action="{{ route('admin.postaddcategory') }}" method="POST" class="d-flex gap-2 justify-content-center">
                @csrf
                <input
                    type="text"
                    name="category"
                    class="form-control bg-dark text-light border-secondary"
                    placeholder="Enter Category Name!"
                    required>

                <button type="submit" class="btn btn-primary fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Add Category
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
