<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps<{
    games: Array<any>;
}>()
// use AuthernticatedLayout
</script>
<template>
    <GuestLayout>
        <div class="bg-gray-800 w-full min-w-full shrink-0 h-screen">
            <h1 class="text-black">Calendar</h1>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2">
                <div v-for="game in games" :key="game.id"
                     class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ game.name }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ game.date }}</p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
<style scoped>
</style>
