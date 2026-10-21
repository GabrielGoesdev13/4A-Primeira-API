<?php

use App\Http\Controllers\Api\ProdutoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivroController;

Route::apiResource('/produtos', ProdutoController::class);

Route::apiResource('livros', LivroController::class);
/*
GET     /api/produtos
POST    /api/produtos
SHOW    /api/produtos/id
PUT     /api/produtos/id
*/