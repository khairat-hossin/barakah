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

    /**
     * Return the URL of the first uploaded override (in public/branding/) among
     * the candidate filenames, or null if none has been uploaded yet. There is
     * no default — the logo/favicon stay blank until the user uploads one on the
     * Branding settings page. The ?v=<mtime> busts caches on a fresh upload.
     */
    private static function firstOverrideUrl(array $candidates): ?string
    {
        foreach ($candidates as $file) {
            $path = public_path('branding/' . $file);
            if (file_exists($path)) {
                return asset('branding/' . $file) . '?v=' . filemtime($path);
            }
        }

        return null;
    }

    /** Candidate filenames (override dir) for the uploaded logo / favicon. */
    public static function logoCandidates(): array
    {
        return ['logo-icon.png', 'logo-icon.svg', 'logo-icon.jpg', 'logo-icon.jpeg', 'logo-icon.webp'];
    }

    public static function faviconCandidates(): array
    {
        return ['favicon.png', 'favicon.ico', 'favicon.svg'];
    }

    /** Uploaded app/sidebar logo URL, or null if none has been set yet. */
    public static function logoUrl(): ?string
    {
        return self::firstOverrideUrl(self::logoCandidates());
    }

    /** Uploaded browser favicon URL, or null if none has been set yet. */
    public static function faviconUrl(): ?string
    {
        return self::firstOverrideUrl(self::faviconCandidates());
    }

    /**
     * Absolute filesystem path to the uploaded logo (for embedding in mPDF
     * PDFs), or null if none has been set yet. mPDF reads local files by path.
     */
    public static function logoPath(): ?string
    {
        foreach (self::logoCandidates() as $file) {
            $path = public_path('branding/' . $file);
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Logo path for PDF/report headers: the uploaded logo if one exists,
     * otherwise the committed default asset. Reports always carry a letterhead
     * logo, so — unlike the app chrome — this falls back to the default rather
     * than showing blank. Returns null only if no logo file exists at all.
     */
    public static function pdfLogoPath(): ?string
    {
        if ($override = self::logoPath()) {
            return $override;
        }

        foreach (['logo-icon-sm.png', 'logo-icon.png'] as $file) {
            $path = public_path('assets/logo/' . $file);
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
