<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Filament navigation groups
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make('Livestock Management')
                    ->icon('heroicon-o-heart')
                    ->collapsible(),
                NavigationGroup::make('Breeding Management')
                    ->icon('heroicon-o-user-plus')
                    ->collapsible(),
                NavigationGroup::make('Operations')
                    ->icon('heroicon-o-cog')
                    ->collapsible(),
                NavigationGroup::make('Financial Management')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(),
                NavigationGroup::make('Reports')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),
            ]);
        });
    }
}
