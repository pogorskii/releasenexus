import PrimeVue from "primevue/config";
import PassThrough from "./theme.js";

export default {
    install: (app, options) => {
        app.use(PrimeVue, {pt: PassThrough});
    },
};
