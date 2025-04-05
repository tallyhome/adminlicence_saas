<footer class="bg-light py-3 mt-auto">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
            <div>
                <a href="{{ route('admin.version') }}" class="text-decoration-none">
                    Version {{ config('version.full')() }}
                </a>
            </div>
        </div>
    </div>
</footer>