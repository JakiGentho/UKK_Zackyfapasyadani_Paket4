@extends('admin.maindesign')

@section('view_orders')

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background-color: #121212;
        color: #f1f1f1;
    }
    .table-dark th {
        background-color: #1f1f1f;
        color: #f1f1f1;
    }
    .table-dark td {
        background-color: #1a1a1a;
        color: #f1f1f1;
    }
    .table-hover tbody tr:hover {
        background-color: #2a2a2a;
    }
</style>

<div class="container my-4">
    <h2 class="mb-4 text-light">Daftar Pesanan</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center table-dark">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Buyer Name</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Price ($)</th>
                    <th>Quantity</th>
                    <th>Total Price ($)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($orders as $order)
                <tr id="order-{{ $order->id }}">
                    <td>{{ $no++ }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td>
                        <img src="{{ asset('products/' . $order->product->product_image) }}" alt="{{ $order->product->product_title }}" class="img-fluid" style="max-width: 80px; max-height: 80px;">
                    </td>
                    <td>{{ $order->product->product_title }}</td>
                    <td>${{ number_format($order->product->product_prices, 2) }}</td>
                    <td>{{ $order->quantity ?? 0 }}</td>
                    <td>${{ number_format(($order->quantity ?? 0) * $order->product->product_prices, 2) }}</td>
                    <td>
                        <button
                            data-url="{{ route('admin.change_status', ['id' => $order->id]) }}"
                            class="btn btn-sm {{ $order->status == 'Delivered' ? 'btn-success' : 'btn-warning' }} status-btn"
                            onclick="changeStatus(this)">
                            {{ $order->status ?? 'Pending' }}
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- jQuery (untuk AJAX) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
function changeStatus(btn) {
    let currentStatus = btn.innerText === 'Pending' ? 'Delivered' : 'Pending';
    let url = btn.dataset.url;

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: currentStatus
        },
        success: function(response) {
            btn.innerText = currentStatus;
            if(currentStatus === 'Delivered') {
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-success');
            } else {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-warning');
            }
        },
        error: function(err) {
            alert('Failed to update status!');
            console.log(err);
        }
    });
}
</script>

@endsection
