// resources/js/echo.js
//
// Este archivo es creado automáticamente por:  php artisan install:broadcasting
//
// VERIFICA que tenga exactamente esta estructura para Reverb.
// Las variables VITE_REVERB_* se añaden a .env por el mismo comando.

/*

local sin depender de cuentas se requiere servidor vps con dominio y certificado ssl
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster:        'reverb',
    key:                import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:             import.meta.env.VITE_REVERB_HOST,
    wsPort:             import.meta.env.VITE_REVERB_PORT  ?? 80,
    wssPort:            import.meta.env.VITE_REVERB_PORT  ?? 443,
    forceTLS:           (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports:  ['ws', 'wss'],
});
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',

    key: import.meta.env.VITE_PUSHER_APP_KEY,

    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,

    forceTLS: true,
});
