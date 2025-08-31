<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('alerta', event => {
        const darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: event.detail.type,
            title: event.detail.message,
            showConfirmButton: false,
            timer: 2000,
            background: darkMode ? '#000000' : '#FFFFFF', 
            color: darkMode ? '#F9FAFB' : '#111827',  
            iconColor: event.detail.type === 'success' ? '#10B981' :
                       event.detail.type === 'error' ? '#EF4444' :
                       event.detail.type === 'warning' ? '#F59E0B' : '#3B82F6',
            customClass: {
                popup: 'shadow-lg rounded-xl'
            }
        });
    });
</script>

