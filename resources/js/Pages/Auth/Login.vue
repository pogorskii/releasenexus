<script lang="ts" setup>
import {useForm} from "@inertiajs/vue3";
// import {route} from "ziggy-js";

const loginForm = useForm({
    email: null,
    password: null,
    loginMethod: "email",
    remember: true,
});

const logIn = () => {
    console.log(loginForm);
    loginForm.post(route('asdsad'), {
        preserveScroll: true,
        onSuccess: async (resp) => {
            console.log(resp);
        },
        onError: (error) => {
            console.error(error);
            // $store.responseMessages.push({
            //     text: Object.values(error)[0] ?? "There was an error.",
            //     type: "error",
            //     bag: "login",
            // });
        },
        onFinish: () => {
        },
    });
};
</script>
<template>
    <div>
        <h1>Please log in</h1>
        <form @submit.prevent="logIn">
            <div class="mb-4 mb-6 flex items-center rounded-full border bg-transparent">
                <input
                    v-model="loginForm.email"
                    :placeholder="loginForm.loginMethod === 'phone' ? 'Phone number' : 'Email address'"
                    :type="loginForm.loginMethod === 'phone' ? 'text' : 'email'"
                    class="grow rounded-r-full bg-transparent px-1 py-4 placeholder-zinc-400 focus:outline-none"
                    name="email"
                />
            </div>
            <div class="mb-12 flex items-center rounded-full border bg-transparent">
                <input
                    v-model="loginForm.password"
                    class="grow rounded-r-full bg-transparent px-2 py-4 placeholder-zinc-400 focus:outline-none"
                    name="password"
                    placeholder="Password"
                    type="password"
                />
            </div>
            <div class="mb-4 flex justify-center">
                <button
                    class="block w-full shrink grow bg-ph-teal-600 px-16 py-3 text-lg font-semibold uppercase shadow hover:bg-ph-teal-500"
                    type="submit"
                >
                    Log In
                </button>
            </div>
        </form>
    </div>
</template>
<style scoped>
</style>
