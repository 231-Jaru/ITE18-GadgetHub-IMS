<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deleted Gadgets - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .deleted-gadget-card {
            border-left: 4px solid #dc3545;
            transition: all 0.3s ease;
        }
        .deleted-gadget-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="fas fa-trash-restore text-danger me-2"></i>Deleted Gadgets
            </h1>
            <a href="/gadgets" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Gadgets
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($deletedGadgets->count() > 0)
            <div class="row">
                @foreach($deletedGadgets as $gadget)
                <div class="col-md-4 mb-3">
                    <div class="card deleted-gadget-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-mobile-alt me-2"></i>{{ $gadget->GadgetName }}
                            </h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $gadget->category->CategoryName ?? 'No Category' }}<br>
                                    <i class="fas fa-star me-1"></i>{{ $gadget->brand->BrandName ?? 'No Brand' }}
                                </small>
                            </p>
                            <p class="text-muted mb-2">
                                <small>
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Deleted: {{ $gadget->deleted_at ? $gadget->deleted_at->format('M d, Y h:i A') : 'Unknown' }}
                                </small>
                            </p>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('gadgets.restore', $gadget->GadgetID) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-undo me-1"></i>Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('gadgets.permanent-delete', $gadget->GadgetID) }}" 
                                      class="d-inline delete-form" 
                                      data-item-name="{{ $gadget->GadgetName }}"
                                      onsubmit="return confirm('Are you sure you want to PERMANENTLY delete \"{{ $gadget->GadgetName }}\"? This action cannot be undone and the gadget will be removed from the database forever.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt me-1"></i>Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4>No Deleted Gadgets</h4>
                <p class="text-muted">There are no deleted gadgets to restore.</p>
                <a href="/gadgets" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Gadgets
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        // Add loading state to restore buttons
        document.querySelectorAll('form[action*="restore"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Restoring...';
                }
            });
        });
    </script>
</body>
</html>

