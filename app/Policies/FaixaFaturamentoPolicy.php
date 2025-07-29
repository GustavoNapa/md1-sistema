<?php

namespace App\Policies;

use App\Models\FaixaFaturamento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FaixaFaturamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Todos os usuários autenticados podem ver a lista
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FaixaFaturamento $faixaFaturamento): bool
    {
        // Todos os usuários autenticados podem ver uma faixa específica
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas admins podem criar
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FaixaFaturamento $faixaFaturamento): bool
    {
        // Apenas admins podem editar
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FaixaFaturamento $faixaFaturamento): bool
    {
        // Apenas admins podem excluir
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FaixaFaturamento $faixaFaturamento): bool
    {
        // Apenas admins podem restaurar
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FaixaFaturamento $faixaFaturamento): bool
    {
        // Apenas admins podem excluir permanentemente
        return $user->role === 'admin';
    }
}

