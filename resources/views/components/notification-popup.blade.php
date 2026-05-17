{{-- Toast Notification Container --}}
<div id="toastContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 max-w-md w-full pointer-events-none">
    {{-- Toasts will be injected here by JS --}}
</div>

{{-- Critical Alert Modal --}}
<div id="criticalAlertModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeCriticalModal()"></div>
    <div class="bg-surface rounded-2xl shadow-2xl border border-error w-full max-w-lg relative z-10 overflow-hidden animate-bounce-in">
        {{-- Red Header --}}
        <div class="bg-primary text-on-primary p-6 text-center">
            <span class="material-symbols-outlined text-5xl mb-2" style="font-variation-settings: 'FILL' 1;">crisis_alert</span>
            <h2 class="text-2xl font-display font-extrabold" id="criticalTitle">PERINGATAN DARURAT</h2>
            <p class="text-sm font-sans opacity-90 mt-1" id="criticalSubtitle">Bencana kritis terdeteksi di sekitar Anda</p>
        </div>
        {{-- Body --}}
        <div class="p-6">
            <div id="criticalBody" class="font-sans text-on-surface-variant space-y-3">
                {{-- Dynamic content --}}
            </div>
        </div>
        {{-- Actions --}}
        <div class="p-4 border-t border-outline-variant bg-surface-container flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-primary font-sans font-bold text-sm hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                Buka Dashboard
            </a>
            <button onclick="closeCriticalModal()" class="bg-primary text-on-primary font-sans font-bold px-6 py-2.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                Saya Mengerti
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-in {
        0% { transform: scale(0.85); opacity: 0; }
        60% { transform: scale(1.03); opacity: 1; }
        100% { transform: scale(1); }
    }
    .animate-bounce-in { animation: bounce-in 0.4s ease-out; }
    @keyframes slide-in-right {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in { animation: slide-in-right 0.3s ease-out; }
    @keyframes slide-out-right {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .animate-slide-out { animation: slide-out-right 0.3s ease-in forwards; }
</style>

<script>
(function() {
    const POLL_INTERVAL = 60000; // 60 seconds
    const SHOWN_KEY = 'nusaalert_shown_alerts';

    function getShownAlerts() {
        try { return JSON.parse(localStorage.getItem(SHOWN_KEY) || '{}'); }
        catch { return {}; }
    }

    function markAlertShown(id) {
        const shown = getShownAlerts();
        shown[id] = Date.now();
        // Keep only last 50 entries
        const keys = Object.keys(shown);
        if (keys.length > 50) {
            keys.sort((a, b) => shown[a] - shown[b]);
            keys.slice(0, keys.length - 50).forEach(k => delete shown[k]);
        }
        localStorage.setItem(SHOWN_KEY, JSON.stringify(shown));
    }

    function isAlertShown(id) {
        const shown = getShownAlerts();
        if (!shown[id]) return false;
        // Shown within last 6 hours counts as "already shown"
        return (Date.now() - shown[id]) < 6 * 60 * 60 * 1000;
    }

    function createToast(alert) {
        const container = document.getElementById('toastContainer');
        const severityColors = {
            'awas': 'border-l-4 border-l-primary bg-error-container',
            'siaga': 'border-l-4 border-l-amber-500 bg-amber-50',
            'waspada': 'border-l-4 border-l-yellow-400 bg-yellow-50',
        };
        const toastClass = severityColors[alert.severity] || severityColors['waspada'];

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto ${toastClass} rounded-xl shadow-lg p-4 flex items-start gap-3 animate-slide-in cursor-pointer`;
        toast.innerHTML = `
            <span class="material-symbols-outlined text-2xl mt-0.5 ${alert.severity === 'awas' ? 'text-primary' : 'text-amber-600'}" style="font-variation-settings: 'FILL' 1;">warning</span>
            <div class="flex-1 min-w-0">
                <h4 class="font-display font-bold text-on-surface text-sm">${alert.jenis.toUpperCase()} ${alert.magnitude ? 'M' + alert.magnitude : ''}</h4>
                <p class="font-sans text-xs text-on-surface-variant truncate">${alert.wilayah}</p>
                <p class="font-sans text-xs text-on-surface-variant">${alert.jarak_km} km dari ${alert.lokasi_nama}</p>
            </div>
            <button onclick="event.stopPropagation(); this.closest('[class*=pointer-events-auto]').classList.add('animate-slide-out'); setTimeout(() => this.closest('[class*=pointer-events-auto]')?.remove(), 300);" class="text-on-surface-variant hover:text-on-surface p-1">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        `;
        toast.addEventListener('click', function() { window.location.href = '/dashboard'; });
        container.appendChild(toast);

        // Auto remove after 8 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('animate-slide-out');
                setTimeout(() => toast.remove(), 300);
            }
        }, 8000);
    }

    function showCriticalModal(alert) {
        const modal = document.getElementById('criticalAlertModal');
        const body = document.getElementById('criticalBody');

        document.getElementById('criticalTitle').textContent = `PERINGATAN: ${alert.jenis.toUpperCase()} ${alert.magnitude ? 'M' + alert.magnitude : ''}`;
        document.getElementById('criticalSubtitle').textContent = alert.wilayah;

        body.innerHTML = `
            <div class="bg-surface-container p-4 rounded-lg border border-outline-variant">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><strong class="text-on-surface">Jenis:</strong> ${alert.jenis.toUpperCase()}</div>
                    ${alert.magnitude ? `<div><strong class="text-on-surface">Magnitudo:</strong> ${alert.magnitude}</div>` : ''}
                    <div><strong class="text-on-surface">Jarak:</strong> ${alert.jarak_km} km</div>
                    <div><strong class="text-on-surface">Dari:</strong> ${alert.lokasi_nama}</div>
                </div>
            </div>
            <p class="text-sm text-on-surface font-bold flex items-start gap-2">
                <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings: 'FILL' 1;">emergency</span>
                Segera ikuti prosedur keselamatan! Jauhi area berbahaya dan pantau informasi resmi.
            </p>
        `;

        modal.classList.remove('hidden');
    }

    window.closeCriticalModal = function() {
        document.getElementById('criticalAlertModal').classList.add('hidden');
    };

    async function pollProximity() {
        try {
            const response = await fetch('/api/user/check-proximity', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) return;

            const data = await response.json();

            if (data.alerts && data.alerts.length > 0) {
                let shownCritical = false;

                data.alerts.forEach(alert => {
                    if (isAlertShown(alert.id)) return;

                    markAlertShown(alert.id);

                    // Show full-screen modal for critical (M6+ or tsunami)
                    if (alert.severity === 'awas' && !shownCritical) {
                        showCriticalModal(alert);
                        shownCritical = true;
                    } else {
                        createToast(alert);
                    }
                });
            }
        } catch (err) {
            // Silently fail - don't disrupt the user
        }
    }

    // Only poll for authenticated users (check if the proximity endpoint exists)
    @auth
        // Initial check after short delay
        setTimeout(pollProximity, 3000);
        // Then poll periodically
        setInterval(pollProximity, POLL_INTERVAL);
    @endauth
})();
</script>
