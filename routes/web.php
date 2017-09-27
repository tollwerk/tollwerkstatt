<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
| -----------------------------------------------------------------------
| The router allows you to register routes that respond to any HTTP verb:
| -----------------------------------------------------------------------
|
| $app->get($uri, $callback);
| $app->post($uri, $callback);
| $app->put($uri, $callback);
| $app->patch($uri, $callback);
| $app->delete($uri, $callback);
| $app->options($uri, $callback);
|
 */

// We need both get- and post-routes because the index action
// contains the contact form with method="post"
$app->get('/', 'IndexController@indexAction');
$app->post('/', 'IndexController@indexAction');

// Ajax feed of calDav events for fullcalendar.js
$app->get('/feed', 'CalendarController@jsonFeedAction');

/*
// An example group of routes
$app->group(['prefix' => 'calendar'], function () use ($app) {
    $app->get('/', 'CalendarController@showAction');
    $app->get('/foo', 'CalendarController@fooAction');
    $app->get('/bar', 'CalendarController@BarAction');
});
*/




