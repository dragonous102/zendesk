@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <form action="{{ route('admin.update') }}" method="POST" class="w-50">
            @csrf
            <div class="mb-3">
                <input type="email" name="email" class="form-control form-control-sm rounded" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="card_number" class="form-control form-control-sm rounded" placeholder="Card Number" required>
            </div>
            <div class="mb-3">
                <input type="text" name="account_number" class="form-control form-control-sm rounded" placeholder="Account Number" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill">Update Profile</button>
        </form>
    </div>
@endsection
