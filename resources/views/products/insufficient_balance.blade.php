@extends('layouts.master')
@section('title', 'Insufficient Balance')
@section('content')
<div class="container mt-4 text-center">
    <h2 class="text-danger">Insufficient Balance</h2>
    <p class="lead">You do not have enough balance to complete this purchase.</p>
    <a href="{{ route('products_list') }}" class="btn btn-warning">Go Back</a>
</div>
@endsection
