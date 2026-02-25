<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Orden para importar (respetando FKs)
$dumpTableOrder = [
    'users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks',
    'jobs', 'job_batches', 'failed_jobs', 'children', 'actions',
    'point_transactions', 'child_user', 'invitations', 'personal_access_tokens',
];

Artisan::command('db:export-dump {file=storage/app/database-dump.json}', function (string $file) use ($dumpTableOrder) {
    $path = base_path($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $data = [];
    $driver = DB::getDriverName();
    $tables = $driver === 'sqlite'
        ? collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"))->pluck('name')->toArray()
        : collect(DB::select('SHOW TABLES'))->map(fn ($r) => array_values((array) $r)[0])->toArray();
    foreach ($tables as $table) {
        $rows = DB::table($table)->get()->map(fn ($r) => (array) $r)->toArray();
        if (count($rows) > 0) {
            $data[$table] = $rows;
        }
    }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $this->info("Exportado a {$path}");
})->purpose('Exportar datos de la BD a JSON');

Artisan::command('db:import-dump {file=storage/app/database-dump.json}', function (string $file) use ($dumpTableOrder) {
    $path = base_path($file);
    if (!is_file($path)) {
        $this->error("No existe el archivo: {$path}");
        return 1;
    }
    $data = json_decode(file_get_contents($path), true);
    if (!$data) {
        $this->error('JSON inválido');
        return 1;
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    foreach ($dumpTableOrder as $table) {
        if (!isset($data[$table])) {
            continue;
        }
        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        }
        foreach ($data[$table] as $row) {
            DB::table($table)->insert($row);
        }
        $this->line("Importadas " . count($data[$table]) . " filas en {$table}");
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    $this->info('Importación completada.');
})->purpose('Importar datos desde JSON a la BD (MySQL)');
