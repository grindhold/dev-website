<?php namespace OParl\Spec;

use Illuminate\Contracts\Filesystem\Filesystem;
use \Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class SpecServiceProvider extends IlluminateServiceProvider
{
    /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
      $this->app->bind('LiveCopyRepository', LiveCopyRepository::class, true);

      $this->app->bind(LiveCopyLoader::class, function () {
          $fs = app(Filesystem::class);
          return new LiveCopyLoader($fs, LiveCopyRepository::PATH);
      });

      $this->app->bind('SpecificationBuildRepository', BuildRepository::class, true);

      $this->app->singleton(
      'oparl.specification.commands.delete_builds',
      Commands\DeleteSpecificationBuildsCommand::class
    );

      $this->app->singleton(
      'oparl.specification.commands.update_builds_gh',
      Commands\UpdateSpecificationBuildDataFromGitHubCommand::class
    );

      $this->app->singleton(
      'oparl.specification.commands.request_build_bk',
      Commands\RequestSpecificationBuildCommand::class
    );

      $this->app->singleton(
      'oparl.specification.commands.list_builds',
      Commands\ListSpecificationBuildsCommand::class
    );

      $this->app->singleton(
      'oparl.specification.commands.update_live_copy',
      Commands\UpdateLiveCopyCommand::class
    );

      $this->commands([
      'oparl.specification.commands.delete_builds',
      'oparl.specification.commands.update_builds_gh',
      'oparl.specification.commands.request_build_bk',
      'oparl.specification.commands.list_builds',
      'oparl.specification.commands.update_live_copy',
    ]);
  }

    public function provides()
    {
        return [
      'oparl.specification.commands.delete_builds',
      'oparl.specification.commands.update_builds_gh',
      'oparl.specification.commands.request_build_bk',
      'oparl.specification.commands.list_builds',
      'oparl.specification.commands.update_live_copy',
    ];
    }
}
