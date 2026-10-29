<?php

namespace Tests\Feature;

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiProdutoTest extends TestCase
{
    
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_cria_produto_com_margem_correta(): void
    {
        $this->postJson('/api/produtos', [
            'nome' => 'Lajota',
            'preco_custo' => 100,
            'preco' => 110,
        ])->assertCreated();

        $this->assertDatabaseHas('produtos', ['nome' => 'Lajota']);
    }

    public function test_filtra_produtos_por_faixa_de_preco(): void
    {
        Produto::create(['nome' => 'Barato', 'preco_custo' => 10, 'preco' => 20, 'estoque' => 1]);
        Produto::create(['nome' => 'Caro', 'preco_custo' => 100, 'preco' => 200, 'estoque' => 1]);

        $this->getJson('/api/produtos?preco_min=100&preco_max=300')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nome', 'Caro');
    }

    public function test_filtra_somente_produtos_em_estoque(): void
    {
        Produto::create(['nome' => 'Com estoque', 'preco_custo' => 10, 'preco' => 20, 'estoque' => 5]);
        Produto::create(['nome' => 'Sem estoque', 'preco_custo' => 10, 'preco' => 20, 'estoque' => 0]);

        $this->getJson('/api/produtos?em_estoque=true')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nome', 'Com estoque');
    }

    public function test_update_via_api_respeita_a_margem_minima(): void
    {
        $produto = Produto::create(['nome' => 'Lajota', 'preco_custo' => 100, 'preco' => 150, 'estoque' => 1]);

        $this->putJson("/api/produtos/{$produto->id}", ['preco' => 105])
            ->assertUnprocessable();

        $this->assertEquals(150.0, (float) $produto->fresh()->preco);
    }

    public function test_delete_via_api_retorna_204(): void
    {
        $produto = Produto::create(['nome' => 'Lajota', 'preco_custo' => 100, 'preco' => 150, 'estoque' => 1]);

        $this->deleteJson("/api/produtos/{$produto->id}")->assertNoContent();

        $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
    }
}
