// src/echo.js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js'; 

let echo;

export function initEcho(token) {
  if (echo) return echo;

  echo = new Echo({
    broadcaster: 'reverb',   // use Laravel Reverb
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,         // disable TLS if running locally
    enabledTransports: ['ws', 'wss'],
    authEndpoint: 'http://127.0.0.1:8000/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    },
  });

  return echo;
}

export function getEcho() {
  return echo;
}

export function disconnectEcho() {
  if (!echo) return;
  try {
    echo.disconnect();
  } catch (e) {}
  echo = null;
}
