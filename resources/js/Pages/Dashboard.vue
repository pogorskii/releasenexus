<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

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
                    <!--                    <div>{{ testThing }}</div>-->
                    <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Games</h2>
                        <div>{{ games[0] }}</div>
                        <!--                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">-->
                        <!--                            <div v-for="game in games" :key="game.id"-->
                        <!--                                 class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">-->
                        <!--                                <div class="p-6">-->
                        <!--                                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">{{ game.name }}</h3>-->
                        <!--                                    <p class="text-gray-500 dark:text-gray-400">{{ game.description }}</p>-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
