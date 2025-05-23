@extends('layouts.master')
@section('title', 'review product')
@section('content')
<div class="container">
    <h2>Add Review for {{ $product->name }}</h2>

    <form method="POST" action="{{ route('products.review.save', $product->id) }}">
        @csrf
        <div class="form-group">
            <label for="review">Your Review</label>
            <textarea name="review" class="form-control" rows="5" required>{{ old('review', $product->review ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3">Submit</button>
    </form>
</div>
@endsection
