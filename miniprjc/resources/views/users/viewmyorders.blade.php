
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-left">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">No</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Customer</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Product</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Quantity</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Price</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Total</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Receiver Address</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Receiver Phone</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Product Image</th>
                                    <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800">
                                @php $grandTotal = 0; @endphp
                                @foreach($orders as $index => $order)
                                    @php
                                        $price = $order->product->price ?? 0;
                                        $quantity = $order->quantity ?? 1;
                                        $total = $price * $quantity;
                                        $grandTotal += $total;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $order->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $order->product->name ?? 'Deleted Product' }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $quantity }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ number_format($price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ number_format($total, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $order->receiver_address }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $order->receiver_phone }}</td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                            @if(!empty($order->product->image))
                                                <img src="{{ asset('products/'.$order->product->image) }}" alt="Product Image" class="w-24 h-auto rounded">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ ucfirst($order->status) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Baris Grand Total --}}
                                <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                    <td colspan="5" class="px-4 py-2 text-right border-b border-gray-200 dark:border-gray-600">Grand Total</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                                    <td colspan="4" class="border-b border-gray-200 dark:border-gray-600"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-4">{{ __("You're logged in!") }}</p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
