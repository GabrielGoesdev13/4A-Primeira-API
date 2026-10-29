<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProdutoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProdutoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ProdutoService::class);
    }

    private function criarProduto(): Produto
    {
        return Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descricao do produto',
            'preco_custo' => 100,
            'preco' => 150,
            'estoque' => 10,
        ]);
    }

    public function test_nao_cria_produto_com_preco_abaixo_da_margem_minima(): void
    {
        try {
            $this->service->create([
                'nome' => 'Produto Invalido',
                'descricao' => 'Preco abaixo da margem',
                'preco_custo' => 100,
                'preco' => 109.99,
                'estoque' => 5,
            ]);

            $this->fail('Deveria lançar ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('preco', $e->errors());
        }

        $this->assertDatabaseCount('produtos', 0);
    }

    public function test_update_altera_o_produto_quando_o_preco_respeita_a_margem(): void
    {
        $produto = $this->criarProduto();

        $atualizado = $this->service->update($produto, ['preco' => 120]);

        $this->assertEquals(120.0, (float) $atualizado->fresh()->preco);
    }

    public function test_update_nao_altera_o_produto_quando_o_preco_viola_a_margem(): void
    {
        $produto = $this->criarProduto();

        try {
            $this->service->update($produto, ['preco' => 105]);

            $this->fail('Deveria lançar ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('preco', $e->errors());
        }

        $this->assertEquals(150.0, (float) $produto->fresh()->preco);
    }

    public function test_delete_remove_o_produto(): void
    {
        $produto = $this->criarProduto();

        $this->service->delete($produto);

        $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
    }
}
