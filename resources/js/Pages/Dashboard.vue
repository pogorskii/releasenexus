<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';

const props = defineProps<{ games?: any, user: any }>();
</script>
<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Hey {{ user.full_name }}, you're logged in!</div>
                    <div>Games currently in the database: {{ games }}</div>
                    <!--                    A link to get-csv route -->
                    <!--                        <a :href="route('get-csv')" class="text-blue-500">Download CSV</a>-->
                    <button @click="router.post(route('get-csv'))">Download CSV</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
