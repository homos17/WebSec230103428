@extends('layouts.master')

@section('title', "Product")

@section("content")
<div class="row">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    @can('add_product')
        <div class="col col-2">
            <a href="{{route('products_edit')}}"
                class="btn btn-success form-control">Add Product</a>
        </div>
    @endcan
</div>
<div class="card my-2">
    <div class="card-body">
        Dear <span id='name'>{{ auth()->user()->name }}</span>, your balance is <span id='balance'>{{ auth()->user()->balance }}</span>
    </div>
</div>
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text"  class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="numeric"  class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="numeric"  class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" {{ request()->order_by==""?"selected":"" }} disabled>Order By</option>
                <option value="name" {{ request()->order_by=="name"?"selected":"" }}>Name</option>
                <option value="price" {{ request()->order_by=="price"?"selected":"" }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction==""?"selected":"" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction=="ASC"?"selected":"" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction=="DESC"?"selected":"" }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>

<div class="card mt-2">
    <div class="card-body">
        View search result of keywords: <span>{!!request()->keywords!!}</span>
    </div>
</div>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const keywords = urlParams.get('keywords');
    document.querySelector('span').innerHTML = keywords;
</script>

    <!-- Display Products -->
    @foreach($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <img src="{{ asset("images/$product->photo") }}" class="img-thumbnail" alt="{{ $product->name }}" width="100%">
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <h3>{{ $product->name }}</h3>
                    <div class="row mb-2">
                        @can('edit_product')
                        <div class="col col-2">
                            <a href="{{route('products_edit', $product->id)}}"
                        class="btn btn-success form-control">Edit</a>
                        </div>
                        @endcan
                        @can('review')
                        <div class="col col-2">
                            <a href="{{route('products.review', $product->id)}}"
                        class="btn btn-success form-control">review</a>
                        </div>
                        @endcan
                        @can('edit_product')
                        <div class="col col-2">
                            <a href="{{route('products_edit', $product->id)}}"
                        class="btn btn-success form-control">Edit</a>
                        </div>
                        @endcan

                        @can('delet_product')
                        <div class="col col-2">
                            <a href="{{route('products_delete', $product->id)}}"
                    class="btn btn-danger form-control">Delete</a>
                        </div>
                        @endcan
                        <div class="col col-4">
                            @if($product->stock > 0)
                                <form action="{{ route('products.buy', $product->id) }}" method="POST">
                                    @csrf
                                    <input type="number" name="quantity" class="form-control mb-1" min="1" value="1" required>
                                    <button type="submit" class="btn btn-primary form-control">Buy</button>
                                </form>
                            @else
                                <button class="btn btn-secondary form-control" disabled>Out of Stock</button>
                            @endif
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tr><th width="20%">Name</th><td>{{ $product->name }}</td></tr>
                        <tr><th>Model</th><td>{{ $product->model }}</td></tr>
                        <tr><th>Code</th><td>{{ $product->code }}</td></tr>
                        <tr><th>Price</th><td>{{ $product->price }}$</td></tr>
                        <tr><th>stock</th><td>{{ $product->stock }}</td></tr>
                        <tr><th>Description</th><td>{{ $product->description }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
