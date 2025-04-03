@extends('layouts.master')
@section('title', 'Update Balance')
@section('content')
<div class="container mt-5">
    <h2>Update balance for Customers</h2>
    <div class="form-group">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <strong>Error!</strong> {{$error}}
            </div>
        @endforeach
    </div>
    <form action="{{ route('updateBalance',$user->id) }}" method="POST">
        {{ csrf_field() }}
        <div class="mb-3">
            <label for="name" class="form-label">New balance:</label>
            <input type="number" step="0.01" name="balance" class="form-control" value="{{ $user->balance }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

