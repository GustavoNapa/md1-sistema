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
                    <form id="inscription-form" method="POST" action="{{ route('inscriptions.store') }}">
                        @csrf

                        <!-- Seção Cliente -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="client_search" class="form-label">Cliente *</label>
                                    <input type="hidden" id="client_id" name="client_id" value="{{ old('client_id') }}">
                                    <input type="text" autocomplete="off" class="form-control @error('client_id') is-invalid @enderror" id="client_search" name="client_search" placeholder="Busque por nome ou email" required value="{{ old('client_search') }}">
                                    <div id="client-suggestions" class="list-group" style="position:absolute; left:0; right:0; z-index:1000; max-height:320px; overflow:auto;"></div>
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
                                        <label class="form-check-label" for="parcelado">Pagamento de entrada?</label>
                                    </div>
                                </div>
                                <div id="parcelado-fields" style="display:none;">
                                    <h6 class="mt-3">Pagamento de Entrada</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="meio_pagamento_entrada" class="form-label">Pagamento no</label>
                                                <select class="form-select" id="meio_pagamento_entrada" name="meio_pagamento_entrada">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->id }}" {{ old('meio_pagamento_entrada') == $platform->id ? 'selected' : '' }}>
                                                            {{ $platform->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="payment_channel_entrada" class="form-label">Meio de pagamento</label>
                                                <select class="form-select" id="payment_channel_entrada" name="payment_channel_entrada">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentChannels as $channel)
                                                        <option value="{{ $channel->id }}" {{ old('payment_channel_entrada') == $channel->id ? 'selected' : '' }}>
                                                            {{ $channel->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_entrada" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_entrada" name="forma_pagamento_entrada">
                                                    <option value="">Selecione meio primeiro</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_entrada_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_entrada" class="form-label">Parcelas</label>
                                                <select class="form-select" id="parcelas_entrada" name="parcelas_entrada">
                                                    <option value="">--</option>
                                                    @for($i = 2; $i <= 24; $i++)
                                                        <option value="{{ $i }}" {{ old('parcelas_entrada') == $i ? 'selected' : '' }}>{{ $i }}x</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_entrada" class="form-label">Valor da Entrada *</label>
                                                <input type="text" class="form-control @error('valor_entrada') is-invalid @enderror" id="valor_entrada" name="valor_entrada" value="{{ old('valor_entrada') ? number_format(old('valor_entrada'), 2, ',', '.') : '' }}">
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
                                                <label for="meio_pagamento_restante" class="form-label">Pagamento no</label>
                                                <select class="form-select" id="meio_pagamento_restante" name="meio_pagamento_restante">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->id }}" {{ old('meio_pagamento_restante') == $platform->id ? 'selected' : '' }}>
                                                            {{ $platform->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="payment_channel_restante" class="form-label">Meio de pagamento</label>
                                                <select class="form-select" id="payment_channel_restante" name="payment_channel_restante">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentChannels as $channel)
                                                        <option value="{{ $channel->id }}" {{ old('payment_channel_restante') == $channel->id ? 'selected' : '' }}>
                                                            {{ $channel->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_restante" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_restante" name="forma_pagamento_restante">
                                                    <option value="">Selecione meio primeiro</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_restante_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_restante" class="form-label">Parcelas</label>
                                                <select class="form-select" id="parcelas_restante" name="parcelas_restante">
                                                    <option value="">--</option>
                                                    @for($i = 2; $i <= 24; $i++)
                                                        <option value="{{ $i }}" {{ old('parcelas_restante') == $i ? 'selected' : '' }}>{{ $i }}x</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_restante" class="form-label">Valor Restante *</label>
                                                <input type="text" class="form-control @error('valor_restante') is-invalid @enderror" id="valor_restante" name="valor_restante" value="{{ old('valor_restante') }}">
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
                                                <label for="meio_pagamento_avista" class="form-label">Pagamento no</label>
                                                <select class="form-select" id="meio_pagamento_avista" name="meio_pagamento_avista">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentPlatforms as $platform)
                                                        <option value="{{ $platform->id }}" {{ old('meio_pagamento_avista') == $platform->id ? 'selected' : '' }}>
                                                            {{ $platform->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="payment_channel_avista" class="form-label">Meio de pagamento</label>
                                                <select class="form-select" id="payment_channel_avista" name="payment_channel_avista">
                                                    <option value="">Selecione</option>
                                                    @foreach($paymentChannels as $channel)
                                                        <option value="{{ $channel->id }}" {{ old('payment_channel_avista') == $channel->id ? 'selected' : '' }}>
                                                            {{ $channel->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="forma_pagamento_avista" class="form-label">Forma de Pagamento *</label>
                                                <select class="form-select" id="forma_pagamento_avista" name="forma_pagamento_avista">
                                                    <option value="">Selecione meio primeiro</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="parcelas_avista_group" style="display:none;">
                                            <div class="mb-3">
                                                <label for="parcelas_avista" class="form-label">Parcelas</label>
                                                <select class="form-select" id="parcelas_avista" name="parcelas_avista">
                                                    <option value="">--</option>
                                                    @for($i = 2; $i <= 24; $i++)
                                                        <option value="{{ $i }}" {{ old('parcelas_avista') == $i ? 'selected' : '' }}>{{ $i }}x</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="valor_avista" class="form-label">Valor *</label>
                                                <input type="text" class="form-control" id="valor_avista" name="valor_avista">
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
    function toggleParcelas(selectId, groupId, parcelasSelectId) {
        const select = document.getElementById(selectId);
        const group = document.getElementById(groupId);
        const parcelasSelect = parcelasSelectId ? document.getElementById(parcelasSelectId) : null;

        function update() {
            const val = select ? select.value : '';
            const n = val === '' ? 0 : Number(val);
            if (!isNaN(n) && n > 1) {
                if (group) group.style.display = '';
                // se houver select de parcelas, selecionar o mesmo número
                if (parcelasSelect) {
                    // se opção existir, seleciona; caso contrário, adiciona temporariamente
                    const opt = parcelasSelect.querySelector(`option[value="${n}"]`);
                    if (opt) {
                        parcelasSelect.value = n;
                    } else {
                        // tenta adicionar uma opção correspondente
                        const o = document.createElement('option');
                        o.value = n;
                        o.text = n + 'x';
                        parcelasSelect.appendChild(o);
                        parcelasSelect.value = n;
                    }
                }
            } else {
                if (group) group.style.display = 'none';
            }
        }
        if (select) {
            select.addEventListener('change', update);
            update();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Client search/autocomplete
        const clients = @json($clients);
        const clientsData = (clients || []).map(c => ({ id: c.id, name: c.name, email: c.email, phone: c.phone, cpf: c.cpf }));
        const clientSearchInput = document.getElementById('client_search');
        const clientIdInput = document.getElementById('client_id');
        const clientSuggestions = document.getElementById('client-suggestions');

        function clearClientSuggestions() {
            clientSuggestions.innerHTML = '';
        }

        function renderClientSuggestions(list) {
            clientSuggestions.innerHTML = '';
            list.forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.textContent = c.name + (c.email ? ' - ' + c.email : '');
                btn.dataset.id = c.id;
                btn.addEventListener('click', function() {
                    clientIdInput.value = c.id;
                    clientSearchInput.value = btn.textContent;
                    clearClientSuggestions();
                });
                clientSuggestions.appendChild(btn);
            });
            if (list.length === 0) {
                const none = document.createElement('div');
                none.className = 'list-group-item';
                none.textContent = 'Nenhum cliente encontrado.';
                clientSuggestions.appendChild(none);
            }
        }

        let clientSearchTimeout = null;
        clientSearchInput.addEventListener('input', function(e) {
            const q = e.target.value.trim().toLowerCase();
            clientIdInput.value = '';
            if (clientSearchTimeout) clearTimeout(clientSearchTimeout);
            clientSearchTimeout = setTimeout(() => {
                let matches = [];
                if (q.length < 2) {
                    // show top clients when query is short (user clicked the field)
                    matches = clientsData.slice(0, 50);
                } else {
                    matches = clientsData.filter(c => {
                        return (c.name || '').toLowerCase().includes(q) || (c.email || '').toLowerCase().includes(q) || (String(c.id) === q);
                    }).slice(0, 50);
                }
                renderClientSuggestions(matches);
            }, 150);
        });

        // Show list when field is focused/clicked (even if empty)
        clientSearchInput.addEventListener('focus', function() {
            const q = clientSearchInput.value.trim().toLowerCase();
            if (q.length < 2) {
                renderClientSuggestions(clientsData.slice(0, 50));
            }
        });

        // Click outside to close suggestions
        document.addEventListener('click', function(ev) {
            if (ev.target !== clientSearchInput && !clientSuggestions.contains(ev.target)) {
                clearClientSuggestions();
            }
        });

        // If old client_id exists, populate the search input with client display
        if (clientIdInput && clientIdInput.value) {
            const existing = clientsData.find(c => String(c.id) === String(clientIdInput.value));
            if (existing) {
                clientSearchInput.value = existing.name + (existing.email ? ' - ' + existing.email : '');
            }
        }
        const valorTotalInput = document.getElementById('valor_total');
        function formatMoneyBR(value) {
            if (value === null || value === undefined) return '';
            let s = String(value).trim();
            if (s === '') return '';
            // Normalize: if contains both '.' and ',' assume '.' are thousands and ',' is decimal
            if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
                s = s.replace(/\./g, '').replace(',', '.');
            } else if (s.indexOf(',') > -1) {
                // only comma present -> decimal separator
                s = s.replace(',', '.');
            }
            // remove any non numeric except dot and minus
            s = s.replace(/[^0-9.\-]/g, '');
            const n = parseFloat(s);
            if (isNaN(n)) return '';
            return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function parseMoneyBRToNumber(value) {
            if (value === null || value === undefined) return 0;
            let s = String(value).trim();
            if (s === '') return 0;
            if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
                s = s.replace(/\./g, '').replace(',', '.');
            } else if (s.indexOf(',') > -1) {
                s = s.replace(',', '.');
            }
            s = s.replace(/[^0-9.\-]/g, '');
            const n = parseFloat(s);
            return isNaN(n) ? 0 : n;
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
                // update dependent fields when product changes
                calcularRestante();
                // if single payment selected, fill valor_avista
                const formaAvista = document.getElementById('forma_pagamento_avista');
                const valorAvistaInput = document.getElementById('valor_avista');
                if (formaAvista && formaAvista.value === 'avista' && valorAvistaInput) {
                    valorAvistaInput.value = formatMoneyBR(parseMoneyBRToNumber(valorTotalInput.value).toFixed(2));
                }
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
            const total = parseMoneyBRToNumber(valorTotalInput.value) || 0;
            const entrada = parseMoneyBRToNumber(valorEntradaInput.value) || 0;
            const restante = total - entrada;
        if (valorRestanteInput) valorRestanteInput.value = restante >= 0 ? formatMoneyBR(restante.toFixed(2)) : '';

            // if remaining payment forma is avista, ensure valor_restante equals restante
            const formaRestante = document.getElementById('forma_pagamento_restante');
            if (formaRestante && formaRestante.value === 'avista' && valorRestanteInput) {
                valorRestanteInput.value = restante >= 0 ? formatMoneyBR(restante.toFixed(2)) : '';
            }

            // if single payment forma is avista, update valor_avista
            const formaAvista = document.getElementById('forma_pagamento_avista');
            const valorAvistaInput = document.getElementById('valor_avista');
            if (formaAvista && formaAvista.value === 'avista' && valorAvistaInput) {
                valorAvistaInput.value = formatMoneyBR(total.toFixed(2));
            }
        }
        valorTotalInput.addEventListener('input', calcularRestante);
        // make valor_entrada easy to type: sanitize while typing, format on blur
        if (valorEntradaInput) {
            // Live currency mask for pt-BR while typing (last 2 digits are cents)
            valorEntradaInput.addEventListener('input', function(e) {
                const el = e.target;
                // keep only digits
                let digits = el.value.replace(/\D/g, '');
                if (!digits) {
                    el.value = '';
                    calcularRestante();
                    return;
                }
                // parse as cents (integer)
                let cents = parseInt(digits, 10);
                if (isNaN(cents)) { el.value = ''; calcularRestante(); return; }
                // get reais and centavos
                let reais = Math.floor(cents / 100);
                let centPart = (cents % 100).toString().padStart(2, '0');
                // format reais with thousands separator '.'
                let reaisFormatted = reais.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                el.value = reaisFormatted + ',' + centPart;
                // keep caret at the end for simpler UX
                try { el.setSelectionRange(el.value.length, el.value.length); } catch (err) {}
                calcularRestante();
            });

            // ensure formatting on blur (already formatted by input mask) - just trim
            valorEntradaInput.addEventListener('blur', function(e) {
                if (e.target.value && e.target.value.trim() !== '') {
                    e.target.value = formatMoneyBR(e.target.value);
                }
            });
        }

        // Auto-fill valor_avista when forma_pagamento_avista is 'avista'
        const formaPagamentoAvista = document.getElementById('forma_pagamento_avista');
        const valorAvistaInput = document.getElementById('valor_avista');
        if (formaPagamentoAvista) {
            formaPagamentoAvista.addEventListener('change', function() {
                if (this.value === 'avista' && valorAvistaInput) {
                    valorAvistaInput.value = formatMoneyBR(parseMoneyBRToNumber(valorTotalInput.value).toFixed(2));
                }
            });
            // set on load if already selected
            if (formaPagamentoAvista.value === 'avista' && valorAvistaInput) {
                valorAvistaInput.value = formatMoneyBR(parseMoneyBRToNumber(valorTotalInput.value).toFixed(2));
            }
        }

        // When forma_pagamento_restante is avista, ensure valor_restante is total - entrada
        const formaPagamentoRestante = document.getElementById('forma_pagamento_restante');
        if (formaPagamentoRestante) {
            formaPagamentoRestante.addEventListener('change', function() {
                if (this.value === 'avista' && valorRestanteInput) {
                    calcularRestante();
                }
            });
            if (formaPagamentoRestante.value === 'avista') {
                calcularRestante();
            }
        }

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
        // Enable/disable inputs in hidden sections to avoid submitting unrelated fields
        function setSectionDisabled(section, disabled) {
            if (!section) return;
            const controls = section.querySelectorAll('input, select, textarea');
            controls.forEach(c => {
                c.disabled = disabled;
            });
        }

        function toggleParceladoFields() {
            if (parceladoCheckbox.checked) {
                parceladoFields.style.display = '';
                avistaFields.style.display = 'none';
                setSectionDisabled(parceladoFields, false);
                setSectionDisabled(avistaFields, true);
            } else {
                parceladoFields.style.display = 'none';
                avistaFields.style.display = '';
                setSectionDisabled(parceladoFields, true);
                setSectionDisabled(avistaFields, false);
            }
        }

        parceladoCheckbox.addEventListener('change', toggleParceladoFields);
        // initialize
        toggleParceladoFields();
        // If user clicks the label or container for valor_entrada while the section is hidden/disabled,
        // automatically enable parcelado so the field becomes editable (less surprising UX).
        try {
            const valorEntradaLabel = document.querySelector('label[for="valor_entrada"]');
            const valorEntradaContainer = valorEntradaLabel ? valorEntradaLabel.closest('.mb-3') : null;
            function enableParceladoAndFocus() {
                if (!parceladoCheckbox.checked) {
                    parceladoCheckbox.checked = true;
                    toggleParceladoFields();
                }
                if (valorEntradaInput) {
                    valorEntradaInput.focus();
                }
            }
            if (valorEntradaLabel) {
                valorEntradaLabel.addEventListener('click', function(e) {
                    enableParceladoAndFocus();
                });
            }
            if (valorEntradaContainer) {
                valorEntradaContainer.addEventListener('click', function(e) {
                    // if clicking the container and input is disabled, enable parcelado
                    if (valorEntradaInput && valorEntradaInput.disabled) {
                        enableParceladoAndFocus();
                    }
                });
            }
        } catch (err) {
            // ignore if DOM structure differs
        }
        // Initialize computed values on load
        calcularRestante();

        // Before sending to backend, convert formatted BR money to numeric (dot decimal)
        const form = document.getElementById('inscription-form');
        if (form) {
            form.addEventListener('submit', function() {
                const fields = ['valor_total', 'valor_avista', 'valor_restante', 'valor_entrada'];
                fields.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el.value !== '') {
                        const n = parseMoneyBRToNumber(el.value);
                        // set as plain numeric string with dot as decimal separator
                        el.value = n.toFixed(2);
                    }
                });
            });
        }
    });

    // Constrói mapa de formas por canal a partir do banco
    (function() {
        const methods = @json(\Illuminate\Support\Facades\DB::table('payment_channel_methods')->orderBy('installments')->get()->map(function($m){ return (array)$m;}));
        const methodsByChannel = {};
        methods.forEach(m => {
            const key = String(m.payment_channel_id);
            if (!methodsByChannel[key]) methodsByChannel[key] = [];
            methodsByChannel[key].push({ id: m.id, name: m.name, installments: m.installments });
        });

        function populateFormaSelect(channelSelectId, formaSelectId, parcelasGroupId, parcelasSelectId, oldValue) {
            const channelSel = document.getElementById(channelSelectId);
            const formaSel = document.getElementById(formaSelectId);
            if (!channelSel || !formaSel) return;

            function rebuild() {
                const chId = channelSel.value;
                formaSel.innerHTML = '';
                const list = methodsByChannel[String(chId)] || [];
                if (list.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Nenhuma forma cadastrada';
                    formaSel.appendChild(opt);
                    // trigger change
                    formaSel.dispatchEvent(new Event('change'));
                    return;
                }
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Selecione a forma';
                formaSel.appendChild(placeholder);
                list.forEach(m => {
                    const o = document.createElement('option');
                    o.value = m.installments ?? m.name;
                    o.textContent = m.name;
                    formaSel.appendChild(o);
                });
                // tenta restaurar valor antigo (oldValue pode ser número ou string)
                if (oldValue) {
                    formaSel.value = oldValue;
                }
                formaSel.dispatchEvent(new Event('change'));
            }

            channelSel.addEventListener('change', rebuild);
            // inicializa agora
            rebuild();
        }

        // Inicializa para cada trio presente no formulário
        populateFormaSelect('payment_channel_entrada', 'forma_pagamento_entrada', 'parcelas_entrada_group', 'parcelas_entrada', {!! json_encode(old('forma_pagamento_entrada', '')) !!});
        populateFormaSelect('payment_channel_restante', 'forma_pagamento_restante', 'parcelas_restante_group', 'parcelas_restante', {!! json_encode(old('forma_pagamento_restante', '')) !!});
        populateFormaSelect('payment_channel_avista', 'forma_pagamento_avista', 'parcelas_avista_group', 'parcelas_avista', {!! json_encode(old('forma_pagamento_avista', '')) !!});

        // ativar toggleParcelas com base nos selects populados
        toggleParcelas('forma_pagamento_entrada', 'parcelas_entrada_group', 'parcelas_entrada');
        toggleParcelas('forma_pagamento_restante', 'parcelas_restante_group', 'parcelas_restante');
        toggleParcelas('forma_pagamento_avista', 'parcelas_avista_group', 'parcelas_avista');

        // se houver valores antigos de channel, disparar change para popular automaticamente
        ['payment_channel_entrada','payment_channel_restante','payment_channel_avista'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // disparar evento para forçar populações iniciais
                el.dispatchEvent(new Event('change'));
            }
        });
    })();
</script>
@endsection
