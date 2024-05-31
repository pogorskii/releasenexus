<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->float('aggregated_rating')->nullable();
            $table->unsignedInteger('aggregated_rating_count')->nullable();
            $table->dateTime('first_release_date')->nullable();
            $table->unsignedInteger('follows')->nullable();
            $table->unsignedInteger('hypes')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('version_parent')->nullable();
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();
        });

        // ID                    uint32             `json:"id"`
// Name                  string             `json:"name"`
// Slug                  string             `json:"slug"`
// AggregatedRating      float32            `json:"aggregated_rating" gorm:"column:rating"`
// AggregatedRatingCount uint32             `json:"aggregated_rating_count" gorm:"column:reviewsCount"`
// FirstReleaseDate      *uint32            `json:"first_release_date"`
// Follows               *uint32            `json:"follows"`
// Hypes                 *uint32            `json:"hypes"`
// Status                *uint8             `json:"status"`
// Summary               *string            `json:"summary"`
// VersionTitle          *string            `json:"version_title"`
// UpdatedAt             uint32             `json:"updated_at"`
// Checksum              string             `json:"checksum"`

        // Relations

        // VersionParent         *uint32            `json:"version_parent"`
        // Category              uint8              `json:"category"`
        // InvolvedCompanies     []uint32           `json:"involved_companies"`
// ParentGame            *uint32            `json:"parent_game"`
// Platforms             []uint16           `json:"platforms"`
// PlayerPerspectives    []uint16           `json:"player_perspectives"`
// Ports                 []uint32           `json:"ports"`
// Remakes               []uint32           `json:"remakes"`
// Remasters             []uint32           `json:"remasters"`
// StandaloneExpansions  []uint32           `json:"standalone_expansions"`
// SimilarGames          []uint32           `json:"similar_games"`
        // Themes                []uint16           `json:"themes"`
        // GameModes             []uint8            `json:"game_modes"`
// Genres                []uint8            `json:"genres"`
        // DLCs                  []uint32           `json:"dlcs"`
// ExpandedGames         []uint32           `json:"expanded_games"`
// Expansions            []uint32           `json:"expansions"`
        // AgeRatings            []AgeRating        `json:"age_ratings" gorm:"-"`
        // AlternativeNames      []AlternativeName  `json:"alternative_names" gorm:"-"`
        // Collection            *Collection        `json:"collection"`
// Collections           []Collection       `json:"collections"`
// Cover                 *Cover             `json:"cover"`
        // ExternalGames         []ExternalGame     `json:"external_games"`
        // Franchise             *Franchise         `json:"franchise"`
        // Franchises            []Franchise        `json:"franchises"`
// GameEngines           []Engine           `json:"game_engines"`
        // GameLocalizations     []GameLocalization `json:"game_localizations"`
        // LanguageSupports      []LanguageSupport  `json:"language_supports"`
        // ReleaseDates          []ReleaseDate      `json:"release_dates"`
        // Screenshots           []Screenshot       `json:"screenshots"`
        // Videos                []Video            `json:"videos"`
// Websites              []Website          `json:"websites"`
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
