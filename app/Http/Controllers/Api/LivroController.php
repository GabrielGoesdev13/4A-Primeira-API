<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LivroResource;
use App\Models\Livro;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function index()
    {
        return LivroResource::collection(Livro::all());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'ano' => 'required|integer|min:0|max:' . date('Y'),
        ]);

        $livro = Livro::create($dados);

        return (new LivroResource($livro))->response()->setStatusCode(201);
    }

    public function show(Livro $livro)
    {
        return new LivroResource($livro);
    }

    public function update(Request $request, Livro $livro)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'autor' => 'sometimes|required|string|max:255',
            'ano' => 'sometimes|required|integer|min:0|max:' . date('Y'),
        ]);

        $livro->update($dados);

        return new LivroResource($livro);
    }

    public function destroy(Livro $livro)
    {
        $livro->delete();

        return response()->noContent();
    }
}