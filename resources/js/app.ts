import "./bootstrap";
import "../css/app.css";

import {createSSRApp, h} from 'vue'
import {createInertiaApp} from "@inertiajs/inertia-vue3";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import Plugins from "./Plugins";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({el, app, props, plugin}) {
        return createSSRApp({render: () => h(app, props)})
            .use(plugin)
            .use(Plugins)
            .mount(el);
    },
}).then(r => {
});
