<?php

namespace App\Support;

class Branding
{
    /** Per-request memo so the org row is queried at most once. */
    private static ?string $name = null;
    private static ?string $shortName = null;

    /**
     * The display brand name. Prefers the Organization Profile name (set in
     * Setup), falling back to APP_NAME. Safe before the DB/table exists.
     */
    public static function name(): string
    {
        if (self::$name !== null) {
            return self::$name;
        }

        try {
            $orgName = \App\Models\OrganizationProfile::query()->value('organization_name_en');
        } catch (\Throwable $e) {
            $orgName = null;
        }

        return self::$name = ($orgName ?: config('app.name', 'App'));
    }

    /**
     * Short brand name for compact spots; prefers short_name, then full name.
     */
    public static function shortName(): string
    {
        if (self::$shortName !== null) {
            return self::$shortName;
        }

        try {
            $short = \App\Models\OrganizationProfile::query()->value('short_name');
        } catch (\Throwable $e) {
            $short = null;
        }

        return self::$shortName = ($short ?: self::name());
    }

    /**
     * Per-server brand assets.
     *
     * Each server may override the default logos by placing files of the same
     * name in public/branding/ (which is git-ignored). If an override exists it
     * is used; otherwise the committed default in public/assets/logo/ is used.
     * This keeps one codebase white-labelable across deployments.
     */
    private static function resolve(string $file): string
    {
        return file_exists(public_path('branding/' . $file))
            ? 'branding/' . $file
            : 'assets/logo/' . $file;
    }

    /** Public URL for a brand asset (for HTML/web pages). */
    public static function url(string $file): string
    {
        return asset(self::resolve($file));
    }

    /** Absolute filesystem path for a brand asset (for mPDF embedding). */
    public static function path(string $file): string
    {
        return public_path(self::resolve($file));
    }
}
