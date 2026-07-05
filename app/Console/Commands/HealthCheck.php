<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HealthCheck extends Command
{
    protected $signature = 'app:health';

    protected $description = 'Post-deploy health check: config, database, storage, assets, fonts.';

    public function handle(): int
    {
        $this->line('Running health check...');
        $this->newLine();

        $failures = 0;

        // --- App config ---------------------------------------------------
        $this->check('APP_KEY is set', filled(config('app.key')), $failures);
        $this->check('APP_ENV is production', config('app.env') === 'production', $failures, warnOnly: true);
        $this->check('APP_DEBUG is off', config('app.debug') === false, $failures, warnOnly: true);
        $this->check('APP_URL is https', str_starts_with((string) config('app.url'), 'https://'), $failures, warnOnly: true);

        // --- Database -----------------------------------------------------
        $dbOk = false;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Throwable $e) {
            $this->check('Database connects', false, $failures, detail: $e->getMessage());
        }
        if ($dbOk) {
            $this->check('Database connects', true, $failures);
            $this->check('Migrations have run', Schema::hasTable('migrations') && Schema::hasTable('users'), $failures);
        }

        // --- Writable paths ----------------------------------------------
        $this->check('storage/ writable', is_writable(storage_path()), $failures);
        $this->check('storage/framework/views writable', is_writable(storage_path('framework/views')), $failures);
        $this->check('bootstrap/cache writable', is_writable(base_path('bootstrap/cache')), $failures);
        $this->check('storage/app/mpdf exists (PDF temp)', is_dir(storage_path('app/mpdf')), $failures, warnOnly: true);
        $this->check('public/branding writable (logo uploads)', is_dir(public_path('branding')) && is_writable(public_path('branding')), $failures, warnOnly: true);

        // --- Cache round-trip --------------------------------------------
        $cacheOk = false;
        try {
            Cache::put('health_check_ping', 'ok', 10);
            $cacheOk = Cache::get('health_check_ping') === 'ok';
            Cache::forget('health_check_ping');
        } catch (\Throwable $e) {
            // handled by the check below
        }
        $this->check('Cache store works', $cacheOk, $failures);

        // --- Front-end assets (Vite) -------------------------------------
        $this->check('Vite build present (public/build/manifest.json)', file_exists(public_path('build/manifest.json')), $failures);

        // --- PDF fonts ----------------------------------------------------
        $this->check('Bengali PDF font present', file_exists(resource_path('fonts/HindSiliguri-Regular.ttf')), $failures, warnOnly: true);

        // --- storage symlink ---------------------------------------------
        $this->check('public/storage symlink exists', file_exists(public_path('storage')), $failures, warnOnly: true);

        $this->newLine();
        if ($failures > 0) {
            $this->error("Health check FAILED — {$failures} critical issue(s) above.");
            return self::FAILURE;
        }

        $this->info('Health check passed. ✅');
        return self::SUCCESS;
    }

    /**
     * Print a check line. Increments $failures for hard failures; warnings are
     * shown but don't fail the command.
     */
    private function check(string $label, bool $ok, int &$failures, bool $warnOnly = false, ?string $detail = null): void
    {
        if ($ok) {
            $this->line("  <fg=green>✓</> {$label}");
            return;
        }

        if ($warnOnly) {
            $this->line("  <fg=yellow>!</> {$label} <fg=gray>(warning)</>");
            return;
        }

        $this->line("  <fg=red>✗</> {$label}" . ($detail ? " <fg=gray>— {$detail}</>" : ''));
        $failures++;
    }
}
