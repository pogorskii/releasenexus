<script setup lang="ts">
import {InertiaLink} from "@inertiajs/inertia-vue3";
import {computed, ref} from "vue";

const props = defineProps<{
    datedReleases: Array<any>;
    tbdReleases: Array<any>;
    params: {
        year: string;
        month: string;
    }
}>()

const monthsMap: {
    [key: number]: string;
} = {
    1: 'January',
    2: 'February',
    3: 'March',
    4: 'April',
    5: 'May',
    6: 'June',
    7: 'July',
    8: 'August',
    9: 'September',
    10: 'October',
    11: 'November',
    12: 'December',
};

const gameTypeMap: {
    [key: number]: string;
} = {
    0: 'Main game',
    1: 'DLC',
    2: 'Expansion',
    3: 'Bundle',
    4: 'Standalone expansion',
    5: 'Mod',
    6: 'Episode',
    7: 'Season',
    8: 'Remake',
    9: 'Remaster',
    10: 'Expanded game',
    11: 'Port',
    12: 'Fork',
    13: 'Pack',
    14: 'Update',
};

const selectedRegion = ref<number>(8);

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {month: 'long', day: 'numeric'});
};

const darkMode = true;

// filter by region
const filteredReleases = computed(() => {
    return props.datedReleases.map(({date, releases}) => {
        return {
            date,
            releases: releases.filter((release) => {
                const matchingReleaseDates = release.release_dates.filter((releaseDate) => releaseDate.region == selectedRegion.value);
                return matchingReleaseDates.length > 0;
            })
        }
    });
});

// TODO: Handle different regions
</script>
<template>
    <div class="bg-black w-full min-w-full" :class="{ dark: darkMode }">
        <div class="flex gap-10">
            <InertiaLink
                :href="`/games/calendar/${params.year}/${Number(params.month) - 1}`"
                class="px-4 py-2 block bg-purple-400 w-full font-semibold text-center">Previous month
            </InertiaLink>
            <h1 class="text-2xl font-semibold whitespace-nowrap">Games releasing in {{ monthsMap[Number(params.month)] }} {{ params.year }}</h1>
            <InertiaLink
                :href="`/games/calendar/${params.year}/${Number(params.month) + 1}`"
                class="px-4 py-2 block bg-purple-400 w-full font-semibold text-center">Next month
            </InertiaLink>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div v-for="day in filteredReleases"
                 class="bg-white dark:bg-black p-6 flex gap-6">
                <div>
                    <h2 class="text-2xl font-semibold whitespace-nowrap border-b pb-2">{{ formatDate(day.date) }}</h2>
                </div>
                <div class="grow grid grid-cols-3 2xl:grid-cols-4 gap-6">
                    <div v-for="release in day.releases"
                         class="aspect-[1/2] group relative col-span-1 flex flex-col justify-between overflow-hidden rounded-xl bg-white [box-shadow:0_0_0_1px_rgba(0,0,0,.03),0_2px_4px_rgba(0,0,0,.05),0_12px_24px_rgba(0,0,0,.05)] transform-gpu dark:bg-[#0f0f0f] dark:[border:1px_solid_rgba(255,255,255,.1)] dark:[box-shadow:0_-20px_80px_-20px_#ffffff1f_inset]">
                        <div class="relative overflow-hidden">
                            <img
                                :src="release.game.covers[0] ? 'https://images.igdb.com/igdb/image/upload/t_original/' + release.game.covers[0]?.g_image_id + '.jpg' : '/game-placeholder.webp'"
                                alt="Cover image"
                                class="duration-200 ease-in-out hover:scale-105 object-cover w-full"
                                :width="release.game.covers[0]?.image.width || 600"
                                :height="release.game.covers[0]?.image.height || 900"
                            />
                        </div>
                        <div
                            class="w-full h-6 flex items-center justify-center uppercase text-xs font-bold bg-transparent dark:[box-shadow:0_-20px_10px_-20px_hsl(var(--secondary))_inset,0_20px_10px_-20px_hsl(var(--secondary))_inset,20px_0_10px_-20px_hsl(var(--secondary))_inset,-20px_10px_10px_-20px_hsl(var(--secondary))_inset] border border-secondary tracking-wide">
                            {{ gameTypeMap[release.game.category] }}
                        </div>
                        <div class="flex grow flex-col p-6 pb-3">
                            <h3 class="mb-2 font-semibold text-xl leading-tight">
                                <InertiaLink
                                    class="hover:text-primary hover:underline hover:decoration-solid hover:underline-offset-4">{{ release.game.name }}
                                </InertiaLink>
                            </h3>
                            <div
                                class="w-full h-[1px] bg-primary mb-2 dark:[box-shadow:0_0_10px_1px_hsl(var(--primary))]"></div>
                            <div class="mb-auto inline-flex flex-wrap gap-2 self-start">
                                <div
                                    v-for="releaseDate in release.release_dates.filter((releaseDate) => releaseDate.region == selectedRegion)"
                                    class="text-sm">
                                    {{ releaseDate.platform.abbreviation ?? releaseDate.platform.name }}
                                </div>
                            </div>
                        </div>
                        <InertiaLink
                            class="px-4 py-2 block bg-primary/70 hover:bg-primary w-full font-semibold text-center transition-colors duration-100 dark:[box-shadow:0_-20px_10px_-20px_hsl(var(--primary))_inset,-20px_0px_10px_-20px_hsl(var(--primary))_inset,20px_0_10px_-20px_hsl(var(--primary))_inset]">More info &rarr;
                        </InertiaLink>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="tbdReleases.length">
            <h2 class="text-2xl font-semibold">Releasing this month without specific date</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2">
                <div v-for="release in tbdReleases"
                     class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <h3 class="text-lg font-semibold">{{ release.game.name }}</h3>
                    <!--                    <p>{{ release.platforms }}</p>-->
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
</style>
