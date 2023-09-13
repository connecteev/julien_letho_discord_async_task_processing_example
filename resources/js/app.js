import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster:       'pusher',
    key:               import.meta.env.VITE_PUSHER_APP_KEY,
    cluster:           import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost:            window.location.hostname,
    wsPort:            6001,
    wssPort:           6001,
    forceTLS:          false,
    enabledTransports: ['ws', 'wss']
})

createInertiaApp({
    title:   (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el)
    },
    progress: {
        color: '#4B5563'
    }
})
