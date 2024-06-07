<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\Storage;

class ExportGamesToCSV
{
    public static function execute(array $games, string $path): void
    {
        $handle = fopen($path, 'a');

//        fputcsv($handle, [
//            'IGDB ID',
//            'Name',
//            'Release Date',
//        ]);

        fputcsv($handle, [
            'id',
            'age_ratings',
            'aggregated_rating',
            'aggregated_rating_count',
            'alternative_names',
            'artworks',
            'bundles',
            'category',
            'checksum',
            'collection',
            'collections',
            'cover',
            'created_at',
            'dlcs',
            'expanded_games',
            'expansions',
            'external_games',
            'first_release_date',
            'follows',
            'forks',
            'franchise',
            'franchises',
            'game_engines',
            'game_localizations',
            'game_modes',
            'genres',
            'hypes',
            'involved_companies',
            'keywords',
            'language_supports',
            'multiplayer_modes',
            'name',
            'parent_game',
            'platforms',
            'player_perspectives',
            'ports',
            'rating',
            'rating_count',
            'release_dates',
            'remakes',
            'remasters',
            'screenshots',
            'similar_games',
            'slug',
            'standalone_expansions',
            'status',
            'storyline',
            'summary',
            'tags',
            'themes',
            'total_rating',
            'total_rating_count',
            'updated_at',
            'url',
            'version_parent',
            'version_title',
            'videos',
            'websites',
        ]);

        collect($games)->chunk(500)->each(function ($chunk) use ($handle) {
            foreach ($chunk as $game) {
                $data = [
                    $game['id'],
                    json_encode($game['age_ratings'] ?? ''),
                    $game['aggregated_rating'] ?? '',
                    $game['aggregated_rating_count'] ?? '',
                    json_encode($game['alternative_names'] ?? ''),
                    //                    json_encode(array_key_exists('alternative_names', $game) ? $game['alternative_names'] : [])
                    //                    $game['artworks'] ?? '',
                    //                    $game['bundles'] ?? '',
                    //                    $game['category'] ?? '',
                    //                    $game['checksum'] ?? '',
                    //                    $game['collection'] ?? '',
                    //                    $game['collections'] ?? '',
                    //                    $game['cover'] ?? '',
                    //                    $game['created_at'] ?? '',
                    //                    $game['dlcs'] ?? '',
                    //                    $game['expanded_games'] ?? '',
                    //                    $game['expansions'] ?? '',
                    //                    $game['external_games'] ?? '',
                    //                    $game['first_release_date'] ?? '',
                    //                    $game['follows'] ?? '',
                    //                    $game['forks'] ?? '',
                    //                    $game['franchise'] ?? '',
                    //                    $game['franchises'] ?? '',
                    //                    $game['game_engines'] ?? '',
                    //                    $game['game_localizations'] ?? '',
                    //                    $game['game_modes'] ?? '',
                    //                    $game['genres'] ?? '',
                    //                    $game['hypes'] ?? '',
                    //                    $game['involved_companies'] ?? '',
                    //                    $game['keywords'] ?? '',
                    //                    $game['language_supports'] ?? '',
                    //                    $game['multiplayer_modes'] ?? '',
                    //                    $game['name'] ?? '',
                    //                    $game['parent_game'] ?? '',
                    //                    $game['platforms'] ?? '',
                    //                    $game['player_perspectives'] ?? '',
                    //                    $game['ports'] ?? '',
                    //                    $game['rating'] ?? '',
                    //                    $game['rating_count'] ?? '',
                    //                    $game['release_dates'] ?? '',
                    //                    $game['remakes'] ?? '',
                    //                    $game['remasters'] ?? '',
                    //                    $game['screenshots'] ?? '',
                    //                    $game['similar_games'] ?? '',
                    //                    $game['slug'] ?? '',
                    //                    $game['standalone_expansions'] ?? '',
                    //                    $game['status'] ?? '',
                    //                    $game['storyline'] ?? '',
                    //                    $game['summary'] ?? '',
                    //                    $game['tags'] ?? '',
                    //                    $game['themes'] ?? '',
                    //                    $game['total_rating'] ?? '',
                    //                    $game['total_rating_count'] ?? '',
                    //                    $game['updated_at'] ?? '',
                    //                    $game['url'] ?? '',
                    //                    $game['version_parent'] ?? '',
                    //                    $game['version_title'] ?? '',
                    //                    $game['videos'] ?? '',
                    //                    $game['websites'] ?? '',
                ];

                fputcsv($handle, $data);
            }
        });

        fclose($handle);

//        age_ratings	Array of Age Rating IDs	The PEGI rating
//aggregated_rating	Double	Rating based on external critic scores
//aggregated_rating_count	Integer	Number of external critic scores
//alternative_names	Array of Alternative Name IDs	Alternative names for this game
//                                                                            artworks	Array of Artwork IDs	Artworks of this game
//bundles	Array of Game IDs	The bundles this game is a part of
//category	Category Enum	The category of this game
//checksum	uuid	Hash of the object
//collection	Reference ID for Collection	The series the game belongs to
//collections	Array of Collection IDs	The collections that this game is in.
//    cover	Reference ID for Cover	The cover of this game
//created_at	datetime	Date this was initially added to the IGDB database
//dlcs	Array of Game IDs	DLCs for this game
//                                      expanded_games	Array of Game IDs	Expanded games of this game
//expansions	Array of Game IDs	Expansions of this game
//external_games	Array of External Game IDs	External IDs this game has on other services
//first_release_date	Unix Time Stamp	The first release date for this game
//                                                                    follows	Integer	[Deprecated - To be removed] Number of people following a game
//forks	Array of Game IDs	Forks of this game
//franchise	Reference ID for Franchise	The main franchise
//franchises	Array of Franchise IDs	Other franchises the game belongs to
//game_engines	Array of Game Engine IDs	The game engine used in this game
//game_localizations	Array of Game Localization IDs	Supported game localizations for this game. A region can have at most one game localization for a given game
//    game_modes	Array of Game Mode IDs	Modes of gameplay
//genres	Array of Genre IDs	Genres of the game
//hypes	Integer	Number of follows a game gets before release
//involved_companies	Array of Involved Company IDs	Companies who developed this game
//keywords	Array of Keyword IDs	Associated keywords
//language_supports	Array of Language Support IDs	Supported Languages for this game
//                                                                              multiplayer_modes	Array of Multiplayer Mode IDs	Multiplayer modes for this game
//                                                                                                                                                          name	String
//parent_game	Reference ID for Game	If a DLC, expansion or part of a bundle, this is the main game or bundle
//platforms	Array of Platform IDs	Platforms this game was released on
//player_perspectives	Array of Player Perspective IDs	The main perspective of the player
//ports	Array of Game IDs	Ports of this game
//rating	Double	Average IGDB user rating
//rating_count	Integer	Total number of IGDB user ratings
//release_dates	Array of Release Date IDs	Release dates of this game
//remakes	Array of Game IDs	Remakes of this game
//remasters	Array of Game IDs	Remasters of this game
//screenshots	Array of Screenshot IDs	Screenshots of this game
//similar_games	Array of Game IDs	Similar games
//slug	String	A url-safe, unique, lower-case version of the name
//standalone_expansions	Array of Game IDs	Standalone expansions of this game
//status	Status Enum	The status of the games release
//storyline	String	A short description of a games story
//summary	String	A description of the game
//tags	Array of Tag Numbers	Related entities in the IGDB API
//themes	Array of Theme IDs	Themes of the game
//total_rating	Double	Average rating based on both IGDB user and external critic scores
//total_rating_count	Integer	Total number of user and external critic scores
//updated_at	datetime	The last date this entry was updated in the IGDB database
//url	String	The website address (URL) of the item
//version_parent	Reference ID for Game	If a version, this is the main game
//version_title	String	Title of this version (i.e Gold edition)
//videos	Array of Game Video IDs	Videos of this game
//websites	Array of Website IDs	Websites associated with this game
    }
}
