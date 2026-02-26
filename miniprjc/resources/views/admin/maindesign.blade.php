@extends('admin.maindesign')

@section('add_category')

@if(session('category_message'))
    <div class="alert alert-success alert-dismissible fade show mt-3 mx-auto" style="max-width: 600px;" role="alert">
        {{ session('category_message') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid mt-5">
    <div class="card mx-auto border-0 shadow-lg" style="
        max-width: 600px;
        background: linear-gradient(180deg, #161b22, #0d1117);
        border: 1px solid #21262d;
        border-radius: 14px;
    ">
        <div class="card-body text-light px-4 py-5">
            <h4 class="text-center mb-4 fw-semibold" style="color: #58a6ff;">
                <i class="fa-solid fa-folder-plus me-2"></i> Add New Category
            </h4>

            <form action="{{ route('admin.postaddcategory') }}" method="POST" class="d-flex align-items-center gap-2 justify-content-center">
                @csrf
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-transparent text-primary border-secondary">
                        <i class="fa-solid fa-tag"></i>
                    </span>
                    <input
                        type="text"
                        name="category"
                        class="form-control bg-transparent text-light border-secondary"
                        placeholder="Enter category name..."
                        style="box-shadow: none;"
                        required>
                </div>
                <button type="submit" class="btn fw-semibold text-light px-4"
                    style="background: linear-gradient(90deg, #1f6feb, #238636); border: none;">
                    <i class="fa-solid fa-plus-circle me-1"></i> Add
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
