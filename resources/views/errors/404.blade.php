@extends('layouts.app')

@section('title', 'Page Not Found - Gadgethub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
                    </div>
                    <h1 class="display-4 fw-bold text-primary">404</h1>
                    <h2 class="h4 mb-3">Page Not Found</h2>
                    <p class="text-muted mb-4">
                        The page you're looking for doesn't exist or has been moved.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
