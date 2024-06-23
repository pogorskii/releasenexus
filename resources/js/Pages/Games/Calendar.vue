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
        <h1 class="text-2xl font-semibold">Games releasing in {{ monthsMap[Number(month)] }} {{ year }}</h1>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-1">
            <div v-for="day in datedReleases"
                 class="bg-black p-6 flex gap-6">
                <h2 class="text-2xl font-semibold whitespace-nowrap">{{ formatDate(day.date) }}</h2>
                <div class="grow grid grid-cols-3 gap-2">
                    <div v-for="release in day.releases"
                         class="bg-gray-800 overflow-hidden shadow sm:rounded-lg p-4 min-h-[15rem] flex flex-col justify-between">
                        <h3 class="text-xl font-semibold mb-2">{{ release.game.name }} ({{ gameTypeMap[release.game.category] }})</h3>
                        <div class="w-full h-[2px] bg-purple-400 mb-2"></div>
                        <div class="flex gap-2 mb-auto">
                            <div v-for="releaseDate in release.release_dates.filter(r => r.region == 8)"
                                 class="text-sm">
                                {{ releaseDate.platform.abbreviation ?? releaseDate.platform.name }}
                            </div>
                        </div>
                        <InertiaLink
                            class="px-4 py-2 block bg-purple-400 w-full rounded-lg font-semibold">More info &rarr;
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
