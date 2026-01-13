@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalhes do Lead</h4>
                    <div>
                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Informações de Contato</h5>
                            <p><strong>Nome:</strong> {{ $lead->name }}</p>
                            <p>
                                <strong>Telefone:</strong> {{ $lead->formatted_phone }}
                                @if($lead->is_whatsapp)
                                    <i class="fab fa-whatsapp text-success"></i>
                                @endif
                            </p>
                            <p><strong>Email:</strong> {{ $lead->email ?? 'Não informado' }}</p>
                            <p><strong>Origem:</strong> {{ $lead->origin_label }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Informações do Pipeline</h5>
                            <p>
                                <strong>Pipeline:</strong> 
                                <i class="fas fa-circle" style="color: {{ $lead->pipeline->color }}; font-size: 0.6rem;"></i>
                                {{ $lead->pipeline->name }}
                            </p>
                            <p>
                                <strong>Etapa Atual:</strong> 
                                <i class="fas fa-circle" style="color: {{ $lead->stage->color }}; font-size: 0.6rem;"></i>
                                {{ $lead->stage->name }}
                            </p>
                            <p><strong>Responsável:</strong> {{ $lead->user->name ?? 'Não atribuído' }}</p>
                            <p><strong>Criado em:</strong> {{ $lead->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($lead->notes)
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Observações</h5>
                                <p class="text-muted">{{ $lead->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
