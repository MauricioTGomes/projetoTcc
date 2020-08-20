<html>
<head>
    <title>Men Store MS - Pedido</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{public_path('css/impressao_pdf.css')}}">
    <link rel="stylesheet" href="{{public_path('css/bootstrap.min.css')}}">
</head>

<body class="documento impressao font-size-11">

<div class="row">
    <div class="col-xs-12 relatorio titulo">PEDIDO N° {{ $pedido->numero }}</div>
</div>

<div class="row">
    <div class="col-xs-7"><b>Data e hora de emissão: {{(new \Carbon\Carbon($pedido->created_at))->format('d/m/Y h:m:s')}}</b></div>
    <div class="col-xs-5 text-right">Vendedor: {{auth()->user()->name}}</div>
</div>

<hr>

<div class="row">
    <div class="col-xs-12">
        <label class="labelTitulo">Cliente</label> <br>
        @if(!is_null($pedido->pessoa))
            <div class="row text-left cabecalho empresa">
                <div class="col-xs-7">Nome: {{ $pedido->pessoa->nomeCompleto() }}</div>
                <div class="col-xs-5">CPF / CNPJ: {{$pedido->pessoa->cpfCnpj()}}</div>
                <div class="col-xs-7">Endereço: {{$pedido->pessoa->endereco}}, {{$pedido->pessoa->endereco_nro}}</div>
                <div class="col-xs-5">{{$pedido->pessoa->cidade->nome}} - {{$pedido->pessoa->cidade->estado->uf}} -
                    CEP: {{$pedido->pessoa->cep}}</div>
                <div class="col-xs-5">E-mail: {{$pedido->pessoa->email}}</div>
            </div>
        @else
            Nome: não informado
        @endif
    </div>
</div>

@if($pedido->itens->count() > 0)
    @php
        $total = 0;
    @endphp
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <label class="labelTitulo">Produtos / Serviços</label>
            <table class="tabelaProdutos table table-condensed">
                <thead>
                <tr>
                    <th width="5%">Item</th>
                    <th width="15%">Cod. barras</th>
                    <th width="32%">Nome</th>
                    <th width="12%" class="text-center">Quantidade</th>
                    <th width="12%" class="text-center">Unitário</th>
                    <th width="12%" class="text-center">Desconto</th>
                    <th width="12%" class="text-center">Valor total</th>
                </tr>
                </thead>

                <tbody>

                @foreach($pedido->itens as $index => $item)
                    @php
                        $total += $item->valor_total;
                    @endphp
                    <tr>
                        <td class="text-center">{{$index + 1 }}</td>
                        <td>{{$item->produto->cod_barras}}</td>
                        <td>{{$item->produto->apelido_produto}}</td>
                        <td class="text-center">{{formatValueForUser($item->quantidade)}}</td>
                        <td class="text-center">{{ formatValueForUser($item->valor_unitario) }}</td>
                        <td class="text-center">{{ formatValueForUser($item->valor_desconto) }}</td>
                        <td class="text-center">{{formatValueForUser($item->valor_total)}}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="4"></td>
                    <td class="text-center" colspan="2"><b>Totalizador</b></td>
                    <td class="text-center"><b>R$ {{formatValueForUser($total)}}</b></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
@endif

@if($pedido->faturado)
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <label class="labelTitulo">Pagamentos</label>
            <table class="tabelaFormasPagamento table table-condensed">
                <thead>
                <tr>
                    <th width="23%">Forma</th>
                    <th width="18%" style="text-align: center;">Valor</th>
                    <th width="59%">Observação</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>{{is_null($pedido->conta) ? "À vista" : "À prazo"}}</td>
                    <td align="center">R$ {{formatValueForUser($pedido->valor_total)}}</td>
                    <td>
                        @if($pedido->conta != null && $pedido->conta->parcelas != null)
                            {{-- Mostra as parcelas da conta à prazo --}}
                            @foreach($pedido->conta->parcelas as $index=>$parcela)
                                @if($index > 0)
                                    @if($index % 2 != 0) &nbsp;
                                    /  &nbsp;
                                    @else <BR> @endif
                                @endif
                                {{$parcela->nro_parcela}} &nbsp;
                                &nbsp;
                                &nbsp;
                                {{$parcela->data_vencimento}} &nbsp;
                                &nbsp;
                                &nbsp;
                                R$ {{formatValueForUser($parcela->valor)}}
                            @endforeach
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4 text-center"><b>Produtos / Serviços R$ {{formatValueForUser($pedido->valor_total)}}</b></div>
        <div class="col-xs-4 text-center">
            <b>{{ $pedido->valor_desconto > 0 ? "Desconto R$ ".formatValueForUser($pedido->valor_desconto) : "" }}</b></div>
        <div class="col-xs-4 text-center"><b>VALOR DO PEDIDO R$ {{formatValueForUser($pedido->valor_liquido)}}</b></div>
    </div>
@endif

@if(strlen($pedido->observacoes))
    <hr>
    <div class="row">
        <div class="col-xs-2 text-left"><b>Observações</b></div>
        <div class="col-xs-10 text-justify">
            {!! nl2br($pedido->observacoes) !!}
        </div>
    </div>
@endif

<hr>

<div class="row assinaturaCliente">
    <div class="col-xs-6">
        <div class="text-center">
            ____/_____/_______ <br>
            Data
        </div>
    </div>

    <div class="col-xs-6">
        <div class="text-center">
            _____________________________ <br>
            Assinatura do cliente
        </div>
    </div>
</div>

</body>

</html>