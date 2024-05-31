import "./bootstrap";
import "../css/app.css";

import {createSSRApp, DefineComponent, h} from 'vue'
import {createInertiaApp} from "@inertiajs/inertia-vue3";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import {ZiggyVue} from "ziggy-js";
// import Plugins from "./Plugins";

const appName = import.meta.env.VITE_APP_NAME || "ReleaseNexus";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) =>
        await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>("./Pages/**/*.vue")
        ),
    setup({el, app, props, plugin}) {
        createSSRApp({render: () => h(app, props)})
            .use(plugin)
            .use(ZiggyVue, (window as any).Ziggy)
            .mount(el);
    },
}).then(r => {
});
