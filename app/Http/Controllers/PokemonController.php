<?php

namespace App\Http\Controllers;

use App\Services\PokemonService;

class PokemonController extends Controller
{
    public function __construct(private PokemonService $service)
    {
    }

    public function show(string $nome)
    {
        $pokemon = $this->service->buscar($nome);

        if (!$pokemon) {
            return response()->json(['mensagem' => 'Pokémon não encontrado'], 404);
        }

        return response()->json($pokemon);
    }
}