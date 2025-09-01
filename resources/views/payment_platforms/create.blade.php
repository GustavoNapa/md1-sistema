@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Cadastrar Plataforma de Pagamento</h2>
    <form method="POST" action="{{ route('payment_platforms.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nome *</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <input type="text" class="form-control" id="description" name="description">
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('payment_platforms.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
