// resources/js/app.js

/**
 * First, load your bootstrap file which sets up axios, lodash, etc.
 * Make sure this path matches your project.
 */
import './bootstrap';

/**
 * Laravel Echo + Pusher setup
 */
import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,      // set in .env: MIX_PUSHER_APP_KEY=your-key
  cluster: process.env.MIX_PUSHER_APP_CLUSTER, // MIX_PUSHER_APP_CLUSTER=mt1 (or your cluster)
  forceTLS: true
});

/**
 * Subscribe to the channel and listen for NewsNotificationCreated
 * (your event’s broadcastOn() returns new Channel('news-notifications'))
 */
window.Echo.channel('news-notifications')
  .listen('NewsNotificationCreated', (payload) => {
    // payload comes from broadcastWith(): { title, content, date }
    const title   = payload.title;
    const message = payload.content;

    // 1) Update the badge count
    let badge = document.querySelector('.dropdown .badge');
    let count = badge ? parseInt(badge.innerText, 10) + 1 : 1;

    if (badge) {
      badge.innerText = count;
    } else {
      const btn = document.querySelector('.has-indicator');
      badge = document.createElement('span');
      badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
      badge.innerText = count;
      btn.appendChild(badge);
    }

    // 2) Prepend the new notification to the dropdown list
    const listContainer = document.querySelector('.dropdown-menu .max-h-400-px');
    const itemHtml = `
      <a href="javascript:void(0)" 
         class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
        <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
          <span class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
            <iconify-icon icon="bitcoin-icons:verify-outline" class="icon text-xxl"></iconify-icon>
          </span>
          <div>
            <h6 class="text-md fw-semibold mb-4">${title}</h6>
            <p class="mb-0 text-sm text-secondary-light text-w-200-px">${message}</p>
          </div>
        </div>
        <span class="text-sm text-secondary-light flex-shrink-0">Just Now</span>
      </a>
    `;
    listContainer.insertAdjacentHTML('afterbegin', itemHtml);

    // 3) Add blinking indicator
    document.querySelector('.has-indicator').classList.add('blink');
  });
