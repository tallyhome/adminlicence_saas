<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [

    /*
    |--------------------------------------------------------------------------
    | Telescope Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Telescope will be accessible from. If the
    | setting is null, Telescope will reside under the same domain as the
    | application. Otherwise, this value will be used as the subdomain.
    |
    */

    'domain' => env('TELESCOPE_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Telescope Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Telescope will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => env('TELESCOPE_PATH', 'telescope'),

    /*
    |--------------------------------------------------------------------------
    | Telescope Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the storage driver that will
    | be used to store Telescope's data. In addition, you may set any
    | custom options as needed by the particular driver you choose.
    |
    */

    'driver' => env('TELESCOPE_DRIVER', 'database'),

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Telescope Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable all Telescope watchers regardless
    | of their individual configuration, which simply provides a single
    | and convenient way to enable or disable Telescope data storage.
    |
    */

    'enabled' => env('TELESCOPE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Telescope Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Telescope route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => [
        'web',
        Authorize::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed / Ignored Paths & Commands
    |--------------------------------------------------------------------------
    |
    | The following array lists the URI paths and Artisan commands that will
    | not be watched by Telescope. In addition to this list, some Laravel
    | commands, like migrations and queue commands, are always ignored.
    |
    */

    'ignore_paths' => [
        'nova-api*',
    ],

    'ignore_commands' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Telescope Watchers
    |--------------------------------------------------------------------------
    |
    | The following array lists the "watchers" that will be registered with
    | Telescope. The watchers gather the application's profile data when
    | a request or task is executed. Feel free to customize this list.
    |
    */

    'watchers' => [
        Watchers\BatchWatcher::class => env('TELESCOPE_BATCH_WATCHER', false),

        Watchers\CacheWatcher::class => [
            'enabled' => env('TELESCOPE_CACHE_WATCHER', false),
            'hidden' => [],
        ],

        Watchers\CommandWatcher::class => [
            'enabled' => env('TELESCOPE_COMMAND_WATCHER', false),
            'ignore' => [],
        ],

        Watchers\DumpWatcher::class => [
            'enabled' => env('TELESCOPE_DUMP_WATCHER', false),
            'always' => env('TELESCOPE_DUMP_WATCHER_ALWAYS', false),
        ],

        Watchers\EventWatcher::class => [
            'enabled' => env('TELESCOPE_EVENT_WATCHER', false),
            'ignore' => [],
        ],

        Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', false),

        Watchers\GateWatcher::class => [
            'enabled' => env('TELESCOPE_GATE_WATCHER', false),
            'ignore_abilities' => [],
            'ignore_packages' => true,
            'ignore_paths' => [],
        ],

        Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', false),

        Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', false),

        Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', false),

        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_MODEL_WATCHER', false),
            'events' => ['eloquent.*'],
            'hydrations' => true,
        ],

        Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', false),

        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_QUERY_WATCHER', false),
            'ignore_packages' => true,
            'ignore_paths' => [],
            'slow' => 100,
        ],

        Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', false),

        Watchers\RequestWatcher::class => [
            'enabled' => env('TELESCOPE_REQUEST_WATCHER', false),
            'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
            'ignore_http_methods' => [],
            'ignore_status_codes' => [],
        ],

        Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', false),

        Watchers\ViewWatcher::class => env('TELESCOPE_VIEW_WATCHER', false),
    ],
];
