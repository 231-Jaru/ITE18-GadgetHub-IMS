<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-store"></i> Gadgethub
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @if(session('api_token') || session('user_id'))
                <!-- Navigation for authenticated admins -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gadgets.*') ? 'active' : '' }}" href="{{ route('gadgets.index') }}">
                        <i class="fas fa-mobile-alt"></i> Gadgets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}" href="{{ route('stocks.index') }}">
                        <i class="fas fa-warehouse"></i> Stocks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                        <i class="fas fa-shopping-cart"></i> Purchase Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                        <i class="fas fa-truck"></i> Suppliers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ session('user_name', 'User') }}
                        <small class="text-muted">({{ ucfirst(session('user_type', 'user')) }})</small>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle fa-2x me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ session('user_name', 'User') }}</div>
                                    <small class="text-muted">{{ ucfirst(session('user_type', 'user')) }}</small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Admin dropdown items -->
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('gadgets.index') }}">
                            <i class="fas fa-mobile-alt me-2"></i> Gadgets
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('stocks.index') }}">
                            <i class="fas fa-warehouse me-2"></i> Stocks
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('purchase-orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Purchase Orders
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('suppliers.index') }}">
                            <i class="fas fa-truck me-2"></i> Suppliers
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar me-2"></i> Reports
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-item-text px-3 py-2">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Last active: {{ session('last_activity') ? \Carbon\Carbon::parse(session('last_activity'))->diffForHumans() : 'Just now' }}
                            </small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Global navigation fix - ensures all navigation links work properly and go to WEB routes, not API routes
    (function() {
        // Track which links we've already fixed to avoid duplicates
        const fixedLinks = new WeakSet();
        
        // Function to ensure URL is a web route, not API route
        function ensureWebRoute(url) {
            if (!url) return url;
            // Remove /api prefix if present
            if (url.startsWith('/api/')) {
                return url.replace('/api/', '/');
            }
            // If it's a full URL with /api/, fix it
            try {
                const urlObj = new URL(url, window.location.origin);
                if (urlObj.pathname.startsWith('/api/')) {
                    urlObj.pathname = urlObj.pathname.replace('/api/', '/');
                    return urlObj.pathname + urlObj.search + urlObj.hash;
                }
            } catch(e) {
                // If URL parsing fails, just return as is
            }
            return url;
        }
        
        function fixNavLinks() {
            // Fix navbar navigation links (not dropdown toggles)
            document.querySelectorAll('.navbar-nav a.nav-link[href]').forEach(function(link) {
                if (fixedLinks.has(link)) return;
                
                let href = link.getAttribute('href');
                // Only fix actual navigation links (not dropdown toggles or anchors)
                if (href && href !== '#' && href !== 'javascript:void(0)' && !href.startsWith('#') && !link.hasAttribute('data-bs-toggle')) {
                    // Ensure it's a web route, not API route
                    href = ensureWebRoute(href);
                    if (href !== link.getAttribute('href')) {
                        link.setAttribute('href', href);
                    }
                    
                    fixedLinks.add(link);
                    
                    // Add navigation handler that runs first
                    link.addEventListener('click', function(e) {
                        let targetHref = this.getAttribute('href');
                        const originalHref = targetHref;
                        // Ensure it's a web route
                        targetHref = ensureWebRoute(targetHref);
                        
                        // Only intercept if the route was changed (was an API route) or if it's a valid web route
                        if (targetHref && targetHref !== '#' && targetHref !== 'javascript:void(0)' && !targetHref.startsWith('/api/')) {
                            // If the href was changed (was an API route), we need to navigate manually
                            if (originalHref !== targetHref) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                window.location.href = targetHref;
                                return false;
                            }
                            // Otherwise, let the browser handle it naturally (for valid web routes like /home)
                        }
                    }, true); // Capture phase - runs before other handlers
                }
            });
            
            // Fix dropdown menu links
            document.querySelectorAll('.dropdown-menu a.dropdown-item[href]').forEach(function(link) {
                if (fixedLinks.has(link)) return;
                
                let href = link.getAttribute('href');
                if (href && href !== '#' && href !== 'javascript:void(0)' && !href.startsWith('#')) {
                    // Ensure it's a web route, not API route
                    href = ensureWebRoute(href);
                    if (href !== link.getAttribute('href')) {
                        link.setAttribute('href', href);
                    }
                    
                    fixedLinks.add(link);
                    
                    // Add navigation handler
                    link.addEventListener('click', function(e) {
                        let targetHref = this.getAttribute('href');
                        const originalHref = targetHref;
                        // Ensure it's a web route
                        targetHref = ensureWebRoute(targetHref);
                        
                        // Only intercept if the route was changed (was an API route) or if it's a valid web route
                        if (targetHref && targetHref !== '#' && targetHref !== 'javascript:void(0)' && !targetHref.startsWith('/api/')) {
                            // If the href was changed (was an API route), we need to navigate manually
                            if (originalHref !== targetHref) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                window.location.href = targetHref;
                                return false;
                            }
                            // Otherwise, let the browser handle it naturally (for valid web routes like /home)
                        }
                    }, true);
                }
            });
        }
        
        // Run immediately if DOM is ready, otherwise wait
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fixNavLinks);
        } else {
            fixNavLinks();
        }
        
        // Also run after Bootstrap loads to catch any late initialization
        if (typeof bootstrap !== 'undefined') {
            setTimeout(fixNavLinks, 200);
        } else {
            window.addEventListener('load', function() {
                setTimeout(fixNavLinks, 200);
            });
        }
    })();
</script>
<script src="{{ asset('js/button-fixes.js') }}"></script>
