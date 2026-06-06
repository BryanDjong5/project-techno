<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Game;

class SearchController extends Controller {
    public function searchGame(Request $request) {
    $keyword = $request->keyword;

    if (!$keyword || strlen($keyword) < 1) {
        return response()->json([]);
    }

    $games = Game::where('name', 'LIKE', "%{$keyword}%")
        ->limit(10)
        ->get();

    return response()->json($games);
   }
}