<?php namespace EFrane\Buildkite\Clients;

use EFrane\Buildkite\BuildkiteException;
use Guzzle\Http\Client;

abstract class AbstractClient
{
  protected $apiVersion = 'v1';

  protected $baseURL = 'https://api.buildkite.com';

  protected $guzzleClient = null;

  protected $organization = null;
  protected $project = null;

  protected $token = null;

  public function __construct($token, $organizationAndProject = null)
  {
    $this->token = $token;

    $organizationAndProject = $this->validateInput($organizationAndProject);
    if (strpos($organizationAndProject, '/') > 0)
    {
      list($this->organization, $this->project) = explode('/', $organizationAndProject);
    } else
    {
      $this->organization = $organizationAndProject;
    }

    $this->guzzleClient = new Client($this->getBaseURL(), []);
  }

  private function getBaseURL()
  {
    return $this->baseURL . '/' . $this->apiVersion . '/';
  }

  protected function prepareURL($path, array $arguments)
  {
    $arguments = implode('&', $arguments);
    return sprintf('/%s/%s?%s', $this->apiVersion, $path, $arguments);
  }

  protected function request($method, $uri, $headers = null, $body = null, $options = [])
  {
    $request = $this->guzzleClient->createRequest($method, $uri, $headers, $body, $options);
    $request->getQuery()->add('access_token', $this->token);

    print($request);

    return $request->send()->json();
  }

  protected function validateInput($id, $type = 'string')
  {
    if (is_null($id)) return null;

    $validationMethod = sprintf('is_%s', $type);
    if (!call_user_func($validationMethod, $id))
      throw new BuildkiteException(sprintf("ID must be %s", ucfirst($type)));

    return $id;
  }

  // TODO: pagination methods
}