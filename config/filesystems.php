<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'administrations' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'administrations'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'sertifikat' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'sertifikat'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'slip' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'slip'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'quotations' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'quotations'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'sk' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'sk'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'pernyataan' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'pernyataan'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'penerimaan' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'penerimaan'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'perjanjian' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'perjanjian'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'sp' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'sp'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'penilaian' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'penilaian'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'mom' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'mom'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'handover' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'handover'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'garansi' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'garansi'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'cuti' => [
            'driver' => 'local',
            'root' => storage_path('app/public' . DIRECTORY_SEPARATOR . 'cuti'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
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
