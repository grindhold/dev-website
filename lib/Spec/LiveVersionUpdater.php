<?php

namespace OParl\Spec;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LiveVersionUpdater
{
    const STATUS_REPOSITORY_MISSING = -2;
    const STATUS_COMMAND_FAILED = -1;

    protected $gitURL = '';
    protected $path = '';
    protected $fs = null;

    public function __construct(Filesystem $fs, $path, $gitURL)
    {
        // make path absolute by assuming it was relative to storage_path('app');
        if (!starts_with($path, DIRECTORY_SEPARATOR)) {
            $path = storage_path("app/{$path}");
        }

        $this->fs = $fs;

        $this->path = $path;
        $this->gitURL = $gitURL;
    }

    /**
     * @return array hash => string(40), last_modified => Carbon
     */
    public function getRepositoryStatus()
    {
        $hash = $this->determineHash();
        $lastModified = $this->determineLastModified();

        return [
            'hash'          => $hash,
            'last_modified' => $lastModified,
        ];
    }

    /**
     * @return string
     **/
    protected function determineHash()
    {
        $exitCode = $this->runCommandInDir(
            'git show HEAD --format="%H" | head -n1',
            $this->path,
            $hash
        );

        if ($exitCode == self::STATUS_COMMAND_FAILED) {
            $hash = '<unknown>';
        } else {
            $hash = trim($hash);
        }

        return $hash;
    }

    /**
     * @param string $cmd
     * @param string $dir
     * @param string $output
     *
     * @return int
     */
    protected function runCommandInDir($cmd, $dir, &$output = null)
    {
        try {
            $process = new Process($cmd, $dir);

            $process->start();
            $process->wait();

            $output = $process->getOutput();

            return $process->getExitCode();
        } catch (ProcessFailedException $e) {
            return self::STATUS_COMMAND_FAILED;
        }
    }

    /**
     * @return Carbon|null
     **/
    protected function determineLastModified()
    {
        try {
            $this->runCommandInDir(
                'git show HEAD --format="%aD" | head -n1',
                $this->path,
                $rfc2822Date
            );

            $rfc2822Date = trim($rfc2822Date);

            $lastModified = Carbon::createFromFormat(Carbon::RFC2822, $rfc2822Date);
        } catch (\Exception $e) {
            $lastModified = null;
        }

        return $lastModified;
    }

    public function updateRepository($forceClone = false)
    {
        if ($forceClone || !$this->repositoryExists()) {
            $retVal = $this->cloneRepository();
        } else {
            $retVal = $this->rebaseRepository();
        }

        return $retVal;
    }

    public function repositoryExists()
    {
        return is_dir($this->path);
    }

    /**
     * Clones the Spec repository.
     *
     * @param bool|false $dryRun
     *
     * @return int negative if dry run, zero if everything went fine, greater than zero in any other case
     **/
    public function cloneRepository($dryRun = false)
    {
        $gitCommand = sprintf('git clone -q --depth=1 %s %s', $this->gitURL, $this->path);

        $retVal = self::STATUS_COMMAND_FAILED;

        if (!$dryRun) {
            exec($gitCommand, $output, $retVal);
        }

        if (!$dryRun && $retVal == 0) {
            $this->makeLiveVersion();
        }

        return $retVal;
    }

    public function makeLiveVersion($dryRun = false)
    {
        if (!$this->repositoryExists()) {
            return self::STATUS_REPOSITORY_MISSING;
        }

        $exitCode = self::STATUS_COMMAND_FAILED;

        if (!$dryRun) {
            $exitCode = $this->runCommandInDir('make live', $this->path);
        }

        return $exitCode;
    }

    /**
     * Rebases spec repository to current HEAD.
     *
     * @param bool|false $dryRun
     *
     * @return int -1 if dry run, 0 if everything went fine, >0 in any other case, -2 if the repository does not exist
     **/
    public function rebaseRepository($dryRun = false)
    {
        $gitCommand = sprintf('git pull -q --rebase origin master');

        if (!$this->repositoryExists()) {
            return self::STATUS_REPOSITORY_MISSING;
        }

        $retVal = self::STATUS_COMMAND_FAILED;

        if (!$dryRun) {
            $oldDir = getcwd();

            chdir($this->path);

            exec($gitCommand, $output, $retVal);

            chdir($oldDir);
        }

        if (!$dryRun && $retVal == 0) {
            $this->makeLiveVersion();
        }

        return $retVal;
    }

    public function deleteRepository()
    {
        if (!$this->repositoryExists()) {
            return true;
        }

        if (!$this->fs->deleteDirectory($this->path)) {
            // try deleting with the absolute path
            try {
                $dir = new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS);
                $last = $this->path;
            } catch (\UnexpectedValueException $e) {
                $dir = new \RecursiveDirectoryIterator(storage_path("app/{$this->path}"),
                    \FilesystemIterator::SKIP_DOTS);
                $last = storage_path("app/{$this->path}");
            }

            $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($it as $path) {
                /* @var $path \SplFileInfo */
                if ($path->isFile()) {
                    unlink($path);
                } elseif ($path->isDir()) {
                    rmdir($path);
                }
            }

            rmdir($last);

            if (!is_dir($last)) {
                return true;
            }
        }

        return false;
    }

    public function cleanLiveVersion($dryRun = false)
    {
        if (!$this->repositoryExists()) {
            return self::STATUS_REPOSITORY_MISSING;
        }

        $exitCode = self::STATUS_COMMAND_FAILED;

        if (!$dryRun) {
            $exitCode = $this->runCommandInDir('make clean', $this->path);
        }

        return $exitCode;
    }
}
