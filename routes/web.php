<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home
Route::get('/', 'EntryController@blog');



// Questions
Route::get('/question/', 'QuestionController@index');

Route::get('/question/create/', 'QuestionController@create');
Route::post('/question/', 'QuestionController@store');

Route::get('/question/{question}', 'QuestionController@show');

Route::get('/question/edit/{question}', 'QuestionController@edit');
Route::put('/question/{question}', 'QuestionController@update');

Route::delete('/question/{question}', 'QuestionController@destroy');



// Entries
Route::get('/entry/', 'EntryController@index');

Route::get('/entry/create/', 'EntryController@create');
Route::post('/entry/', 'EntryController@store');

Route::get('/entry/{entry}', 'EntryController@show');

Route::get('/entry/edit/{entry}', 'EntryController@edit');
Route::put('/entry/{entry}', 'EntryController@update');

Route::delete('/entry/{entry}', 'EntryController@destroy');
