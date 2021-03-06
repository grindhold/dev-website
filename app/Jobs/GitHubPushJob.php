<?php
/**
 * Created by PhpStorm.
 * User: sgraupner
 * Date: 07/10/2016
 * Time: 18:52
 */

namespace App\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use OParl\Spec\Jobs\SpecificationLiveVersionBuildJob;

/**
 * GitHubPushJob
 *
 * This is just a meta job which dispatches the actual update
 * job required for a given repository. Hook handling from GH
 * is done this way to ensure as quick a reply to the Hookshoot
 * bot as possible.
 *
 * @package App\Jobs
 */
class GitHubPushJob extends Job
{
    use DispatchesJobs;

    /**
     * @var string
     */
    protected $repository = '';

    /**
     * @var array json decoded hook payload
     */
    protected $payload = [];

    public function __construct($repository, array $payload)
    {
        $this->repository = $repository;
        $this->payload = $payload;
    }

    public function handle()
    {
        // fixme: there is an error with the repository status model
        //$this->dispatch(new StoreRepositoryStatusJob($this->repository, $this->payload));

        switch ($this->repository) {
            case 'spec':
                $this->dispatchNow(new SpecificationLiveVersionBuildJob($this->payload));
                break;

            case 'dev-website':
                // TODO: subtree-split lib/Server to OParl/php-reference-server
                break;

            case 'liboparl':
                // TODO: request new buildables from CI?
                break;

            default:
                \Log::error("Cannot process push job for " . $this->repository);
                break;
        }
    }
}
