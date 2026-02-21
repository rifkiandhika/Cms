@extends('layouts.app')
@section('title', 'Edit Customer')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('customers.index') }}">Customer</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-row gap-1 day-sorting">
        <button class="btn btn-sm btn-primary">Today</button>
        <button class="btn btn-sm">7d</button>
        <button class="btn btn-sm">2w</button>
        <button class="btn btn-sm">1m</button>
        <button class="btn btn-sm">3m</button>
        <button class="btn btn-sm">6m</button>
        <button class="btn btn-sm">1y</button>
    </div>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Edit Customer</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Edit Customer</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('customer.form')

                    <div class="text-end mt-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection