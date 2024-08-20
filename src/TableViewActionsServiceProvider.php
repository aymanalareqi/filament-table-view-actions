<?php

namespace Alareqi\TableViewActions;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Alareqi\TableViewActions\Commands\TableViewActionsCommand;
use Alareqi\TableViewActions\Livewire\TableViewComponent;
use Livewire\Livewire;

class TableViewActionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('table-view-actions')
            ->hasViews();
    }

    public function bootingPackage()
    {
        Livewire::component('table-view-actions::table-view-component', TableViewComponent::class);
    }
}
