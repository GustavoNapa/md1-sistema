@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalhes do Produto</h4>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>{{ $product->name }}</h5>
                            @if($product->description)
                                <p class="text-muted">{{ $product->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                                {{ $product->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">Preço Normal</h6>
                                    <h4 class="text-primary">{{ $product->formatted_price }}</h4>
                                </div>
                            </div>
                        </div>
                        @if($product->offer_price)
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Preço de Oferta</h6>
                                        <h4>{{ $product->formatted_offer_price }}</h4>
                                        <small>
                                            Economia: R$ {{ number_format($product->price - $product->offer_price, 2, ',', '.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h6>Inscrições Vinculadas ({{ $product->inscriptions->count() }})</h6>
                        @if($product->inscriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Status</th>
                                            <th>Data Início</th>
                                            <th>Valor Pago</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->inscriptions as $inscription)
                                            <tr>
                                                <td>{{ $inscription->client->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $inscription->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($inscription->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($inscription->amount_paid)
                                                        R$ {{ number_format($inscription->amount_paid, 2, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('inscriptions.show', $inscription) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Nenhuma inscrição vinculada a este produto.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
