@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Nova Inscrição</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inscriptions.store') }}">
                        @csrf

                        <!-- Seção Cliente -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Cliente *</label>
                                    <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                        <option value="">Selecione um cliente</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                data-name="{{ $client->name }}"
                                                data-email="{{ $client->email }}"
                                                data-phone="{{ $client->phone }}"
                                                data-cpf="{{ $client->cpf }}"
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} - {{ $client->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vendor_id" class="form-label">Vendedor</label>
                                    <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                        <option value="">Selecione um vendedor</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Seção Produto e Dados Básicos -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produto *</label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                                @if($product->offer_price && $product->offer_price < $product->price)
                                                    - R$ {{ number_format($product->offer_price, 2, ',', '.') }} 
                                                    <small class="text-muted">(de R$ {{ number_format($product->price, 2, ',', '.') }})</small>
                                                @else
                                                    - R$ {{ number_format($product->price, 2, ',', '.') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="natureza_juridica" class="form-label">Natureza Jurídica *</label>
                                    <select class="form-select @error('natureza_juridica') is-invalid @enderror" id="natureza_juridica" name="natureza_juridica" required>
                                        <option value="">Selecione</option>
                                        <option value="pessoa fisica" {{ old('natureza_juridica') == 'pessoa fisica' ? 'selected' : '' }}>Pessoa Física</option>
                                        <option value="pessoa juridica" {{ old('natureza_juridica') == 'pessoa juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                                    </select>
                                    @error('natureza_juridica')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        @foreach(\App\Http\Controllers\InscriptionController::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 'active') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Seção Valores -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Informações Financeiras</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="valor_total" class="form-label">Valor Total *</label>
                                            <input type="text" class="form-control @error('valor_total') is-invalid @enderror" id="valor_total" name="valor_total" value="{{ old('valor_total') }}" required>
                                            @error('valor_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="class_group" class="form-label">Turma</label>
                                            <input type="text" class="form-control @error('class_group') is-invalid @enderror" id="class_group" name="class_group" value="{{ old('class_group') }}">
                                            @error('class_group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="classification" class="form-label">Classificação</label>
                                            <input type="text" class="form-control @error('classification') is-invalid @enderror" id="classification" name="classification" value="{{ old('classification') }}">
                                            @error('classification')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagamento Parcelado -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="parcelado" name="parcelado">
                                        <label class="form-check-label" for="parcelado">Pagamento parcelado?</label>
                                    </div>
                                </div>
                                <div id="parcelado-fields" style="display:none;">
                                    <h6 class="mt-3">Pagamento de Entrada</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_entrada" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_entrada" name="forma_pagamento_entrada">
                                                    <option value="avista">À vista</option>
                                                    <option value="parcelado">Parcelado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_entrada_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_entrada" class="form-label">Parcelas</label>
                                                <input type="number" class="form-control" id="parcelas_entrada" name="parcelas_entrada" min="2" max="24">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="meio_pagamento_entrada" class="form-label">Meio de Pagamento *</label>
                                                <select class="form-select" id="meio_pagamento_entrada" name="meio_pagamento_entrada">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->name }}">{{ $platform->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_entrada" class="form-label">Valor da Entrada *</label>
                                                <input type="number" class="form-control @error('valor_entrada') is-invalid @enderror" id="valor_entrada" name="valor_entrada" value="{{ old('valor_entrada') }}" step="0.01" min="0">
                                                @error('valor_entrada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_pagamento_entrada" class="form-label">Data do Pagamento da Entrada *</label>
                                                <input type="date" class="form-control @error('data_pagamento_entrada') is-invalid @enderror" id="data_pagamento_entrada" name="data_pagamento_entrada" value="{{ old('data_pagamento_entrada') }}">
                                                @error('data_pagamento_entrada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <h6 class="mt-3">Pagamento Restante</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="meio_pagamento_restante" class="form-label">Meio de Pagamento *</label>
                                                <select class="form-select" id="meio_pagamento_restante" name="meio_pagamento_restante">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->name }}">{{ $platform->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_restante" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_restante" name="forma_pagamento_restante">
                                                    <option value="avista">À vista</option>
                                                    <option value="parcelado">Parcelado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_restante_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_restante" class="form-label">Parcelas</label>
                                                <input type="number" class="form-control" id="parcelas_restante" name="parcelas_restante" min="2" max="24">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_restante" class="form-label">Valor Restante *</label>
                                                <input type="number" class="form-control @error('valor_restante') is-invalid @enderror" id="valor_restante" name="valor_restante" value="{{ old('valor_restante') }}" step="0.01" min="0">
                                                @error('valor_restante')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_contrato" class="form-label">Data do Contrato *</label>
                                                <input type="date" class="form-control @error('data_contrato') is-invalid @enderror" id="data_contrato" name="data_contrato" value="{{ old('data_contrato') }}">
                                                @error('data_contrato')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="avista-fields">
                                    <h6 class="mt-3">Pagamento Único</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="meio_pagamento_avista" class="form-label">Meio de Pagamento *</label>
                                                <select class="form-select" id="meio_pagamento_avista" name="meio_pagamento_avista">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->name }}">{{ $platform->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_avista" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_avista" name="forma_pagamento_avista">
                                                    <option value="avista">À vista</option>
                                                    <option value="parcelado">Parcelado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_avista_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_avista" class="form-label">Parcelas</label>
                                                <input type="number" class="form-control" id="parcelas_avista" name="parcelas_avista" min="2" max="24">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_avista" class="form-label">Valor *</label>
                                                <input type="number" class="form-control" id="valor_avista" name="valor_avista" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_pagamento_avista" class="form-label">Data do Pagamento *</label>
                                                <input type="date" class="form-control" id="data_pagamento_avista" name="data_pagamento_avista">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Endereço -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Endereço</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="cep" class="form-label">CEP *</label>
                                            <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep') }}" placeholder="00000-000" maxlength="9" required>
                                            @error('cep')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="cep-loading" style="display:none;" class="mt-2">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                <span>Buscando CEP...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="endereco" class="form-label">Rua *</label>
                                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco" value="{{ old('endereco') }}" required>
                                            @error('endereco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="numero_casa" class="form-label">Número *</label>
                                            <input type="text" class="form-control @error('numero_casa') is-invalid @enderror" id="numero_casa" name="numero_casa" value="{{ old('numero_casa') }}" required>
                                            @error('numero_casa')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="complemento" class="form-label">Complemento</label>
                                            <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento" value="{{ old('complemento') }}">
                                            @error('complemento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="bairro" class="form-label">Bairro *</label>
                                            <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro" value="{{ old('bairro') }}" required>
                                            @error('bairro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cidade" class="form-label">Cidade *</label>
                                            <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade" value="{{ old('cidade') }}" required>
                                            @error('cidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado *</label>
                                            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                                <option value="">Selecione</option>
                                                <option value="AC" {{ old('estado') == 'AC' ? 'selected' : '' }}>Acre</option>
                                                <option value="AL" {{ old('estado') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                                <option value="AP" {{ old('estado') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                                <option value="AM" {{ old('estado') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                                <option value="BA" {{ old('estado') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                                <option value="CE" {{ old('estado') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                                <option value="DF" {{ old('estado') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                                <option value="ES" {{ old('estado') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                                <option value="GO" {{ old('estado') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                                <option value="MA" {{ old('estado') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                                <option value="MT" {{ old('estado') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                                <option value="MS" {{ old('estado') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                                <option value="MG" {{ old('estado') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                                <option value="PA" {{ old('estado') == 'PA' ? 'selected' : '' }}>Pará</option>
                                                <option value="PB" {{ old('estado') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                                <option value="PR" {{ old('estado') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                                <option value="PE" {{ old('estado') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                                <option value="PI" {{ old('estado') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                                <option value="RJ" {{ old('estado') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                                <option value="RN" {{ old('estado') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                                <option value="RS" {{ old('estado') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                                <option value="RO" {{ old('estado') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                                <option value="RR" {{ old('estado') == 'RR' ? 'selected' : '' }}>Roraima</option>
                                                <option value="SC" {{ old('estado') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                                <option value="SP" {{ old('estado') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                                <option value="SE" {{ old('estado') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                                <option value="TO" {{ old('estado') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                            </select>
                                            @error('estado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos -->
                        <input type="hidden" name="deal_stage" value="Sistema MD1">
                        <input type="hidden" name="deal_user" value="{{ auth()->user()->email }}">

                        <!-- Observações -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commercial_notes" class="form-label">Observações Comerciais</label>
                                    <textarea class="form-control @error('commercial_notes') is-invalid @enderror" id="commercial_notes" name="commercial_notes" rows="3">{{ old('commercial_notes') }}</textarea>
                                    @error('commercial_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="general_notes" class="form-label">Observações Gerais</label>
                                    <textarea class="form-control @error('general_notes') is-invalid @enderror" id="general_notes" name="general_notes" rows="3">{{ old('general_notes') }}</textarea>
                                    @error('general_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inscriptions.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Criar Inscrição</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Exibe campo de parcelas se forma de pagamento for parcelado
    function toggleParcelas(selectId, groupId) {
        const select = document.getElementById(selectId);
        const group = document.getElementById(groupId);
        function update() {
            if (select.value === 'parcelado') {
                group.style.display = '';
            } else {
                group.style.display = 'none';
            }
        }
        select.addEventListener('change', update);
        update();
    }
    toggleParcelas('forma_pagamento_entrada', 'parcelas_entrada_group');
    toggleParcelas('forma_pagamento_restante', 'parcelas_restante_group');
    toggleParcelas('forma_pagamento_avista', 'parcelas_avista_group');
document.addEventListener('DOMContentLoaded', function() {
    const valorTotalInput = document.getElementById('valor_total');
    function formatMoneyBR(value) {
        value = value.replace(/[^\d.,]/g, '');
        if (value === '') return '';
        let floatValue = parseFloat(value.replace('.', '').replace(',', '.'));
        if (isNaN(floatValue)) return '';
        return floatValue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    valorTotalInput.addEventListener('input', function(e) {
        let v = e.target.value;
        e.target.value = formatMoneyBR(v);
    });

    const productSelect = document.getElementById('product_id');
    const productPrices = {};
    @foreach($products as $product)
        productPrices[{{ $product->id }}] = {
            price: {{ $product->price }},
            offer_price: {{ $product->offer_price ?? 'null' }}
        };
    @endforeach
    productSelect.addEventListener('change', function() {
        const selectedId = this.value;
        if (productPrices[selectedId]) {
            const p = productPrices[selectedId];
            let total = p.price;
            if (p.offer_price && p.offer_price > 0 && p.offer_price < p.price) {
                total = p.offer_price;
            }
            valorTotalInput.value = formatMoneyBR(total.toString());
        } else {
            valorTotalInput.value = '';
        }
    });

    const cepInput = document.getElementById('cep');
    cepInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 8);
        }
        e.target.value = value;
    });
    cepInput.addEventListener('input', function(e) {
        const cep = cepInput.value.replace(/\D/g, '');
        if (cep.length === 8) {
            const loading = document.getElementById('cep-loading');
            loading.style.display = 'inline-block';
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (!('erro' in data)) {
                        document.getElementById('endereco').value = data.logradouro || '';
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('cidade').value = data.localidade || '';
                        document.getElementById('estado').value = data.uf || '';
                        document.getElementById('numero_casa').focus();
                    } else {
                        document.getElementById('endereco').value = '';
                        document.getElementById('bairro').value = '';
                        document.getElementById('cidade').value = '';
                        document.getElementById('estado').value = '';
                        alert('CEP não encontrado.');
                    }
                })
                .catch(() => {
                    loading.style.display = 'none';
                    alert('Erro ao buscar o CEP.');
                });
        }
    });

    const valorEntradaInput = document.getElementById('valor_entrada');
    const valorRestanteInput = document.getElementById('valor_restante');
    function calcularRestante() {
        const total = parseFloat(valorTotalInput.value) || 0;
        const entrada = parseFloat(valorEntradaInput.value) || 0;
        const restante = total - entrada;
        valorRestanteInput.value = restante >= 0 ? restante.toFixed(2) : '';
    }
    valorTotalInput.addEventListener('input', calcularRestante);
    valorEntradaInput.addEventListener('input', calcularRestante);

    // Exibe campos conforme parcelado ou à vista
    const parceladoCheckbox = document.getElementById('parcelado');
    const parceladoFields = document.getElementById('parcelado-fields');
    const avistaFields = document.getElementById('avista-fields');
    function toggleParceladoFields() {
        if (parceladoCheckbox.checked) {
            parceladoFields.style.display = '';
            avistaFields.style.display = 'none';
        } else {
            parceladoFields.style.display = 'none';
            avistaFields.style.display = '';
        }
    }
    parceladoCheckbox.addEventListener('change', toggleParceladoFields);
    toggleParceladoFields();
});
</script>
@endsection
