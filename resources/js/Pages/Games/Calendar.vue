<script setup lang="ts">
import {InertiaLink} from "@inertiajs/inertia-vue3";

const props = defineProps<{
    datedReleases: Array<any>;
    tbdReleases: Array<any>;
}>()

const {year, month} = route().params;

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

// main_game	0
// dlc_addon	1
// expansion	2
// bundle	3
// standalone_expansion	4
// mod	5
// episode	6
// season	7
// remake	8
// remaster	9
// expanded_game	10
// port	11
// fork	12
// pack	13
// update	14
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

// Function to convert date to a string
const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {month: 'long', day: 'numeric'});
};

// TODO: Handle different regions
</script>
<template>
    <div class="bg-black w-full min-w-full">
        <div class="flex gap-10">
            <InertiaLink
                :href="`/games/calendar/${year}/${Number(month) - 1}`"
                class="px-4 py-2 block bg-purple-400 w-full font-semibold text-center">Previous month
            </InertiaLink>
            <h1 class="text-2xl font-semibold whitespace-nowrap">Games releasing in {{ monthsMap[Number(month)] }} {{ year }}</h1>
            <InertiaLink
                :href="`/games/calendar/${year}/${Number(month) + 1}`"
                class="px-4 py-2 block bg-purple-400 w-full font-semibold text-center">Next month
            </InertiaLink>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div v-for="day in datedReleases"
                 class="bg-black p-6 flex gap-6">
                <div>
                    <h2 class="text-2xl font-semibold whitespace-nowrap border-b pb-2">{{ formatDate(day.date) }}</h2>
                </div>
                <div class="grow grid grid-cols-3 2xl:grid-cols-4 gap-6">
                    <div v-for="release in day.releases"
                         class="bg-gray-800 overflow-hidden shadow sm:rounded-lg aspect-[3/5] flex flex-col justify-between">
                        <div class="relative overflow-hidden">
                            <img
                                :src="'https://images.igdb.com/igdb/image/upload/t_original/' + release.game.covers[0]?.g_image_id + '.jpg'"
                                alt="Cover image"
                                class="duration-200 ease-in-out hover:scale-105 object-cover w-full"
                                :width="release.game.covers[0]?.image.width || 600"
                                :height="release.game.covers[0]?.image.height || 900"
                            />
                            <div
                                class="absolute left-2 top-2 z-10 text-black uppercase text-xs bg-white p-2 font-bold rounded-lg bg-opacity-75">{{ gameTypeMap[release.game.category] }}
                            </div>
                        </div>
                        <div class="flex grow flex-col p-6 pb-3">
                            <h3 class="mb-4 font-semibold text-xl leading-tight">
                                <InertiaLink
                                    class="hover:text-primary hover:underline hover:decoration-solid hover:underline-offset-4">{{ release.game.name }}
                                </InertiaLink>
                            </h3>
                            <div class="w-full h-[2px] bg-purple-400 mb-2"></div>
                            <div class="mb-auto inline-flex flex-wrap gap-2 self-start">
                                <div v-for="releaseDate in release.release_dates.filter(r => r.region == 8)"
                                     class="text-sm">
                                    {{ releaseDate.platform.abbreviation ?? releaseDate.platform.name }}
                                </div>
                            </div>
                        </div>
                        <InertiaLink
                            class="px-4 py-2 block bg-purple-400 w-full font-semibold text-center">More info &rarr;
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
