<?php

namespace OParl\Spec;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use OParl\Spec\Commands\InitializeCommand;
use OParl\Spec\Commands\InitializeSchemaCommand;
use OParl\Spec\Commands\UpdateDownloadablesCommand;
use OParl\Spec\Commands\UpdateSchemaCommand;
use OParl\Spec\Commands\UpdateSpecificationCommand;

class SpecServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            UpdateDownloadablesCommand::class,
            UpdateSpecificationCommand::class,
            UpdateSchemaCommand::class,
            InitializeSchemaCommand::class,
            InitializeCommand::class,
        ]);
    }
}
