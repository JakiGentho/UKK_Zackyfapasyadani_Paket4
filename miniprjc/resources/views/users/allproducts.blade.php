@extends('users.maindesign')

@section('all_products')
    <div class="container">
      <div class="row">
        @foreach($products as $product)
        <div class="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="box">
            <a href="{{ route('product_details', $product->id) }}">
              <div class="img-box">
                <img src="{{ asset('products/'.$product->product_image) }}" alt="">
              </div>
              <div class="detail-box">
                <h6>
                  {{ $product->product_title }}
                </h6>
                <h6>
                  Price
                  <span>
                    ${{ $product->product_prices }}
                  </span>
                </h6>
              </div>
              <div class="new">
                <span>
                  New
                </span>
              </div>
            </a>
          </div>
        </div>
        @endforeach
      </div>
      <div class="btn-box" data-aos="zoom-in" data-aos-delay="50">
        <a href="{{route('index')}}">
          View All Products
        </a>
      </div>
    </div>
@endsection

