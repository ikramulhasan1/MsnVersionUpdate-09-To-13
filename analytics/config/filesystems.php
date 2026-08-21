<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        /*
        |----------------------------------------------------------------
        | Image Everything (Phase S1) — temporary, per-job image storage
        |----------------------------------------------------------------
        |
        | A DEDICATED disk, not just reusing 'local' above, specifically
        | so every image-processing path in the codebase reads
        | Storage::disk('private-images') and is instantly recognizable
        | as "this file is temporary and will be auto-deleted" — never
        | mixed in with whatever else 'local' might hold for unrelated
        | purposes. NEVER served publicly (no 'url'/'serve' key here,
        | unlike 'local' above) — every real file under this root sits
        | at storage/app/private/image-processing/{job-uuid}/... and is
        | only ever reachable through an authenticated, ownership-checked
        | controller action, never a direct URL. See
        | App\Console\Commands\CleanupExpiredImageJobsCommand for what
        | actually deletes files from here once a job's own expires_at
        | passes.
        */
        'private-images' => [
            'driver' => 'local',
            'root' => storage_path('app/private/image-processing'),
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];