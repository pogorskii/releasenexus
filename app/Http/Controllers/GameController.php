<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\GReleaseDateResource;
use App\Models\Game;
use App\Models\GReleaseDate;
use Inertia\Inertia;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $year, int $month)
    {
//        $releaseDates = GReleaseDate::with('dateable')->whereYear('date', $year)->whereMonth('date', $month);

//        $releaseDays = $releaseDates->groupBy(['date', 'dateable_id'])->map(function ($item) {
//            return [
//                'date'     => $item->first()->first()->date,
//                'releases' => $item->map(function ($release) {
//                    return [
//                        'game'          => (new GameResource($release->first()->dateable))->resolve(),
//                        'release_dates' => $release,
//                    ];
//                })->values(),
//            ];
//        })->sortBy('date')->values();

        $releasingOnSpecificDate = GReleaseDate::with('dateable.covers.image', 'platform')->where('dateable_type', 'App\Models\Game')->whereYear('date', $year)->whereMonth('date', $month)->where('category', '0')->where('dateable_id', '!=', null)->get();
        $formattedReleases       = $releasingOnSpecificDate->groupBy(['date', 'dateable_id'])->map(function ($item) {
            return [
                'date'     => $item->first()->first()->date,
                'releases' => $item->map(function ($release) {
                    return [
                        'game'          => (new GameResource($release->first()->dateable))->resolve(),
                        'release_dates' => $release,
                    ];
                })->values(),
            ];
        })->sortBy('date')->values();

        $releasingThisMonth   = GReleaseDate::with('dateable.covers.image', 'platform')->where('dateable_type', 'App\Models\Game')->whereYear('date', $year)->whereMonth('date', $month)->where('category', '1')->where('dateable_id', '!=', null)->get();
        $formattedTBDReleases = $releasingThisMonth->groupBy('dateable_id')->map(function ($item) {
            return [
                'game'          => (new GameResource($item->first()->dateable))->resolve(),
                'release_dates' => $item,
            ];
        })->values();

//        dd($formattedReleases[0]['releases'][0]['game']);

        return Inertia::render('Games/Calendar', [
            'datedReleases' => $formattedReleases,
            'tbdReleases'   => $formattedTBDReleases,
            'params'        => [
                'year'  => $year,
                'month' => $month,
            ],

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
