<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps<{
    releases: Array<any>;
}>()

console.log(props.releases);
</script>
<template>
    <div class="bg-gray-800 w-full min-w-full shrink-0 h-screen">
        <h1 class="text-2xl font-semibold">Calendar</h1>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2">
            <div v-for="day in releases"
                 class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                <h2 class="text-2xl font-semibold">{{ day.date }}</h2>
                {{ day }}
                <!--                <div class="px-4 py-5 sm:px-6">-->
                <!--                    <div v-for="release_date in day.release_dates" :key="release_date.id">-->
                <!--                        <h3 class="text-lg font-semibold">{{ release_date.title }}</h3>-->
                <!--                        &lt;!&ndash;                        <p class="text-gray-500 dark:text-gray-400">{{ release_date.platform }}</p>&ndash;&gt;-->
                <!--                        <p class="text-gray-500 dark:text-gray-400">{{ release_date }}</p>-->
                <!--                    </div>-->
                <!--                </div>-->
            </div>
        </div>
    </div>
</template>
<style scoped>
</style>
