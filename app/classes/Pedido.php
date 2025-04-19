<?php

namespace app\classes;

/*
Para a venda desses produtos é desejado armazenar as variações vendidas e seu
preço de venda, o endereço de entrega, o cliente, o valor total, o valor de frete,
descontos e a forma de pagamento, podendo ser PIX, Boleto ou Cartão(1x). Pagamentos
via PIX possuem um desconto fixo de 10% no valor dos itens com o frete. Para calcular
o valor total, deve-se somar todos os preços das variações dos produtos com o valor
do frete e subtrair pelo desconto da forma de pagamento, se tiver.
*/

class Pedido extends Model
{
    private int $id = 0;
    private ?Cliente $cliente = null;
    private ?Endereco $enderecoEntrega = null;
    private int $status = 0;
    private int $formaDePagamento = 0;
    // private float $valorTotal = 0.0;
    private float $valorFrete = 0.0;
    private array $descontos = [];
    private array $itensPedido = [];

    public function calcularValorTotal()
    {
        return 0.0;
    }

    public function emArray(): array
    {
        $data = [];
        array_push($data, 'a');
        return $data;
    }
}
