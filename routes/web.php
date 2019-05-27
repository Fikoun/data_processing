<?php
// Route::get('/push_data', 'ApiController@push');

//	COMMON
Route::get('/', 'CommonController@home')->name('home');
Route::get('/home', function () { return redirect('/'); });

Route::get('/contact', 'CommonController@contact')->name('contact');;
Route::get('/documentation', 'CommonController@documentation')->name('documentation');

// 	MEASUREMENT
// Show
Route::get('/measurements', 'MeasurementsController@list')->name('measurements');
Route::get('/measurement/{measurement}', 'MeasurementsController@show')->name('measurement');
// Create
Route::get('/measurements/create', 'MeasurementsController@create')->name('create_measurement');
Route::post('/measurements', 'MeasurementsController@store')->name('store_measurement');
// Edit
Route::get('/measurements/edit/{measurement}', 'MeasurementsController@edit')->name('edit_measurement');
Route::patch('/measurements/{measurement}', 'MeasurementsController@update')->name('update_measurement');
// Delete
Route::get('/measurements/delete/{measurement}', 'MeasurementsController@delete')->name('delete_measurement');
// Export
Route::get('/measurements/export/{measurement}', 'MeasurementsController@export')->name('export_measurement');




Auth::routes();