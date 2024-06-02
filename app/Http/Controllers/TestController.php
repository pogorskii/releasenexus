<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Game;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = (new UserResource($request->user()))->resolve();

            // Count all the games in the local db
            $gamesCount = Game::count();

//            ray('test');

//            dd($gamesCount);

            return Inertia::render('Dashboard', [
                'user'  => $user,
                'games' => $gamesCount,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
