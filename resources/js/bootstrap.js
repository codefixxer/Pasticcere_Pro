// polyfills, lodash, etc. if you need them
// window._ = require('lodash');

// ——— AXIOS ———
import axios from 'axios';
window.axios = axios;

// Tell Laravel we’re making XHR requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ——— ECHO + PUSHER ———
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,       // set in your .env
  cluster: process.env.MIX_PUSHER_APP_CLUSTER, // set in your .env
  forceTLS: true,                            // or `encrypted: true`
});
