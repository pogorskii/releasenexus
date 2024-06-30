import './bootstrap';
import '../css/app.css';

import {createSSRApp, h, DefineComponent} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from "ziggy-js";
import {Ziggy} from '../ziggy.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        createSSRApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue, Ziggy as any)
            .mount(el);
    },
});
