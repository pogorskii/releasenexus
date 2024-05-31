import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
    build: {
        chunkSizeWarningLimit: 5000,
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.ts"],
            ssr: "resources/js/ssr.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    ssr: {
        noExternal: ["@inertiajs/server"],
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            "ziggy-js": path.resolve("vendor/tightenco/ziggy"),
        },
    },
});
