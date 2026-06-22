<?php

namespace App\Support;

class Branding
{
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
