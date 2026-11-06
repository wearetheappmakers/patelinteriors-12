<?php

// Route::get('/home', function () {
//     $users[] = Auth::user();
//     $users[] = Auth::guard()->user();
//     $users[] = Auth::guard('user')->user();

//     return view('user.home');
// })->name('home');



Route::get('/home', 'HomeController@index')->name('home');