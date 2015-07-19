<?php namespace OParl\Spec;

use \ArrayAccess;
use \Iterator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Carbon\Carbon;
use GrahamCampbell\GitHub\GitHubManager;

/**
 * Version Repository
 *
 * This repository manages all accessible Spec versions.
 *
 * @package OParl\Spec
 **/
class VersionRepository implements ArrayAccess, Iterator
{
  /**
   * The static json storing the available versions
   */
  const REPOSITORY_FILE = 'versions.json';

  /**
   * @var array Version objects
   **/
  protected $versions = [];

  /**
   * @var int Currently selected version (\Iterator)
   **/
  private $current = 0;

  /**
   * Loads the available versions from the repository file.
   *
   * @param Filesystem $fs
   */
  public function __construct(Filesystem $fs)
  {
    $versions = collect(json_decode($fs->get(static::REPOSITORY_FILE), true));
    $this->versions = $versions->map(function ($version) {
      return new Version($version['sha'], $version['message'], $version['date']);
    })->all();
  }

  /**
   * Creates or updates the repository file.
   *
   * @param Filesystem $fs
   * @param array $ghVersions
   **/
  public static function update(Filesystem $fs, GitHubManager $gh, $user, $repo)
  {
    $commits = $gh->repo()->commits()->all($user, $repo, []);

    $versions = collect($commits)->map(function ($version) {
      return new Version(
        $version['sha'],
        explode("\n", $version['commit']['message'])[0],
        $version['commit']['committer']['date']
      );
    })->filter(function(Version $version) {
      return $version->getDate() >= Carbon::createFromDate(2015, 7, 13);
    });

    $fs->put(static::REPOSITORY_FILE, json_encode($versions, JSON_PRETTY_PRINT));
  }

  /**
   * @return Version latest version object
   **/
  public function latest()
  {
    return $this->versions[0];
  }

  /**
   * @param $hash
   * @return boolean Is the version already available?
   **/
  public function isAvailable($hash)
  {
    foreach ($this->versions as $version)
      if ($version->getHash(7) === $hash) return $version->isAvailable();
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current()
  {
    return $this->versions[$this->current];
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next()
  {
    $this->current += 1;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   */
  public function key()
  {
    return $this->current;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   */
  public function valid()
  {
    return count($this->versions) < $this->current;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind()
  {
    $this->current = 0;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset)
  {
    return isset($this->versions[$offset]);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset)
  {
    return $this->versions[$offset];
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   */
  public function offsetSet($offset, $value)
  {
    $this->versions[$offset] = $value;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   */
  public function offsetUnset($offset)
  {
    unset($this->versions[$offset]);
  }
}