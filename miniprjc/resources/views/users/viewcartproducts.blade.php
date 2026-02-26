@extends('maindesign')

@section('viewcart_products')

<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <table id="cartTable" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
        <thead>
            <tr style="background-color: #f2f2f2; color: #000;">
                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Product Title</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ddd;">Product Price</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ddd;">Product Image</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ddd;">Quantity</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #ddd;">Action</th>
            </tr>
        </thead>

        <tbody>
            @php $totalPrice = 0; @endphp

            @foreach($cart->sortBy('id') as $cart_product)
                <tr style="border-bottom: 1px solid #444; color: #2b2b2b; background-color: #fff;" data-original-price="{{ $cart_product->product->product_prices }}">
                    <td style="padding: 12px; vertical-align: top;">{{ $cart_product->product->product_title }}</td>
                    <td style="padding: 12px; text-align: center; vertical-align: top;" class="unit-price">{{ $cart_product->product->product_prices }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <img style="width:150px; border-radius: 8px;" src="{{ asset('products/'.$cart_product->product->product_image) }}" alt="product image">
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <div style="display:flex; justify-content:center; align-items:center;">
                            <button type="button" class="qty-btn" style="padding:2px 8px;">-</button>
                            <input type="number" class="quantity" value="1" min="1" style="width:50px; text-align:center; margin:0 5px; -moz-appearance: textfield; appearance: textfield;" onkeydown="return false;">
                            <button type="button" class="qty-btn" style="padding:2px 8px;">+</button>
                        </div>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a style="padding: 8px 14px; background-color: red; color: white; text-decoration: none; border-radius: 5px;"
                           href="{{ route('removecartproducts', $cart_product->id) }}">
                            Remove
                        </a>
                    </td>
                </tr>

                @php $totalPrice += $cart_product->product->product_prices; @endphp
            @endforeach

            {{-- Baris Total Price --}}
            <tr style="background-color: #8c8c8c; color: #000; font-weight: bold;">
                <td style="padding: 12px; text-align: left;">Total Price</td>
                <td colspan="4" style="padding: 12px; text-align: center;">$<span id="cartTotal">{{ $totalPrice }}</span></td>
            </tr>
        </tbody>
    </table>

    @if (session('confirm_order'))
        <div style="border: 1px solid blue; color: white; border-radius: 4px; padding: 10px; background-color: blue; margin-bottom: 10px;">
            {{session('confirm_order')}}
        </div>
    @endif

    <form action="{{route('confirm_order')}}" method="post" style="margin-top: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
    @csrf
    <h2 style="text-align:center; margin-bottom:20px; color:#333;">Shipping Details</h2>

    <div style="margin-bottom:20px;">
        <label for="receiver_address" style="display:block; margin-bottom:5px; font-weight:600; color:#555;">Your Address</label>
        <input type="text" name="receiver_address" id="receiver_address" placeholder="Enter Your Address" required
               style="width:100%; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:16px; outline:none; transition: 0.3s;">
    </div>

    <div style="margin-bottom:20px;">
        <label for="receiver_phone" style="display:block; margin-bottom:5px; font-weight:600; color:#555;">Phone Number</label>
        <input type="number" name="receiver_phone" id="receiver_phone" placeholder="Enter Your Phone Number" required
               style="width:100%; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:16px; outline:none; transition: 0.3s;">
    </div>

    <div style="text-align:center;">
        <input type="submit" value="Confirm Order" class="btn-confirm-order"
               style="background-color:#007bff; color:white; padding:12px 25px; font-size:16px; border:none; border-radius:6px; cursor:pointer; transition:0.3s;">
    </div>
</form>

<style>
    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0,123,255,0.3);
    }

    .btn-confirm-order:hover {
        background-color: #0056b3;
    }
</style>

</div>

<script>
function updateRowTotal(row) {
    const unitPriceOriginal = parseInt(row.dataset.originalPrice);
    const quantity = parseInt(row.querySelector('.quantity').value);
    const totalPrice = unitPriceOriginal * quantity;
    row.querySelector('.unit-price').textContent = totalPrice;
}

function updateCartTotal() {
    const rows = document.querySelectorAll('#cartTable tbody tr');
    let total = 0;
    rows.forEach(row => {
        const priceCell = row.querySelector('.unit-price');
        if(priceCell) total += parseInt(priceCell.textContent);
    });
    document.getElementById('cartTotal').textContent = total;
}

document.querySelectorAll('#cartTable tbody tr').forEach(row => {
    const minusBtn = row.querySelector('.qty-btn:first-child');
    const plusBtn = row.querySelector('.qty-btn:last-child');
    const qtyInput = row.querySelector('.quantity');

    minusBtn.addEventListener('click', () => {
        if(qtyInput.value > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
        updateRowTotal(row);
        updateCartTotal();
    });
    plusBtn.addEventListener('click', () => {
        qtyInput.value = parseInt(qtyInput.value) + 1;
        updateRowTotal(row);
        updateCartTotal();
    });

    qtyInput.addEventListener('input', () => {
        if(qtyInput.value < 1) qtyInput.value = 1;
        updateRowTotal(row);
        updateCartTotal();
    });

    // Inisialisasi harga tiap baris
    updateRowTotal(row);
});

// Inisialisasi total cart
updateCartTotal();
</script>

@endsection
