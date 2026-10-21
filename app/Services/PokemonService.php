<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PokemonService
{
    public function buscar(string $nome): ?array
    {
        $resposta = Http::timeout(10)->get('https://pokeapi.co/api/v2/pokemon/' . strtolower($nome));

        if ($resposta->failed()) {
            return null;
        }

        $dados = $resposta->json();

        return [
            'id' => $dados['id'],
            'nome' => $dados['name'],
            'altura' => $dados['height'],
            'peso' => $dados['weight'],
            'tipos' => collect($dados['types'])->pluck('type.name')->all(),
            'imagem' => $dados['sprites']['front_default'],
        ];
    }
}