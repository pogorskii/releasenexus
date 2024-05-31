import { createSSRApp, h } from "vue";
import { renderToString } from "@vue/server-renderer";
import { createInertiaApp } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { ZiggyVue } from "ziggy-js";
async function resolvePageComponent(path, pages) {
  for (const p of Array.isArray(path) ? path : [path]) {
    const page = pages[p];
    if (typeof page === "undefined") {
      continue;
    }
    return typeof page === "function" ? page() : page;
  }
  throw new Error(`Page not found: ${path}`);
}
const appName = "ReleaseNexus";
createServer(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, /* @__PURE__ */ Object.assign({ "./Pages/Auth/ConfirmPassword.vue": () => import("./assets/ConfirmPassword-DHspj_qB.js"), "./Pages/Auth/ForgotPassword.vue": () => import("./assets/ForgotPassword-Mjv2LGAH.js"), "./Pages/Auth/Login.vue": () => import("./assets/Login-D5GrPBvX.js"), "./Pages/Auth/Register.vue": () => import("./assets/Register-CzVL4jZw.js"), "./Pages/Auth/ResetPassword.vue": () => import("./assets/ResetPassword-B66p_HuF.js"), "./Pages/Auth/VerifyEmail.vue": () => import("./assets/VerifyEmail-spwoBlx-.js"), "./Pages/Dashboard.vue": () => import("./assets/Dashboard-DkK_liv0.js"), "./Pages/Home.vue": () => import("./assets/Home-BgAnjBX5.js"), "./Pages/Profile/Edit.vue": () => import("./assets/Edit-DbmYqj-C.js"), "./Pages/Profile/Partials/DeleteUserForm.vue": () => import("./assets/DeleteUserForm-C_f33_4A.js"), "./Pages/Profile/Partials/UpdatePasswordForm.vue": () => import("./assets/UpdatePasswordForm-DbI6q6Go.js"), "./Pages/Profile/Partials/UpdateProfileInformationForm.vue": () => import("./assets/UpdateProfileInformationForm-BOjNNy-X.js"), "./Pages/Welcome.vue": () => import("./assets/Welcome-Dn4kg8Da.js") })),
    setup({ App, props, plugin }) {
      return createSSRApp({ render: () => h(App, props) }).use(plugin).use(ZiggyVue, {
        ...page.props.ziggy,
        location: new URL(page.props.ziggy.location)
      });
    }
  })
);
