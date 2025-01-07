import Pusher from 'pusher-js';

window.Pusher = Pusher;

import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.DEV ? '127.0.0.1' : import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: false,
    encrypted: true,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    cluster: 'eu'
});
if (import.meta.env.DEV) {
    Pusher.log = (msg) => {
        console.log(msg);
    };
}
