const CACHE_NAME = 'nusaalert-cache-v1';
const urlsToCache = [
    '/',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event for offline support (basic caching)
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
    );
});

// Push Notification Event Listener
self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    let data = {};
    if (event.data) {
        data = event.data.json();
    }

    console.log('Push notification received', data);

    const title = data.title || "Peringatan NusaAlert";
    const options = {
        body: data.body || "Ada peringatan sistem baru.",
        icon: data.icon || '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        tag: data.tag || 'nusaalert-' + Date.now(),
        renotify: true,
        requireInteraction: data.requireInteraction || false,
        vibrate: [200, 100, 200, 100, 300],
        actions: [
            { action: 'open', title: 'Lihat Detail' },
            { action: 'dismiss', title: 'Tutup' }
        ],
        data: {
            url: data.action_url || '/dashboard'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification Click Event Listener
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // Focus existing window if available
            for (const client of windowClients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(urlToOpen);
                    return client.focus();
                }
            }
            // Open new window
            return clients.openWindow(urlToOpen);
        })
    );
});
