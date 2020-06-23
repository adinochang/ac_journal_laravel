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
Route::get('/', 'EntryController@blog')->name('home');



// Questions
Route::get('/question/', 'QuestionController@index')->name('question.index');

Route::get('/question/create/', 'QuestionController@create')->name('question.create');
Route::post('/question/', 'QuestionController@store')->name('question.store');

Route::get('/question/{question}', 'QuestionController@show')->name('question.show');

Route::get('/question/edit/{question}', 'QuestionController@edit')->name('question.edit');
Route::put('/question/{question}', 'QuestionController@update')->name('question.update');

Route::delete('/question/{question}', 'QuestionController@destroy')->name('question.destroy');



// Entries
Route::get('/entry/', 'EntryController@index')->name('entry.index');

Route::get('/entry/create/', 'EntryController@create')->name('entry.create');
Route::post('/entry/', 'EntryController@store')->name('entry.store');

Route::get('/entry/{entry}', 'EntryController@show')->name('entry.show');

Route::get('/entry/edit/{entry}', 'EntryController@edit')->name('entry.edit');
Route::put('/entry/{entry}', 'EntryController@update')->name('entry.update');

Route::delete('/entry/{entry}', 'EntryController@destroy')->name('entry.destroy');



// Reports
Route::get('/report', 'EntryController@report')->name('entry.report');
