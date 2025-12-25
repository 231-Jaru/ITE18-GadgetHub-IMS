@extends('layouts.app')

@section('title', 'Server Error - Gadgethub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-server fa-5x text-danger"></i>
                    </div>
                    <h1 class="display-4 fw-bold text-danger">500</h1>
                    <h2 class="h4 mb-3">Server Error</h2>
                    <p class="text-muted mb-4">
                        Something went wrong on our end. We're working to fix it.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i>Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
