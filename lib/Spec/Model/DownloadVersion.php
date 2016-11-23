<?php
/**
 * Created by PhpStorm.
 * User: sgraupner
 * Date: 23/11/2016
 * Time: 10:26
 */

namespace OParl\Spec\Model;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DownloadVersion
{
    protected $fs = null;

    protected $path = '';

    /**
     * @var Collection $files available files for the identifier and version
     */
    protected $files = null;

    public function __construct(Filesystem $fs, $identifier, $version)
    {
        $this->fs = $fs;
        $this->path = "downloads/{$identifier}/{$version}";

        if (!$fs->exists($this->path)) {
            throw new FileNotFoundException("No downloads available for identifier {$identifier}");
        }

        $this->files = collect($fs->files("downloads/{$identifier}/{$version}"))
            ->map(function ($filename) use ($fs) {
                return new Download($fs, $filename);
            });
    }

    /**
     * Return the number of days this download version exists
     * @return int
     */
    public function getAge()
    {
        $fileCTime = Carbon::createFromTimestamp(filectime(storage_path("app/{$this->path}")));

        return $fileCTime->diffInDays();
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFileForExtension($extension)
    {
        $file = $this->files->filter(function (Download $file) use ($extension) {
            return ends_with($file->getFilename(), $extension);
        });

        if (is_null($file)) {
            throw new FileNotFoundException("No file found for extension {$extension}");
        }

        return $file;
    }
}