<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() {
    $games = [
        ['name' => 'Mobile Legends', 'category' => 'Top Up'],
        ['name' => 'Free Fire',       'category' => 'Diamonds'],
        ['name' => 'PUBG Mobile',     'category' => 'UC Credits'],
        ['name' => 'Point Blank',     'category' => 'Top Up'],
        ['name' => 'Roblox',          'category' => 'Robux'],
        ['name' => 'Genshin Impact',  'category' => 'Crystals'],
        ['name' => 'Valorant',        'category' => 'Points'],
        ['name' => 'Minecraft',       'category' => 'Game Key'],
    ];

    foreach ($games as $game) {
       \App\Models\Game::firstOrCreate(
            ['name' => $game['name']],
            ['category' => $game['category']]
        );
    }
}
}
