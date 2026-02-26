@extends('users.maindesign')

@section('dashboard')
<div class="container-fluid text-center py-5">
    <h3 class="text-light">Halo, {{ Auth::user()->name }} 👋</h3>
    <p class="text-muted">Selamat datang di halaman akunmu.</p>

    <div class="d-flex justify-content-center my-3">
        <a href="#" class="btn btn-danger mx-2">
            <i class="fa fa-home"></i> Home
        </a>
        <a href="{{ route('myorders') }}" class="btn btn-outline-light mx-2">
            <i class="fa fa-shopping-bag"></i> My Order
        </a>
    </div>

    <div class="card bg-dark text-light mx-auto mt-4" style="max-width: 400px;">
        <div class="card-body">
            <img src="{{ asset('images/profile.png') }}" alt="Profile" width="80" class="mb-3 rounded-circle">

            <h5 class="fw-bold">{{ Auth::user()->name }}</h5>
            <p class="text-secondary mb-4">{{ Auth::user()->email }}</p>

            <p>Kamu dapat melihat pesananmu di menu <strong>My Order</strong>.</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger mt-3">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
