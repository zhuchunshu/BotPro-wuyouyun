<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\wuyouyun\src\Http\Controllers\Setting;


// 设置
Route::get('/', [Setting::class,'index']);
Route::get('/{id}', [Setting::class,'edit']);
Route::put('/{id}', [Setting::class,'update']); 