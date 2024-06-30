import {PageProps as InertiaPageProps} from "@inertiajs/core";
import {AxiosInstance} from "axios";
import ziggyRoute, {Config as ZiggyConfig} from "ziggy-js";
import {PageProps as AppPageProps} from "./";

declare global {
    const route: typeof routeFn;

    interface Window {
        axios: AxiosInstance;
    }
}

declare module "@vue/runtime-core" {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

// declare global {
//     interface Window {
//         axios: AxiosInstance;
//     }
//
//     var route: typeof ziggyRoute;
//     var Ziggy: ZiggyConfig;
// }
//
declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {
    }

    const route: typeof routeFn;
}
