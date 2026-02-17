<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Genera un backup de la base de datos';

    public function handle()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $localPath = storage_path('app/backups/' . $filename);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Generar dump
        $command = "mysqldump -h{$dbHost} -u{$dbUser} -p'{$dbPass}' {$dbName} > {$localPath}";
        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error('Error al generar el backup');
            return;
        }

        // Comprimir
        exec("gzip {$localPath}");
        $compressedPath = $localPath . '.gz';

        // Subir a Google Drive
        $remotePath = "drive:Backups/DsInspecciones/";
        exec("rclone move {$compressedPath} {$remotePath}", $output, $result);

        if ($result !== 0) {
            $this->error('Error al subir a Google Drive');
            return;
        }

        $this->info('Backup generado, comprimido y subido a Google Drive correctamente');
    }
}
