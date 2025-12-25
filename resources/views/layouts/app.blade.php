<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gadgethub - Inventory Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .main-content {
            min-height: calc(100vh - 56px);
            padding-top: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .alert {
            border: none;
            border-radius: 8px;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    @include('partials.navbar')
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Loading Overlay -->
    @include('partials.loading')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Global JavaScript -->
    <script>
        // CSRF Token setup for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Global loading functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
        
        // Auto-hide loading after 5 seconds as fallback
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                hideLoading();
            }, 5000);
        });
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
        
        // Fix navigation links in navbar to ensure they work properly and go to WEB routes, not API routes
        (function() {
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
            
            function fixNavigationLinks() {
                // Fix navbar navigation links
                document.querySelectorAll('.navbar-nav a.nav-link[href]').forEach(function(link) {
                    if (fixedLinks.has(link)) return;
                    
                    let href = link.getAttribute('href');
                    if (href && href !== '#' && href !== 'javascript:void(0)' && !href.startsWith('#') && !link.hasAttribute('data-bs-toggle')) {
                        // Ensure it's a web route, not API route
                        href = ensureWebRoute(href);
                        if (href !== link.getAttribute('href')) {
                            link.setAttribute('href', href);
                        }
                        
                        fixedLinks.add(link);
                        link.addEventListener('click', function(e) {
                            let targetHref = this.getAttribute('href');
                            targetHref = ensureWebRoute(targetHref);
                            if (targetHref && targetHref !== '#' && targetHref !== 'javascript:void(0)' && !targetHref.startsWith('/api/')) {
                                window.location.href = targetHref;
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return false;
                            }
                        }, true);
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
                        link.addEventListener('click', function(e) {
                            let targetHref = this.getAttribute('href');
                            targetHref = ensureWebRoute(targetHref);
                            if (targetHref && targetHref !== '#' && targetHref !== 'javascript:void(0)' && !targetHref.startsWith('/api/')) {
                                window.location.href = targetHref;
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return false;
                            }
                        }, true);
                    }
                });
            }
            
            // Run when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fixNavigationLinks);
            } else {
                fixNavigationLinks();
            }
            
            // Also run after Bootstrap loads
            if (typeof bootstrap !== 'undefined') {
                setTimeout(fixNavigationLinks, 200);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(fixNavigationLinks, 200);
                });
            }
        })();
    </script>
    
    @yield('scripts')
</body>
</html>
