@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Cliente</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('clients.update', $client) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $client->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF *</label>
                                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                           id="cpf" name="cpf" value="{{ old('cpf', $client->cpf) }}" required>
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $client->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefone / WhatsApp</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                                           placeholder="(11) 99999-9999" maxlength="15">
                                    <div class="form-text">Formato: (11) 99999-9999</div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="phone-error" style="display: none;">
                                        Por favor, insira um telefone válido com 10 ou 11 dígitos.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos Demográficos -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sexo" class="form-label">Sexo</label>
                                    <select class="form-select @error('sexo') is-invalid @enderror" 
                                            id="sexo" name="sexo">
                                        <option value="">Selecione o sexo</option>
                                        <option value="masculino" {{ old('sexo', $client->sexo) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="feminino" {{ old('sexo', $client->sexo) == 'feminino' ? 'selected' : '' }}>Feminino</option>
                                        <option value="outro" {{ old('sexo', $client->sexo) == 'outro' ? 'selected' : '' }}>Outro</option>
                                        <option value="nao_informado" {{ old('sexo', $client->sexo) == 'nao_informado' ? 'selected' : '' }}>Não informado</option>
                                    </select>
                                    <div class="form-text">Campo para estudos demográficos</div>
                                    @error('sexo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="media_faturamento" class="form-label">Média de Faturamento Mensal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control @error('media_faturamento') is-invalid @enderror" 
                                               id="media_faturamento" name="media_faturamento" 
                                               value="{{ old('media_faturamento', $client->media_faturamento ? number_format($client->media_faturamento, 2, ',', '.') : '') }}"
                                               placeholder="0,00">
                                    </div>
                                    <div class="form-text">Valor médio de faturamento mensal do cliente</div>
                                    @error('media_faturamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date', $client->birth_date?->format('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}">
                                    <div class="form-text">Não pode ser uma data futura</div>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="birth-date-error" style="display: none;">
                                        A data de nascimento não pode ser no futuro.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="specialty" class="form-label">Especialidade</label>
                                    <select class="form-select @error('specialty') is-invalid @enderror" 
                                            id="specialty" name="specialty">
                                        <option value="">Selecione uma especialidade</option>
                                        @foreach($specialties as $value => $label)
                                            <option value="{{ $value }}" {{ old('specialty', $client->specialty) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_city" class="form-label">Cidade de Atendimento</label>
                                    <input type="text" class="form-control @error('service_city') is-invalid @enderror" 
                                           id="service_city" name="service_city" value="{{ old('service_city', $client->service_city) }}"
                                           pattern="^(?!^\d+$).+" title="A cidade não pode conter apenas números">
                                    @error('service_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="state" class="form-label">Estado</label>
                                    <select class="form-select @error('state') is-invalid @enderror" 
                                            id="state" name="state">
                                        <option value="">UF</option>
                                        <option value="AC" {{ old('state', $client->state) == 'AC' ? 'selected' : '' }}>AC</option>
                                        <option value="AL" {{ old('state', $client->state) == 'AL' ? 'selected' : '' }}>AL</option>
                                        <option value="AP" {{ old('state', $client->state) == 'AP' ? 'selected' : '' }}>AP</option>
                                        <option value="AM" {{ old('state', $client->state) == 'AM' ? 'selected' : '' }}>AM</option>
                                        <option value="BA" {{ old('state', $client->state) == 'BA' ? 'selected' : '' }}>BA</option>
                                        <option value="CE" {{ old('state', $client->state) == 'CE' ? 'selected' : '' }}>CE</option>
                                        <option value="DF" {{ old('state', $client->state) == 'DF' ? 'selected' : '' }}>DF</option>
                                        <option value="ES" {{ old('state', $client->state) == 'ES' ? 'selected' : '' }}>ES</option>
                                        <option value="GO" {{ old('state', $client->state) == 'GO' ? 'selected' : '' }}>GO</option>
                                        <option value="MA" {{ old('state', $client->state) == 'MA' ? 'selected' : '' }}>MA</option>
                                        <option value="MT" {{ old('state', $client->state) == 'MT' ? 'selected' : '' }}>MT</option>
                                        <option value="MS" {{ old('state', $client->state) == 'MS' ? 'selected' : '' }}>MS</option>
                                        <option value="MG" {{ old('state', $client->state) == 'MG' ? 'selected' : '' }}>MG</option>
                                        <option value="PA" {{ old('state', $client->state) == 'PA' ? 'selected' : '' }}>PA</option>
                                        <option value="PB" {{ old('state', $client->state) == 'PB' ? 'selected' : '' }}>PB</option>
                                        <option value="PR" {{ old('state', $client->state) == 'PR' ? 'selected' : '' }}>PR</option>
                                        <option value="PE" {{ old('state', $client->state) == 'PE' ? 'selected' : '' }}>PE</option>
                                        <option value="PI" {{ old('state', $client->state) == 'PI' ? 'selected' : '' }}>PI</option>
                                        <option value="RJ" {{ old('state', $client->state) == 'RJ' ? 'selected' : '' }}>RJ</option>
                                        <option value="RN" {{ old('state', $client->state) == 'RN' ? 'selected' : '' }}>RN</option>
                                        <option value="RS" {{ old('state', $client->state) == 'RS' ? 'selected' : '' }}>RS</option>
                                        <option value="RO" {{ old('state', $client->state) == 'RO' ? 'selected' : '' }}>RO</option>
                                        <option value="RR" {{ old('state', $client->state) == 'RR' ? 'selected' : '' }}>RR</option>
                                        <option value="SC" {{ old('state', $client->state) == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="SP" {{ old('state', $client->state) == 'SP' ? 'selected' : '' }}>SP</option>
                                        <option value="SE" {{ old('state', $client->state) == 'SE' ? 'selected' : '' }}>SE</option>
                                        <option value="TO" {{ old('state', $client->state) == 'TO' ? 'selected' : '' }}>TO</option>
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Região</label>
                                    <select class="form-select @error('region') is-invalid @enderror" 
                                            id="region" name="region">
                                        <option value="">Selecione uma região</option>
                                        <option value="Norte" {{ old('region', $client->region) == 'Norte' ? 'selected' : '' }}>Norte</option>
                                        <option value="Nordeste" {{ old('region', $client->region) == 'Nordeste' ? 'selected' : '' }}>Nordeste</option>
                                        <option value="Centro-Oeste" {{ old('region', $client->region) == 'Centro-Oeste' ? 'selected' : '' }}>Centro-Oeste</option>
                                        <option value="Sudeste" {{ old('region', $client->region) == 'Sudeste' ? 'selected' : '' }}>Sudeste</option>
                                        <option value="Sul" {{ old('region', $client->region) == 'Sul' ? 'selected' : '' }}>Sul</option>
                                    </select>
                                    @error('region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <input type="text" class="form-control @error('instagram') is-invalid @enderror" 
                                           id="instagram" name="instagram" value="{{ old('instagram', $client->instagram) }}"
                                           placeholder="@usuario">
                                    @error('instagram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="active" class="form-label">Status</label>
                                    <select class="form-select @error('active') is-invalid @enderror" id="active" name="active">
                                        <option value="1" {{ old('active', $client->active) == '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ old('active', $client->active) == '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                    @error('active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Voltar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Atualizar Cliente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Aplicar máscaras com jQuery Mask
    $('#phone').mask('(00) 00000-0000', {
        placeholder: '(11) 99999-9999',
        translation: {
            '0': {pattern: /[0-9]/}
        }
    });
    
    $('#cpf').mask('000.000.000-00', {
        placeholder: '000.000.000-00'
    });
    
    // Validação de telefone em tempo real
    $('#phone').on('input blur', function() {
        const phone = $(this).val().replace(/\D/g, ''); // Remove tudo que não é dígito
        const phoneField = $(this);
        const errorDiv = $('#phone-error');
        
        // Remove classes de erro anteriores
        phoneField.removeClass('is-invalid is-valid');
        errorDiv.hide();
        
        if (phone.length > 0) {
            if (phone.length === 10 || phone.length === 11) {
                // Telefone válido
                phoneField.addClass('is-valid');
            } else {
                // Telefone inválido
                phoneField.addClass('is-invalid');
                errorDiv.show();
            }
        }
    });
    
    // Validação de data de nascimento
    $('#birth_date').on('change', function() {
        const selectedDate = new Date($(this).val());
        const todayDate = new Date();
        const dateField = $(this);
        const errorDiv = $('#birth-date-error');
        
        // Remove classes de erro anteriores
        dateField.removeClass('is-invalid is-valid');
        errorDiv.hide();
        
        if ($(this).val()) {
            if (selectedDate > todayDate) {
                dateField.addClass('is-invalid');
                errorDiv.show();
            } else {
                dateField.addClass('is-valid');
            }
        }
    });
    
    // Validação de cidade (não pode ser só números)
    $('#service_city').on('input blur', function() {
        const city = $(this).val().trim();
        const cityField = $(this);
        
        cityField.removeClass('is-invalid is-valid');
        
        if (city.length > 0) {
            if (/^\d+$/.test(city)) {
                cityField.addClass('is-invalid');
                if (!cityField.siblings('.invalid-feedback.city-error').length) {
                    cityField.after('<div class="invalid-feedback city-error" style="display: block;">A cidade não pode conter apenas números.</div>');
                }
            } else {
                cityField.addClass('is-valid');
                cityField.siblings('.city-error').remove();
            }
        } else {
            cityField.siblings('.city-error').remove();
        }
    });
    
    // Validação de região (não pode ser só números)
    $('#region').on('input blur', function() {
        const region = $(this).val().trim();
        const regionField = $(this);
        
        regionField.removeClass('is-invalid is-valid');
        
        if (region.length > 0) {
            if (/^\d+$/.test(region)) {
                regionField.addClass('is-invalid');
                if (!regionField.siblings('.invalid-feedback.region-error').length) {
                    regionField.after('<div class="invalid-feedback region-error" style="display: block;">A região não pode conter apenas números.</div>');
                }
            } else {
                // Verificar se é uma região válida
                const validRegions = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
                if (validRegions.includes(region)) {
                    regionField.addClass('is-valid');
                    regionField.siblings('.region-error').remove();
                }
            }
        } else {
            regionField.siblings('.region-error').remove();
        }
    });
    
    // Validação do formulário antes do envio
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Validar telefone
        const phone = $('#phone').val().replace(/\D/g, '');
        if (phone.length > 0 && phone.length !== 10 && phone.length !== 11) {
            $('#phone').addClass('is-invalid');
            $('#phone-error').show();
            isValid = false;
        }
        
        // Validar data de nascimento
        const birthDate = $('#birth_date').val();
        if (birthDate) {
            const selectedDate = new Date(birthDate);
            const todayDate = new Date();
            todayDate.setHours(23, 59, 59, 999); // Final do dia
            if (selectedDate > todayDate) {
                $('#birth_date').addClass('is-invalid');
                $('#birth-date-error').show();
                isValid = false;
            }
        }
        
        // Validar cidade
        const city = $('#service_city').val().trim();
        if (city && /^\d+$/.test(city)) {
            $('#service_city').addClass('is-invalid');
            if (!$('#service_city').siblings('.city-error').length) {
                $('#service_city').after('<div class="invalid-feedback city-error" style="display: block;">A cidade não pode conter apenas números.</div>');
            }
            isValid = false;
        }
        
        // Validar região
        const region = $('#region').val();
        if (region) {
            const validRegions = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
            if (!validRegions.includes(region)) {
                $('#region').addClass('is-invalid');
                if (!$('#region').siblings('.region-error').length) {
                    $('#region').after('<div class="invalid-feedback region-error" style="display: block;">Selecione uma região válida.</div>');
                }
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll para o primeiro erro
            $('.is-invalid').first().focus();
            
            // Mostrar alerta
            if (!$('.alert-validation').length) {
                $('.card-body').prepend(`
                    <div class="alert alert-danger alert-validation alert-dismissible fade show" role="alert">
                        <strong>Erro!</strong> Por favor, corrija os campos destacados em vermelho.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
            }
        }
    });
});
</script>
@endsection
