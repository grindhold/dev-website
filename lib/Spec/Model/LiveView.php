<?php
/**
 * Created by PhpStorm.
 * User: sgraupner
 * Date: 24/11/2016
 * Time: 11:21
 */

namespace OParl\Spec\Model;

use Illuminate\Contracts\Filesystem\Filesystem;
use Masterminds\HTML5;

/**
 * Represents a live view of the specification
 *
 * Provides the necessary modifications to turn
 *
 * @package OParl\Spec\Model
 */
class LiveView
{
    protected $fs = null;
    protected $originalHTML = '';
    protected $originalDOM  = null;
    protected $versionInformation = [];

    protected $body = '';
    protected $tableOfContents = '';

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;

        $this->originalHTML = $fs->get('live/live.html');
        $this->versionInformation = json_decode($fs->get('live/version.json'), true);

        $html5 = new HTML5();
        $this->originalDOM = $html5->loadHTML($this->originalHTML);

        $this->parse();
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getTableOfContents()
    {
        return $this->tableOfContents;
    }

    public function getVersionInformation()
    {
        return $this->versionInformation;
    }

    protected function parse()
    {
        // split into table of contents and body
        // rewrite image urls
        // rewrite examples
        // rewrite footnotes
    }
}