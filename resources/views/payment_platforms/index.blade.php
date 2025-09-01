@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Plataformas de Pagamento</h2>
    <a href="{{ route('payment_platforms.create') }}" class="btn btn-primary mb-3">Nova Plataforma</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($platforms as $platform)
            <tr>
                <td>{{ $platform->id }}</td>
                <td>{{ $platform->name }}</td>
                <td>{{ $platform->description }}</td>
                <td>
                    <a href="{{ route('payment_platforms.edit', $platform->id) }}" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
