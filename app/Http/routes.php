<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* @var Illuminate\Routing\Router $router */

/**
 * Route group for dev.oparl.org.
 *
 * This route group contains all the endpoints necessary to navigate through
 * dev.oparl.org except the api/ section which is loaded in via the
 * OParl\Server\ServerServiceProvider.
 */
$router->group(['domain' => 'dev.' . config('app.url')], function () use ($router) {
    $router->get('/', ['uses' => 'DevelopersController@index', 'as' => 'developers.index']);

    // Specification
    $router->get('/spezifikation', ['uses' => 'SpecificationController@index', 'as' => 'specification.index']);
    $router->get('/spezifikation.md', ['uses' => 'SpecificationController@raw', 'as' => 'specification.raw']);
    $router->get('/spezifikation/images/',
        ['uses' => 'SpecificationController@imageIndex', 'as' => 'specification.images']);
    $router->get('/spezifikation/images/{image}.png', 'SpecificationController@image')
        ->name('specification.image')
        ->where('image', '[a-zA-Z0-9-._]+');

    // Downloads
    $router->get('/downloads/')
        ->name('downloads.index')
        ->uses('DownloadsController@index');

    $router->get('/downloads/spezifikation-stable.{format}')
        ->name('downloads.specification.stable')
        ->uses('DownloadsController@stableSpecification');

    $router->get('/downloads/spezifikation-{version}.{format}')
        ->name('downloads.specification')
        ->uses('DownloadsController@specification')
        ->where('version', '[a-z0-9.]{5,32}');

    $router->get('/contact', ['uses' => 'DevelopersController@contact', 'as' => 'contact.index']);

    $router->get('/_/bk/', 'Hooks\BuildkiteController@index')->name('hooks.bk.index');

    $router->get('/_/gh/', ['uses' => 'Hooks\GitHubHooksController@index', 'as' => 'hooks.gh.index']);
    $router->post('/_/gh/', ['uses' => 'Hooks\GitHubHooksController@index', 'as' => 'hooks.gh.index.post']);
    $router->get('/_/gh/push/[a-zA-Z.]+', ['uses' => 'Hooks\GitHubHooksController@index', 'as' => 'hooks.gh.push.get']);
    $router->post('/_/gh/push/{repository}', ['uses' => 'Hooks\GitHubHooksController@push', 'as' => 'hooks.gh.push'])
        ->where('repository', '[a-z-]+');

    // Dummy file controller for API demo
    $router->pattern('filename', '[a-z0-9]{3,8}');
    $router->get('/demo/{filename}.pdf', ['uses' => 'DummyFileController@show', 'as' => 'dummyfile.show']);
    $router->get('/demo/f/{filename}.pdf', ['uses' => 'DummyFileController@serve', 'as' => 'dummyfile.serve']);
});

/*
 * Route group for spec.oparl.org
 *
 * This route group provides an easy to remember redirect to the
 * latest specification version as spec.oparl.org
 *
 * Additionally, short links to downloads of the stable and
 * the latest unstable specification versions are provided.
 */
$router->group(['domain' => 'spec.' . config('app.url')], function () use ($router) {
    $router->any('/')->uses('SpecificationController@redirectToIndex');
    $router->get('/1.0')->uses('SpecificationController@redirectToVersion')->where('version', '1.0');

    $router->get('/1.0.{format}')->uses('DownloadsController@stableSpecification');
    $router->get('/latest.{format}')->uses('DownloadsController@latestSpecification');
});

/*
 * Route group for schema.oparl.org
 *
 * This route group defines access to the versioned JSONSchema of the OParl Specification
 * which is accessible at schema.oparl.org/{version}/{entity}.json
 *
 * Direct access to schema.oparl.org is redirected to dev.oparl.org
 */
$router->group(['domain' => 'schema.' . config('app.url')], function () use ($router) {
    $router->pattern('version', '(1\.0|1\.1|master)');

    $router->get('/')->uses('DevelopersController@redirectToIndex');

    $router->get('/{version}/')
        ->name('schema.list')
        ->uses('SchemaController@listSchemaVersion');

    $router->get('/{version}/{entity}')
        ->name('schema.get')
        ->uses('SchemaController@getSchema')
        ->where('entity', '[A-Za-z]+');
});
