<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\PersonsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebsitesController;
use Illuminate\Support\Facades\Route;

Route::get('/offers', [OffersController::class, 'get']);
Route::post('/offers/fetch', [OffersController::class, 'fetchNewOffers']);
Route::patch('/offers/{offer}', [OffersController::class, 'update']);

Route::get('/meetings', [MeetingsController::class, 'get']);
Route::post('/meetings', [MeetingsController::class, 'create']);
Route::patch('/meetings/{meeting}', [MeetingsController::class, 'update']);
Route::delete('/meetings/{meeting}', [MeetingsController::class, 'delete']);

Route::get('/persons', [PersonsController::class, 'get']);
Route::post('/persons', [PersonsController::class, 'create']);
Route::patch('/persons/{person}', [PersonsController::class, 'update']);
Route::delete('/persons/{person}', [PersonsController::class, 'delete']);

Route::get('/websites', [WebsitesController::class, 'get']);
Route::post('/websites', [WebsitesController::class, 'create']);
Route::patch('/websites/{website}', [WebsitesController::class, 'update']);
Route::delete('/websites/{website}', [WebsitesController::class, 'delete']);

Route::delete('/database', [DatabaseController::class, 'truncate']);

Route::get('/settings', [SettingsController::class, 'get']);
Route::patch('/settings', [SettingsController::class, 'update']);

Route::get('/logs', [LogsController::class, 'list']);
Route::get('/logs/{filename}', [LogsController::class, 'read']);
