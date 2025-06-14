<?php

return [
    'panels' => [
        'admin' => [
            'id' => 'admin',
            'path' => '/admin',
            'login' => \Filament\Http\Livewire\Auth\Login::class,
            'registration' => null,
            'password_reset' => null,
            'email_verification' => null,
            'default' => true,
            'domain' => null,
            'middleware' => [
                'web',
                \Filament\Http\Middleware\Authenticate::class,
            ],
            'auth_guard' => 'web',
            'auth_password_broker' => null,
            'database_notifications' => [
                'enabled' => false,
                'polling_interval' => null,
            ],
            'widgets' => [
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\RevenueChart::class,
                \App\Filament\Widgets\ExpenseBreakdown::class,
                \App\Filament\Widgets\UpcomingDeliveries::class,
            ],
            'pages' => [
                'namespace' => 'App\\Filament\\Pages',
                'path' => app_path('Filament/Pages'),
                'register' => [
                    // Custom pages
                ],
            ],
            'resources' => [
                'namespace' => 'App\\Filament\\Resources',
                'path' => app_path('Filament/Resources'),
                'register' => [
                    // Custom resources will be auto-discovered
                ],
            ],
            'livewire' => [
                'namespace' => 'App\\Filament\\Livewire',
                'path' => app_path('Filament/Livewire'),
            ],
            'dark_mode' => false,
            'database_notifications' => [
                'enabled' => false,
                'polling_interval' => null,
            ],
            'breadcrumbs' => true,
            'navigation' => [
                'groups' => [
                    'Manajemen Ternak' => [
                        'icon' => 'heroicon-o-heart',
                        'collapsible' => true,
                    ],
                    'Manajemen Breeding' => [
                        'icon' => 'heroicon-o-user-plus',
                        'collapsible' => true,
                    ],
                    'Operasional' => [
                        'icon' => 'heroicon-o-cog',
                        'collapsible' => true,
                    ],
                    'Manajemen Keuangan' => [
                        'icon' => 'heroicon-o-banknotes',
                        'collapsible' => true,
                    ],
                    'Laporan' => [
                        'icon' => 'heroicon-o-chart-bar',
                        'collapsible' => true,
                    ],
                ],
            ],
            'max_content_width' => null,
            'favicon' => null,
            'spa' => false,
            'unsaved_changes_alerts' => true,
        ],
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    'assets_path' => null,
    'cache_path' => base_path('bootstrap/cache/filament'),
    'livewire_loading_delay' => 'default',
];
