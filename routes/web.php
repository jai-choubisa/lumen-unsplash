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
*/


$router->get('/me', 'UnsplashController@getCurrentUserProfile');

$router->get('/photos', 'UnsplashController@listPhotos');

$router->get('/collections', 'UnsplashController@listCollections');

$router->get('/get-token', 'AuthorizeController@getAccessCode');
