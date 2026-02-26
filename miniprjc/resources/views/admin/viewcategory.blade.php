@extends('admin.maindesign')

@section('view_category')

@if (session('deletecategory_message'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('deletecategory_message') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid mt-4">
    <div class="card bg-dark border-0 shadow-sm">
        <div class="card-body">
            <h4 class="card-title text-light mb-4 text-center">View Categories</h4>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle text-center">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th scope="col" style="width: 15%;">ID</th>
                            <th scope="col" style="width: 55%;">Category Name</th>
                            <th scope="col" style="width: 30%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories->sortBy('id') as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $category->category }}</td>
                                <td>
                                    <a href="{{ route('admin.categoryupdate', $category->id) }}" class="btn btn-success btn-sm me-2">
                                        <i class="bi bi-pencil-square me-1"></i> Update
                                    </a>
                                    <a href="{{ route('admin.categorydelete', $category->id) }}"
                                       onclick="return confirm('Are you sure you want to delete this category?')"
                                       class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash3-fill me-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection
