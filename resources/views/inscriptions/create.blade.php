@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Nova Inscrição</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inscriptions.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Cliente *</label>
                                    <select class="form-select @error('client_id') is-invalid @enderror" 
                                            id="client_id" name="client_id" required>
                                        <option value="">Selecione um cliente</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                    <label for="class_group" class="form-label">Turma</label>
                                    <input type="text" class="form-control @error('class_group') is-invalid @enderror" 
                                           id="class_group" name="class_group" value="{{ old('class_group') }}">
                                    @error('class_group')
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

                        <div class="row">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="crmb_number" class="form-label">Número CRMB</label>
                                    <input type="text" class="form-control @error('crmb_number') is-invalid @enderror" 
                                           id="crmb_number" name="crmb_number" value="{{ old('crmb_number') }}">
                                    @error('crmb_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input @error('has_medboss') is-invalid @enderror" 
                                               type="checkbox" id="has_medboss" name="has_medboss" value="1" 
                                               {{ old('has_medboss') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_medboss">
                                            Possui MedBoss
                                        </label>
                                        @error('has_medboss')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Data de Início</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="original_end_date" class="form-label">Data Término Prevista</label>
                                    <input type="date" class="form-control @error('original_end_date') is-invalid @enderror" 
                                           id="original_end_date" name="original_end_date" value="{{ old('original_end_date') }}">
                                    @error('original_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="actual_end_date" class="form-label">Data Término Real</label>
                                    <input type="date" class="form-control @error('actual_end_date') is-invalid @enderror" 
                                           id="actual_end_date" name="actual_end_date" value="{{ old('actual_end_date') }}">
                                    @error('actual_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="platform_release_date" class="form-label">Data Liberação Plataforma</label>
                                    <input type="date" class="form-control @error('platform_release_date') is-invalid @enderror" 
                                           id="platform_release_date" name="platform_release_date" value="{{ old('platform_release_date') }}">
                                    @error('platform_release_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="calendar_week" class="form-label">Semana Calendário</label>
                                    <input type="number" class="form-control @error('calendar_week') is-invalid @enderror" 
                                           id="calendar_week" name="calendar_week" value="{{ old('calendar_week') }}" 
                                           min="1" max="52">
                                    @error('calendar_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="current_week" class="form-label">Semana Atual</label>
                                    <input type="number" class="form-control @error('current_week') is-invalid @enderror" 
                                           id="current_week" name="current_week" value="{{ old('current_week') }}" 
                                           min="1" max="52">
                                    @error('current_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Valor Pago</label>
                                    <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" 
                                           id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" 
                                           step="0.01" min="0">
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Forma de Pagamento</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method">
                                        <option value="">Selecione</option>
                                        @foreach(\App\Http\Controllers\InscriptionController::getPaymentMethodOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

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

                        <!-- Campos Demográficos para Estudos -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="problemas_desafios" class="form-label">Problemas ou Desafios</label>
                                    <textarea class="form-control @error('problemas_desafios') is-invalid @enderror" 
                                              id="problemas_desafios" name="problemas_desafios" rows="4" 
                                              placeholder="Descreva os principais problemas ou desafios enfrentados pelo aluno...">{{ old('problemas_desafios') }}</textarea>
                                    <div class="form-text">Campo para estudos demográficos - descreva os desafios que motivaram a inscrição</div>
                                    @error('problemas_desafios')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Histórico de Faturamento Mensal</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-text mb-3">
                                                <i class="fas fa-info-circle"></i> 
                                                Registre o faturamento mensal para acompanhar a evolução durante a mentoria
                                            </div>
                                            <div id="historico-faturamento-container">
                                                <div class="row historico-item mb-2">
                                                    <div class="col-md-3">
                                                        <select class="form-select" name="historico_mes[]">
                                                            <option value="">Mês</option>
                                                            <option value="1">Janeiro</option>
                                                            <option value="2">Fevereiro</option>
                                                            <option value="3">Março</option>
                                                            <option value="4">Abril</option>
                                                            <option value="5">Maio</option>
                                                            <option value="6">Junho</option>
                                                            <option value="7">Julho</option>
                                                            <option value="8">Agosto</option>
                                                            <option value="9">Setembro</option>
                                                            <option value="10">Outubro</option>
                                                            <option value="11">Novembro</option>
                                                            <option value="12">Dezembro</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" class="form-control" name="historico_ano[]" 
                                                               placeholder="Ano" min="2020" max="{{ date('Y') + 1 }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text">R$</span>
                                                            <input type="text" class="form-control money-mask" name="historico_valor[]" 
                                                                   placeholder="0,00">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-historico" style="display: none;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-historico">
                                                <i class="fas fa-plus"></i> Adicionar Mês
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inscriptions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Inscrição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

