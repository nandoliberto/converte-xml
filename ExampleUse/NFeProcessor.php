<?php

require __DIR__ . '/vendor/autoload.php';

namespace ExampleUse;

use ConverteCTeNFe\ConverteXml;
use Exception;
use SimpleXMLElement;

/**
 * Classe responsável por carregar e exibir informações de uma NFe
 */
class NFeProcessor
{
    private ConverteXml $converter;
    private ?SimpleXMLElement $xml = null;

    public function __construct(ConverteXml $converter)
    {
        $this->converter = $converter;
    }

    /**
     * Carrega o XML de uma NFe e armazena o objeto internamente
     */
    public function carregar(string $xmlPath): void
    {
        $this->xml = $this->converter->xmlNFeToObject($xmlPath);
    }

    /**
     * Retorna a chave da NFe
     */
    public function getChave(): string
    {
        return (string)($this->xml->NFe->infNFe['Id'] ?? '');
    }

    /**
     * Retorna dados do emitente
     */
    public function getEmitente(): array
    {
        return [
            'nome' => (string)($this->xml->NFe->infNFe->emit->xNome ?? ''),
            'cnpj' => (string)($this->xml->NFe->infNFe->emit->CNPJ ?? ''),
        ];
    }

    /**
     * Retorna dados do destinatário
     */
    public function getDestinatario(): array
    {
        return [
            'nome' => (string)($this->xml->NFe->infNFe->dest->xNome ?? ''),
            'cnpj' => (string)($this->xml->NFe->infNFe->dest->CNPJ ?? ''),
        ];
    }

    /**
     * Retorna o valor total da nota
     */
    public function getValorTotal(): string
    {
        return (string)($this->xml->NFe->infNFe->total->ICMSTot->vNF ?? '0.00');
    }

    /**
     * Retorna a lista de produtos da nota
     */
    public function getProdutos(): array
    {
        $produtos = [];

        foreach ($this->xml->NFe->infNFe->det as $det) {
            $produtos[] = [
                'codigo' => (string)$det->prod->cProd,
                'descricao' => (string)$det->prod->xProd,
                'quantidade' => (string)$det->prod->qCom,
                'valor_unitario' => (string)$det->prod->vUnCom,
                'valor_total' => (string)$det->prod->vProd,
            ];
        }

        return $produtos;
    }

    /**
     * Exibe as informações formatadas
     */
    public function exibirResumo(): void
    {
        echo "✅ XML convertido com sucesso!" . PHP_EOL;
        echo str_repeat("=", 50) . PHP_EOL;

        echo "Chave da NFe: " . $this->getChave() . PHP_EOL;
        echo "Emitente: " . $this->getEmitente()['nome'] . PHP_EOL;
        echo "CNPJ Emitente: " . $this->getEmitente()['cnpj'] . PHP_EOL;
        echo "Destinatário: " . $this->getDestinatario()['nome'] . PHP_EOL;
        echo "Valor Total: R$ " . $this->getValorTotal() . PHP_EOL;

        echo str_repeat("-", 50) . PHP_EOL;
        echo "Produtos:" . PHP_EOL;

        foreach ($this->getProdutos() as $produto) {
            echo "- {$produto['descricao']} ({$produto['quantidade']} x R$ {$produto['valor_unitario']}) = R$ {$produto['valor_total']}" . PHP_EOL;
        }
    }
}

// ==========================
// Execução do exemplo
// ==========================

try {
    $processor = new NFeProcessor(new ConverteXml());
    $processor->carregar(__DIR__ . '/file.xml');
    $processor->exibirResumo();
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . PHP_EOL;
}
