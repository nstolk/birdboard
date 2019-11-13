<?php



Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
//    Route::get('/projects', 'ProjectsController@index');
//    Route::get('/projects/create', 'ProjectsController@create');
//    Route::get('/projects/{project}', 'ProjectsController@show');
//    Route::get('/projects/{project}/edit', 'ProjectsController@edit');
//    Route::patch('/projects/{project}', 'ProjectsController@update');
//    Route::post('/projects', 'ProjectsController@store');
//    Route::delete('/projects/{project}', 'ProjectsController@destroy');

    Route::resource('projects', 'ProjectsController');


    Route::post('/projects/{project}/tasks', 'ProjectTasksController@store');
    Route::patch('/projects/{project}/tasks/{task}', 'ProjectTasksController@update');



    Route::get('/home', 'HomeController@index')->name('home');
});

Auth::routes();

