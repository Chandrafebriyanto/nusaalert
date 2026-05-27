<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'NusaAlert') — Sistem Peringatan Dini Bencana</title>
    <meta name="description" content="@yield('meta_description', 'NusaAlert - Sistem Peringatan Dini Bencana Alam Personal. Dapatkan notifikasi real-time dari BMKG langsung di perangkat Anda.')" />
    <meta name="theme-color" content="#d32f2f" />
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=swap" rel="stylesheet" />

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-sans antialiased min-h-screen flex flex-col">

    @yield('body')

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);

                    @auth
                    // Request push notification permission and subscribe
                    if ('Notification' in window && 'PushManager' in window) {
                        initPushNotifications(registration);
                    }
                    @endauth
                }, err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        async function initPushNotifications(registration) {
            // Check if already subscribed
            const existingSub = await registration.pushManager.getSubscription();
            if (existingSub) {
                console.log('Already subscribed to push notifications');
                return;
            }

            // Request notification permission
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.log('Notification permission denied');
                return;
            }

            try {
                // VAPID public key from server
                const vapidPublicKey = '{{ env("VAPID_PUBLIC_KEY") }}';
                const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey
                });

                // Send subscription to server
                const response = await fetch('/push-subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(subscription)
                });

                if (response.ok) {
                    console.log('Push notification subscription successful');
                } else {
                    console.error('Push subscription server error:', response.status);
                }
            } catch (error) {
                console.error('Failed to subscribe to push notifications:', error);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
</body>
</html>
