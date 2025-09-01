@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Editar Plataforma de Pagamento</h2>
    <form method="POST" action="{{ route('payment_platforms.update', $platform->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nome *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $platform->name }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <input type="text" class="form-control" id="description" name="description" value="{{ $platform->description }}">
        </div>
        <button type="submit" class="btn btn-success">Atualizar</button>
        <a href="{{ route('payment_platforms.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
