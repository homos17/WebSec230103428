@extends('layouts.master')
@section('title', 'Invoice')
@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">Purchase Invoice</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price (per unit)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->product->name }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>${{ number_format($order->product->price, 2) }}</td>
                    <td>${{ number_format($order->total_price, 2) }}</td>
                </tr>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                    <td><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <a href="{{ route('products_list') }}" class="btn btn-primary">OK</a>
    </div>
@endsection
