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
                                    <select class="form-select @error('client_id') is-invalid @enderror" 
                                            id="client_id" name="client_id" required>
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
                                    <select class="form-select @error('vendor_id') is-invalid @enderror" 
                                            id="vendor_id" name="vendor_id">
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
                                    <select class="form-select @error('product_id') is-invalid @enderror" 
                                            id="product_id" name="product_id" required>
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
                                    <select class="form-select @error('natureza_juridica') is-invalid @enderror" 
                                            id="natureza_juridica" name="natureza_juridica" required>
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
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
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
                                            <input type="number" class="form-control @error('valor_total') is-invalid @enderror" 
                                                   id="valor_total" name="valor_total" value="{{ old('valor_total') }}" 
                                                   step="0.01" min="0" required>
                                            @error('valor_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="payment_platform_id" class="form-label">Plataforma de Pagamento *</label>
                                            <select class="form-select @error('payment_platform_id') is-invalid @enderror" id="payment_platform_id" name="payment_platform_id" required>
                                                <option value="">Selecione uma plataforma</option>
                                                @foreach($paymentPlatforms as $platform)
                                                    <option value="{{ $platform->id }}" {{ old('payment_platform_id') == $platform->id ? 'selected' : '' }}>
                                                        {{ $platform->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('payment_platform_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="class_group" class="form-label">Turma</label>
                                            <input type="text" class="form-control @error('class_group') is-invalid @enderror" 
                                                   id="class_group" name="class_group" value="{{ old('class_group') }}">
                                            @error('class_group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="classification" class="form-label">Classificação</label>
                                            <input type="text" class="form-control @error('classification') is-invalid @enderror" 
                                                   id="classification" name="classification" value="{{ old('classification') }}">
                                            @error('classification')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagamento Entrada -->
                                <div class="mb-3">
                                    <label class="form-label">Possui pagamento de entrada?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tem_entrada" id="entrada_sim" value="sim">
                                            <label class="form-check-label" for="entrada_sim">Sim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tem_entrada" id="entrada_nao" value="nao" checked>
                                            <label class="form-check-label" for="entrada_nao">Não</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="entrada-fields" style="display:none;">
                                    <h6 class="mt-3">Pagamento de Entrada</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_entrada" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select @error('forma_pagamento_entrada') is-invalid @enderror" 
                                                        id="forma_pagamento_entrada" name="forma_pagamento_entrada">
                                                    <option value="">Selecione</option>
                                                    <option value="PIX" {{ old('forma_pagamento_entrada') == 'PIX' ? 'selected' : '' }}>PIX</option>
                                                    <option value="Boleto" {{ old('forma_pagamento_entrada') == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                                    <option value="Cartão 1x" {{ old('forma_pagamento_entrada') == 'Cartão 1x' ? 'selected' : '' }}>Cartão 1x</option>
                                                    <option value="Cartão 2x" {{ old('forma_pagamento_entrada') == 'Cartão 2x' ? 'selected' : '' }}>Cartão 2x</option>
                                                    <option value="Cartão 3x" {{ old('forma_pagamento_entrada') == 'Cartão 3x' ? 'selected' : '' }}>Cartão 3x</option>
                                                    <option value="Cartão 4x" {{ old('forma_pagamento_entrada') == 'Cartão 4x' ? 'selected' : '' }}>Cartão 4x</option>
                                                    <option value="Cartão 5x" {{ old('forma_pagamento_entrada') == 'Cartão 5x' ? 'selected' : '' }}>Cartão 5x</option>
                                                    <option value="Cartão 6x" {{ old('forma_pagamento_entrada') == 'Cartão 6x' ? 'selected' : '' }}>Cartão 6x</option>
                                                    <option value="Cartão 7x" {{ old('forma_pagamento_entrada') == 'Cartão 7x' ? 'selected' : '' }}>Cartão 7x</option>
                                                    <option value="Cartão 8x" {{ old('forma_pagamento_entrada') == 'Cartão 8x' ? 'selected' : '' }}>Cartão 8x</option>
                                                    <option value="Cartão 9x" {{ old('forma_pagamento_entrada') == 'Cartão 9x' ? 'selected' : '' }}>Cartão 9x</option>
                                                    <option value="Cartão 10x" {{ old('forma_pagamento_entrada') == 'Cartão 10x' ? 'selected' : '' }}>Cartão 10x</option>
                                                    <option value="Cartão 11x" {{ old('forma_pagamento_entrada') == 'Cartão 11x' ? 'selected' : '' }}>Cartão 11x</option>
                                                    <option value="Cartão 12x" {{ old('forma_pagamento_entrada') == 'Cartão 12x' ? 'selected' : '' }}>Cartão 12x</option>
                                                    <option value="Cartão Recorrencia" {{ old('forma_pagamento_entrada') == 'Cartão Recorrencia' ? 'selected' : '' }}>Cartão Recorrência</option>
                                                    <option value="Deposito em conta" {{ old('forma_pagamento_entrada') == 'Deposito em conta' ? 'selected' : '' }}>Depósito em conta</option>
                                                </select>
                                                @error('forma_pagamento_entrada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_entrada" class="form-label">Valor da Entrada *</label>
                                                <input type="number" class="form-control @error('valor_entrada') is-invalid @enderror" 
                                                       id="valor_entrada" name="valor_entrada" value="{{ old('valor_entrada') }}" 
                                                       step="0.01" min="0">
                                                @error('valor_entrada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_pagamento_entrada" class="form-label">Data do Pagamento *</label>
                                                <input type="date" class="form-control @error('data_pagamento_entrada') is-invalid @enderror" 
                                                       id="data_pagamento_entrada" name="data_pagamento_entrada" value="{{ old('data_pagamento_entrada') }}">
                                                @error('data_pagamento_entrada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="restante-fields">
                                    <h6 class="mt-3">Pagamento Restante</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_restante" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select @error('forma_pagamento_restante') is-invalid @enderror" 
                                                        id="forma_pagamento_restante" name="forma_pagamento_restante">
                                                    <option value="">Selecione</option>
                                                    <option value="PIX" {{ old('forma_pagamento_restante') == 'PIX' ? 'selected' : '' }}>PIX</option>
                                                    <option value="Boleto" {{ old('forma_pagamento_restante') == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                                    <option value="Cartão 1x" {{ old('forma_pagamento_restante') == ' 1x' ? 'selected' : '' }}>Cartão 1x</option>
                                                    <option value="Cartão 2x" {{ old('forma_pagamento_restante') == 'Cartão 2x' ? 'selected' : '' }}>Cartão 2x</option>
                                                    <option value="Cartão 3x" {{ old('forma_pagamento_restante') == 'Cartão 3x' ? 'selected' : '' }}>Cartão 3x</option>
                                                    <option value="Cartão 4x" {{ old('forma_pagamento_restante') == 'Cartão 4x' ? 'selected' : '' }}>Cartão 4x</option>
                                                    <option value="Cartão 5x" {{ old('forma_pagamento_restante') == 'Cartão 5x' ? 'selected' : '' }}>Cartão 5x</option>
                                                    <option value="Cartão 6x" {{ old('forma_pagamento_restante') == 'Cartão 6x' ? 'selected' : '' }}>Cartão 6x</option>
                                                    <option value="Cartão 7x" {{ old('forma_pagamento_restante') == 'Cartão 7x' ? 'selected' : '' }}>Cartão 7x</option> 
                                                    <option value="Cartão 8x" {{ old('forma_pagamento_restante') == 'Cartão 8x' ? 'selected' : '' }}>Cartão 8x</option>
                                                    <option value="Cartão 9x" {{ old('forma_pagamento_restante') == 'Cartão 9x' ? 'selected' : '' }}>Cartão 9x</option>
                                                    <option value="Cartão 10x" {{ old('forma_pagamento_restante') == 'Cartão 10x' ? 'selected' : '' }}>Cartão 10x</option>
                                                    <option value="Cartão 11x" {{ old('forma_pagamento_restante') == 'Cartão 11x' ? 'selected' : '' }}>Cartão 11x</option>
                                                    <option value="Cartão 12x" {{ old('forma_pagamento_restante') == 'Cartão 12x' ? 'selected' : '' }}>Cartão 12x</option>

                                                    <option value="Cartão Recorrencia" {{ old('forma_pagamento_restante') == 'Cartão Recorrencia' ? 'selected' : '' }}>Cartão Recorrência</option>
                                                    <option value="Deposito em conta" {{ old('forma_pagamento_restante') == 'Deposito em conta' ? 'selected' : '' }}>Depósito em conta</option>
                                                </select>
                                                @error('forma_pagamento_restante')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_restante" class="form-label">Valor Restante *</label>
                                                <input type="number" class="form-control @error('valor_restante') is-invalid @enderror" 
                                                       id="valor_restante" name="valor_restante" value="{{ old('valor_restante') }}" 
                                                       step="0.01" min="0">
                                                @error('valor_restante')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_contrato" class="form-label">Data do Contrato *</label>
                                                <input type="date" class="form-control @error('data_contrato') is-invalid @enderror" 
                                                       id="data_contrato" name="data_contrato" value="{{ old('data_contrato') }}">
                                                @error('data_contrato')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                            <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                                                   id="cep" name="cep" value="{{ old('cep') }}" 
                                                   placeholder="00000-000" maxlength="9" required>
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
                                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                                                   id="endereco" name="endereco" value="{{ old('endereco') }}" required>
                                            @error('endereco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="numero_casa" class="form-label">Número *</label>
                                            <input type="text" class="form-control @error('numero_casa') is-invalid @enderror" 
                                                   id="numero_casa" name="numero_casa" value="{{ old('numero_casa') }}" required>
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
                                            <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
                                                   id="complemento" name="complemento" value="{{ old('complemento') }}">
                                            @error('complemento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="bairro" class="form-label">Bairro *</label>
                                            <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
                                                   id="bairro" name="bairro" value="{{ old('bairro') }}" required>
                                            @error('bairro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cidade" class="form-label">Cidade *</label>
                                            <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                                                   id="cidade" name="cidade" value="{{ old('cidade') }}" required>
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
                                            <select class="form-select @error('estado') is-invalid @enderror" 
                                                    id="estado" name="estado" required>
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
                                    <textarea class="form-control @error('commercial_notes') is-invalid @enderror" 
                                              id="commercial_notes" name="commercial_notes" rows="3">{{ old('commercial_notes') }}</textarea>
                                    @error('commercial_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="general_notes" class="form-label">Observações Gerais</label>
                                    <textarea class="form-control @error('general_notes') is-invalid @enderror" 
                                              id="general_notes" name="general_notes" rows="3">{{ old('general_notes') }}</textarea>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Máscara para CEP
        const cepInput = document.getElementById('cep');
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });

            // Preencher endereço via ViaCEP
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
                                // Limpa campos se CEP não encontrado
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
        // Auto-calcular valor restante
        const valorTotalInput = document.getElementById('valor_total');
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

        function toggleEntradaFields() {
            const sim = document.getElementById('entrada_sim');
            const entradaFields = document.getElementById('entrada-fields');
            const restanteFields = document.getElementById('restante-fields');
            if (sim.checked) {
                entradaFields.style.display = '';
                restanteFields.style.display = '';
                document.getElementById('forma_pagamento_entrada').required = true;
                document.getElementById('valor_entrada').required = true;
                document.getElementById('data_pagamento_entrada').required = true;
                document.getElementById('forma_pagamento_restante').required = true;
                document.getElementById('valor_restante').required = true;
                document.getElementById('data_contrato').required = true;
            } else {
                entradaFields.style.display = 'none';
                restanteFields.style.display = '';
                document.getElementById('forma_pagamento_entrada').required = false;
                document.getElementById('valor_entrada').required = false;
                document.getElementById('data_pagamento_entrada').required = false;
                document.getElementById('forma_pagamento_restante').required = true;
                document.getElementById('valor_restante').required = true;
                document.getElementById('data_contrato').required = true;
            }
        }
        document.getElementById('entrada_sim').addEventListener('change', toggleEntradaFields);
        document.getElementById('entrada_nao').addEventListener('change', toggleEntradaFields);
        toggleEntradaFields();
    });
</script>
@endsection