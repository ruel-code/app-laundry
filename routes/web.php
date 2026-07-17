<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/santri', function () {
    return view('santri.index');
})->name('santri.index');

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

Route::get('/orders/create', function () {
    return view('orders.create');
})->name('orders.create');

Route::get('/orders/{order}', function () {
    return view('orders.show');
})->name('orders.show');

Route::get('/reports', function () {
    return view('reports.index');
})->name('reports.index');
