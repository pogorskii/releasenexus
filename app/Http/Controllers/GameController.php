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
//        $releaseDate = GReleaseDate::with('dateable')->whereYear('date', $year)->whereMonth('date', $month)->get();
//        Load the dateable relationship
        $releaseDates = GReleaseDate::whereYear('date', $year)->whereMonth('date', $month)->with('dateable')->get();

//        Group by dateable_id and date
//        $releaseDays = $releaseDate->groupBy('date')->map(function ($item) {
//            return [
//                'date'          => $item->first()->date,
//                'release_dates' => GReleaseDateResource::collection($item->load('dateable'))->resolve(),
//            ];
//        })->values()->sortBy('date')->values();

        return Inertia::render('Games/Calendar', [
            'releases' => $releaseDates,
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
