const CACHE_NAME = 'crm-prospeccion-v1';
const APP_SHELL = [
    '/',
    '/admin',
    '/manifest.webmanifest',
    '/icons/icon.svg'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();

    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL).catch(() => null))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
        ))
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    event.respondWith(
        fetch(request)
            .then((response) => response)
            .catch(() => caches.match(request).then((cached) => cached || caches.match('/admin')))
    );
});
