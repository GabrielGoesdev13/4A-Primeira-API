<?php

namespace App\Repositories;

use App\Models\Produto;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProdutoRepository implements ProdutoRepositoryInterface
{
    public function search(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return Produto::query()
            ->when($filtros['search'] ?? null,
                fn ($query, $busca) => $query->where('nome', 'ILIKE', "%{$busca}%")
            )
            ->when($filtros['preco_min'] ?? null,
                fn ($query, $minimo) => $query->where('preco', '>=', $minimo)
            )
            ->when($filtros['preco_max'] ?? null,
                fn ($query, $maximo) => $query->where('preco', '<=', $maximo)
            )
            ->when(filter_var($filtros['em_estoque'] ?? false, FILTER_VALIDATE_BOOLEAN),
                fn ($query) => $query->where('estoque', '>', 0)
            )
            ->latest()
            ->paginate($porPagina);
    }

    public function create(array $dados): Produto
    {
        return Produto::create($dados);
    }

    public function update(Produto $produto, array $dados): Produto
    {
        $produto->update($dados);

        return $produto->refresh();
    }

    public function delete(Produto $produto): void
    {
        $produto->delete();
    }
}
