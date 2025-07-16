@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Novo Cliente</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('clients.store') }}" id="clientForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF *</label>
                                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                           id="cpf" name="cpf" value="{{ old('cpf') }}" required>
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
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="(11) 99999-9999" maxlength="15">
                                    @error('phone')
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
                                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="specialty" class="form-label">Especialidade</label>
                                    <select class="form-select @error('specialty') is-invalid @enderror" 
                                            id="specialty" name="specialty">
                                        <option value="">Selecione uma especialidade</option>
                                        @foreach($specialties as $value => $label)
                                            <option value="{{ $value }}" {{ old('specialty') == $value ? 'selected' : '' }}>
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
                                           id="service_city" name="service_city" value="{{ old('service_city') }}"
                                           pattern="^(?!^\d+$).+" title="A cidade não pode conter apenas números">
                                    @error('service_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="state" class="form-label">UF</label>
                                    <select class="form-select @error('state') is-invalid @enderror" 
                                            id="state" name="state">
                                        <option value="">UF</option>
                                        <option value="AC" {{ old('state') == 'AC' ? 'selected' : '' }}>AC</option>
                                        <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>AL</option>
                                        <option value="AP" {{ old('state') == 'AP' ? 'selected' : '' }}>AP</option>
                                        <option value="AM" {{ old('state') == 'AM' ? 'selected' : '' }}>AM</option>
                                        <option value="BA" {{ old('state') == 'BA' ? 'selected' : '' }}>BA</option>
                                        <option value="CE" {{ old('state') == 'CE' ? 'selected' : '' }}>CE</option>
                                        <option value="DF" {{ old('state') == 'DF' ? 'selected' : '' }}>DF</option>
                                        <option value="ES" {{ old('state') == 'ES' ? 'selected' : '' }}>ES</option>
                                        <option value="GO" {{ old('state') == 'GO' ? 'selected' : '' }}>GO</option>
                                        <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>MA</option>
                                        <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>MT</option>
                                        <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>MS</option>
                                        <option value="MG" {{ old('state') == 'MG' ? 'selected' : '' }}>MG</option>
                                        <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>PA</option>
                                        <option value="PB" {{ old('state') == 'PB' ? 'selected' : '' }}>PB</option>
                                        <option value="PR" {{ old('state') == 'PR' ? 'selected' : '' }}>PR</option>
                                        <option value="PE" {{ old('state') == 'PE' ? 'selected' : '' }}>PE</option>
                                        <option value="PI" {{ old('state') == 'PI' ? 'selected' : '' }}>PI</option>
                                        <option value="RJ" {{ old('state') == 'RJ' ? 'selected' : '' }}>RJ</option>
                                        <option value="RN" {{ old('state') == 'RN' ? 'selected' : '' }}>RN</option>
                                        <option value="RS" {{ old('state') == 'RS' ? 'selected' : '' }}>RS</option>
                                        <option value="RO" {{ old('state') == 'RO' ? 'selected' : '' }}>RO</option>
                                        <option value="RR" {{ old('state') == 'RR' ? 'selected' : '' }}>RR</option>
                                        <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="SP" {{ old('state') == 'SP' ? 'selected' : '' }}>SP</option>
                                        <option value="SE" {{ old('state') == 'SE' ? 'selected' : '' }}>SE</option>
                                        <option value="TO" {{ old('state') == 'TO' ? 'selected' : '' }}>TO</option>
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Região</label>
                                    <select class="form-select @error('region') is-invalid @enderror" 
                                            id="region" name="region">
                                        <option value="">Selecione uma região</option>
                                        <option value="Norte" {{ old('region') == 'Norte' ? 'selected' : '' }}>Norte</option>
                                        <option value="Nordeste" {{ old('region') == 'Nordeste' ? 'selected' : '' }}>Nordeste</option>
                                        <option value="Centro-Oeste" {{ old('region') == 'Centro-Oeste' ? 'selected' : '' }}>Centro-Oeste</option>
                                        <option value="Sudeste" {{ old('region') == 'Sudeste' ? 'selected' : '' }}>Sudeste</option>
                                        <option value="Sul" {{ old('region') == 'Sul' ? 'selected' : '' }}>Sul</option>
                                    </select>
                                    @error('region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="text" class="form-control @error('instagram') is-invalid @enderror" 
                                   id="instagram" name="instagram" value="{{ old('instagram') }}" placeholder="@usuario">
                            @error('instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Cliente
                            </button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para telefone
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '');
            let formattedPhone = '';
            
            if (x.length > 0) {
                if (x.length <= 2) {
                    formattedPhone = `(${x}`;
                } else if (x.length <= 7) {
                    formattedPhone = `(${x.substring(0, 2)}) ${x.substring(2)}`;
                } else if (x.length <= 11) {
                    formattedPhone = `(${x.substring(0, 2)}) ${x.substring(2, 7)}-${x.substring(7)}`;
                } else {
                    formattedPhone = `(${x.substring(0, 2)}) ${x.substring(2, 7)}-${x.substring(7, 11)}`;
                }
            }
            
            e.target.value = formattedPhone;
        });
        
        // Validação para não aceitar apenas texto
        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && value.length < 10) {
                e.target.setCustomValidity('O telefone deve ter pelo menos 10 dígitos');
            } else {
                e.target.setCustomValidity('');
            }
        });
    }
    
    // Validação para cidade não aceitar apenas números
    const cityInput = document.getElementById('service_city');
    if (cityInput) {
        cityInput.addEventListener('input', function(e) {
            const value = e.target.value.trim();
            if (/^\d+$/.test(value)) {
                e.target.setCustomValidity('A cidade não pode conter apenas números');
            } else {
                e.target.setCustomValidity('');
            }
        });
    }
    
    // Validação de data de nascimento
    const birthDateInput = document.getElementById('birth_date');
    if (birthDateInput) {
        const today = new Date().toISOString().split('T')[0];
        birthDateInput.setAttribute('max', today);
        
        birthDateInput.addEventListener('change', function(e) {
            const selectedDate = new Date(e.target.value);
            const todayDate = new Date();
            
            if (selectedDate > todayDate) {
                e.target.setCustomValidity('A data de nascimento não pode ser no futuro');
            } else {
                e.target.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection