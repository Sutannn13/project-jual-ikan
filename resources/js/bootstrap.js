import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 * 
 * DISABLED: Chat menggunakan polling, bukan WebSocket.
 * Uncomment jika ingin pakai real-time broadcasting dengan Reverb.
 */

// import './echo';
