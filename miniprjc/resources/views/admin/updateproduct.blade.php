@extends('admin.maindesign')

@section('update_product')

@if(session('product_message'))
<div style="border: 1px solid blue; color:white; border-radius:4px; padding: 10px; background-color: green; margin-bottom: 10px;">
    {{ session('product_message') }}
</div>
@endif

<div class="container-fluid" style="margin-left: 400px;">
    <form action="{{ route('admin.updateproduct', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="text" name="product_title" value="{{ $product->product_title }}"> <br><br>

        <textarea name="product_description" style="width: 300px; height: 200px;">{{ $product->product_description }}</textarea> <br><br>

        <input type="number" name="product_quantity" value="{{ $product->product_quantity }}"> <br><br>

        <input type="number" name="product_price" value="{{ $product->product_price }}"> <br><br>

        <img style="width: 100px;" src="{{ asset('products/'.$product->product_image) }}">
        <input type="file" name="product_image"> <label>Add Image here!</label> <br><br>

        <select name="product_category">
            <option value="{{ $product->product_category }}">{{ $product->product_category }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->category }}">{{ $category->category }}</option>
            @endforeach
        </select> <label>Select A Category</label> <br><br>

        <input type="submit" name="submit" value="Update Product"> <br><br>
    </form>
</div>

@endsection
