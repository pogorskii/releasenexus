import {Config} from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};

// ID                    uint32             `json:"id"`
// Name                  string             `json:"name"`
// Slug                  string             `json:"slug"`
// AgeRatings            []AgeRating        `json:"age_ratings" gorm:"-"`
// AggregatedRating      float32            `json:"aggregated_rating" gorm:"column:rating"`
// AggregatedRatingCount uint32             `json:"aggregated_rating_count" gorm:"column:reviewsCount"`
// AlternativeNames      []AlternativeName  `json:"alternative_names" gorm:"-"`
// Category              uint8              `json:"category"`
// Collection            *Collection        `json:"collection"`
// Collections           []Collection       `json:"collections"`
// Cover                 *Cover             `json:"cover"`
// DLCs                  []uint32           `json:"dlcs"`
// ExpandedGames         []uint32           `json:"expanded_games"`
// Expansions            []uint32           `json:"expansions"`
// ExternalGames         []ExternalGame     `json:"external_games"`
// FirstReleaseDate      *uint32            `json:"first_release_date"`
// Follows               *uint32            `json:"follows"`
// Franchise             *Franchise         `json:"franchise"`
// Franchises            []Franchise        `json:"franchises"`
// GameEngines           []Engine           `json:"game_engines"`
// GameLocalizations     []GameLocalization `json:"game_localizations"`
// GameModes             []uint8            `json:"game_modes"`
// Genres                []uint8            `json:"genres"`
// Hypes                 *uint32            `json:"hypes"`
// InvolvedCompanies     []uint32           `json:"involved_companies"`
// LanguageSupports      []LanguageSupport  `json:"language_supports"`
// ParentGame            *uint32            `json:"parent_game"`
// Platforms             []uint16           `json:"platforms"`
// PlayerPerspectives    []uint16           `json:"player_perspectives"`
// Ports                 []uint32           `json:"ports"`
// ReleaseDates          []ReleaseDate      `json:"release_dates"`
// Remakes               []uint32           `json:"remakes"`
// Remasters             []uint32           `json:"remasters"`
// StandaloneExpansions  []uint32           `json:"standalone_expansions"`
// Screenshots           []Screenshot       `json:"screenshots"`
// SimilarGames          []uint32           `json:"similar_games"`
// Status                *uint8             `json:"status"`
// Summary               *string            `json:"summary"`
// Themes                []uint16           `json:"themes"`
// VersionParent         *uint32            `json:"version_parent"`
// VersionTitle          *string            `json:"version_title"`
// Videos                []Video            `json:"videos"`
// Websites              []Website          `json:"websites"`
// UpdatedAt             uint32             `json:"updated_at"`
// Checksum              string             `json:"checksum"`
