<?php

namespace App\Jobs;

use GrahamCampbell\GitHub\GitHubManager;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use OParl\Spec\LiveCopyRepository;

use PharData;

class UpdateLiveCopy extends SpecificationUpdateJob implements SelfHandling, ShouldQueue
{
  use InteractsWithQueue, SerializesModels;

  protected $cloneRefresh = false;

  public function __construct($forceRefresh = false)
  {
    $this->cloneRefresh = $forceRefresh;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(Filesystem $fs, CacheRepository $cache)
  {
    // remove cached livecopy chapters
    $cache->forget('livecopy:chapters');
    $cache->forget('livecopy:html');

    ($this->cloneRefresh)
      ? $this->performCloneRefresh($fs)
      : $this->performPullRefresh();

    exec('make live');
  }

  /**
   * @param Filesystem $fs
   **/
  protected function performCloneRefresh(Filesystem $fs)
  {
    $fs->deleteDirectory(LiveCopyRepository::PATH);
    $gitURL = sprintf("https://github.com/%s/%s", $this->user, $this->repo);

    chdir(storage_path('app'));
    exec("git clone --depth=1 {$gitURL} " . LiveCopyRepository::PATH);
    chdir(storage_path('app/' . LiveCopyRepository::PATH));
  }

  protected function performPullRefresh()
  {
    chdir(storage_path('app/' . LiveCopyRepository::PATH));
    exec('git pull --rebase');
  }
}
