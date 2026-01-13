@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Lead</h4>
                    <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('leads.update', $lead) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $lead->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" required>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" id="is_whatsapp" name="is_whatsapp"
                                           {{ old('is_whatsapp', $lead->is_whatsapp) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_whatsapp">
                                        <i class="fab fa-whatsapp text-success"></i> Este número é WhatsApp
                                    </label>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $lead->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="origin" class="form-label">Origem *</label>
                            <select class="form-select @error('origin') is-invalid @enderror" 
                                    id="origin" name="origin" required>
                                <option value="">Selecione a origem</option>
                                <option value="campanha" {{ old('origin', $lead->origin) == 'campanha' ? 'selected' : '' }}>Campanha</option>
                                <option value="email" {{ old('origin', $lead->origin) == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="facebook" {{ old('origin', $lead->origin) == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="indicacao" {{ old('origin', $lead->origin) == 'indicacao' ? 'selected' : '' }}>Indicação</option>
                                <option value="instagram" {{ old('origin', $lead->origin) == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="whatsapp" {{ old('origin', $lead->origin) == 'whatsapp' ? 'selected' : '' }}>Whatsapp</option>
                                <option value="outro" {{ old('origin', $lead->origin) == 'outro' ? 'selected' : '' }}>Outro (especificar)</option>
                            </select>
                            @error('origin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="origin_other_div" style="display: {{ old('origin', $lead->origin) == 'outro' ? 'block' : 'none' }};">
                            <label for="origin_other" class="form-label">Outro (especificar)</label>
                            <input type="text" class="form-control" id="origin_other" name="origin_other" 
                                   value="{{ old('origin_other', $lead->origin_other) }}"
                                   placeholder="Especifique a origem">
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Responsável</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Nenhum responsável</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('user_id', $lead->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar Lead</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('origin').addEventListener('change', function() {
    const otherDiv = document.getElementById('origin_other_div');
    if (this.value === 'outro') {
        otherDiv.style.display = 'block';
    } else {
        otherDiv.style.display = 'none';
    }
});
</script>
@endsection
