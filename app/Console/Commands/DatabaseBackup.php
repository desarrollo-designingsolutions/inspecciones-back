<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Genera un backup de la base de datos de Inspecciones';

    private string $remotePath = "drive:Backups/DsInspecciones/";

    public function handle(): void
    {
        if (in_array((int) date('N'), [6, 7])) {
            $this->info('⏭ Fin de semana, no se genera backup.');
            return;
        }

        $this->generateBackup();
        $this->applyRetentionPolicy();
    }

    // ─────────────────────────────────────────────
    // 1. GENERACIÓN DEL BACKUP
    // ─────────────────────────────────────────────

    private function generateBackup(): void
    {
        $filename   = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $localPath  = storage_path('app/backups/' . $filename);
        $backupsDir = storage_path('app/backups');

        if (!file_exists($backupsDir)) {
            mkdir($backupsDir, 0755, true);
        }

        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        $cmd = "mysqldump -h{$dbHost} -u{$dbUser} -p'{$dbPass}' {$dbName} > {$localPath}";
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('❌ Error al generar el backup.');
            return;
        }

        exec("gzip {$localPath}", $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('❌ Error al comprimir el backup.');
            return;
        }

        $compressedPath = $localPath . '.gz';
        exec("rclone move {$compressedPath} {$this->remotePath}", $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('❌ Error al subir a Google Drive.');
            return;
        }

        $this->info('✅ Backup generado, comprimido y subido correctamente.');
    }

    // ─────────────────────────────────────────────
    // 2. POLÍTICA DE RETENCIÓN
    // ─────────────────────────────────────────────

    private function applyRetentionPolicy(): void
    {
        $this->info('🔍 Listando backups remotos...');

        exec("rclone lsf {$this->remotePath}", $files, $exitCode);

        if ($exitCode !== 0) {
            $this->error('❌ No se pudo listar los archivos remotos.');
            return;
        }

        $today   = new DateTime();
        $backups = $this->parseBackupFiles($files);

        if (empty($backups)) {
            $this->info('No se encontraron backups para procesar.');
            return;
        }

        $toDelete = $this->resolveFilesToDelete($backups, $today);

        if (empty($toDelete)) {
            $this->info('✅ Todos los backups cumplen la política. Nada que eliminar.');
            return;
        }

        $this->info(sprintf('🗑  Eliminando %d backup(s) que no cumplen la política...', count($toDelete)));

        foreach ($toDelete as $filename) {
            exec("rclone delete " . escapeshellarg($this->remotePath . $filename), $out, $code);

            $code === 0
                ? $this->line("   ✓ Eliminado: {$filename}")
                : $this->warn("   ✗ No se pudo eliminar: {$filename}");
        }

        $this->info('✅ Política de retención aplicada.');
    }

    // ─────────────────────────────────────────────
    // 3. HELPERS
    // ─────────────────────────────────────────────

    private function parseBackupFiles(array $files): array
    {
        $backups = [];

        foreach ($files as $file) {
            $file = trim($file);
            if (preg_match('/^backup_(\d{4}-\d{2}-\d{2})_[\d-]+\.sql\.gz$/', $file, $m)) {
                try {
                    $backups[$file] = new DateTime($m[1]);
                } catch (\Exception $e) {
                    // Nombre con fecha inválida → ignorar
                }
            }
        }

        return $backups;
    }

    private function resolveFilesToDelete(array $backups, DateTime $today): array
    {
        $currentYear  = (int) $today->format('Y');
        $currentMonth = (int) $today->format('m');
        $currentWeek  = (int) $today->format('W');

        $grouped = [];

        foreach ($backups as $filename => $date) {
            $y = (int) $date->format('Y');
            $m = (int) $date->format('m');
            $w = (int) $date->format('W');

            $grouped[$y][$m][$w][$filename] = $date;
        }

        $toDelete = [];

        foreach ($grouped as $year => $months) {

            if ($year < $currentYear) {
                $allOfYear = $this->flattenGroup($months);
                $december  = array_filter(
                    $allOfYear,
                    fn(DateTime $d) => (int) $d->format('m') === 12
                );

                $keep     = $this->lastFridayOrLatest($december ?: $allOfYear);
                $toDelete = array_merge($toDelete, $this->filesNotIn($allOfYear, $keep));
                continue;
            }

            foreach ($months as $month => $weeks) {

                if ($month < $currentMonth) {
                    $allOfMonth = $this->flattenGroup([$month => $weeks]);
                    $keep       = $this->lastFridayOrLatest($allOfMonth);
                    $toDelete   = array_merge($toDelete, $this->filesNotIn($allOfMonth, $keep));
                    continue;
                }

                foreach ($weeks as $week => $weekBackups) {
                    if ($week < $currentWeek) {
                        $keep     = $this->lastFridayOrLatest($weekBackups);
                        $toDelete = array_merge($toDelete, $this->filesNotIn($weekBackups, $keep));
                    }
                }
            }
        }

        return array_unique($toDelete);
    }

    private function flattenGroup(array $group): array
    {
        $flat = [];
        array_walk_recursive($group, function ($value, $key) use (&$flat) {
            if ($value instanceof DateTime) {
                $flat[$key] = $value;
            }
        });
        return $flat;
    }

    private function lastFridayOrLatest(array $backups): ?string
    {
        if (empty($backups)) {
            return null;
        }

        $fridays = array_filter(
            $backups,
            fn(DateTime $d) => $d->format('N') === '5'
        );

        $pool = $fridays ?: $backups;

        uasort($pool, fn(DateTime $a, DateTime $b) => $b <=> $a);

        return array_key_first($pool);
    }

    private function filesNotIn(array $group, ?string $keepFilename): array
    {
        if ($keepFilename === null) {
            return array_keys($group);
        }

        return array_keys(
            array_filter($group, fn($_, $name) => $name !== $keepFilename, ARRAY_FILTER_USE_BOTH)
        );
    }
}
