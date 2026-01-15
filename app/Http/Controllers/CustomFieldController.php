<?php

namespace App\Http\Controllers;

use App\Models\FieldGroup;
use App\Models\CustomField;
use App\Models\Lead;
use App\Models\LeadCustomFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    // Retornar todos os grupos e campos para o modal de gerenciamento
    public function index($type = 'contato')
    {
        $groups = FieldGroup::with('customFields')
            ->where('type', $type)
            ->orderBy('order')
            ->get();

        return response()->json($groups);
    }

    // Criar novo grupo
    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:contato,empresa,negocio',
        ]);

        $maxOrder = FieldGroup::where('type', $validated['type'])->max('order') ?? 0;

        $group = FieldGroup::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'order' => $maxOrder + 1,
            'is_system' => false,
        ]);

        return response()->json([
            'success' => true,
            'group' => $group,
        ]);
    }

    // Criar novo campo
    public function storeField(Request $request)
    {
        $validated = $request->validate([
            'field_group_id' => 'required|exists:field_groups,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,monetary,rich_text,phone,select,multi_select',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        // Gerar identificador único
        $identifier = Str::slug($validated['name'], '_');
        $counter = 1;
        while (CustomField::where('identifier', $identifier)->exists()) {
            $identifier = Str::slug($validated['name'], '_') . '_' . $counter;
            $counter++;
        }

        $maxOrder = CustomField::where('field_group_id', $validated['field_group_id'])->max('order') ?? 0;

        $field = CustomField::create([
            'field_group_id' => $validated['field_group_id'],
            'name' => $validated['name'],
            'identifier' => $identifier,
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'order' => $maxOrder + 1,
            'is_system' => false,
            'is_required' => $validated['is_required'] ?? false,
        ]);

        $field->load('fieldGroup');

        return response()->json([
            'success' => true,
            'field' => $field,
        ]);
    }

    // Atualizar valor de campo customizado para um lead
    public function updateLeadFieldValue(Request $request, Lead $lead, CustomField $field)
    {
        $validated = $request->validate([
            'value' => 'nullable|string',
        ]);

        LeadCustomFieldValue::updateOrCreate(
            [
                'lead_id' => $lead->id,
                'custom_field_id' => $field->id,
            ],
            [
                'value' => $validated['value'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Valor atualizado com sucesso',
        ]);
    }

    // Deletar grupo (apenas se não for sistema)
    public function destroyGroup(FieldGroup $group)
    {
        if ($group->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um grupo do sistema',
            ], 403);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grupo excluído com sucesso',
        ]);
    }

    // Deletar campo (apenas se não for sistema)
    public function destroyField(CustomField $field)
    {
        if ($field->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um campo do sistema',
            ], 403);
        }

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campo excluído com sucesso',
        ]);
    }
}
