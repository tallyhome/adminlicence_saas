<footer class="bg-light py-3 mt-auto">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
            <div>
                <a href="{{ route('admin.version') }}" class="text-decoration-none">
                    <i class="fas fa-info-circle me-1"></i> Version 5.0.1
                </a>
            </div>
        </div>
    </div>
</footer>