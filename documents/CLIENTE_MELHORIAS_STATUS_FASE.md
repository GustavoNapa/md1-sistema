# Melhorias no Sistema de Clientes

## Data: 20/01/2026

## Resumo das Alterações

Implementação de melhorias no sistema de gerenciamento de clientes, incluindo novo status "Pausa", controle de fases e funcionalidades de busca avançada.

## 1. Novo Status de "Pausa"

### Descrição
Além dos status "Ativo" e "Inativo", foi incluída a opção "Pausa" para clientes que estão temporariamente inativos.

### Campos Adicionados
- `status` (string): Campo principal que substitui o antigo campo booleano `active`
  - Valores possíveis: `active`, `inactive`, `paused`
  - Padrão: `active`

### Funcionalidades
- Badge visual diferenciado para cada status
- Filtro específico na listagem de clientes
- Validações automáticas no formulário

## 2. Detalhamento da Pausa

### Campos Adicionados
- `pause_start_date` (date): Data de início da pausa **(obrigatório quando status = paused)**
- `pause_end_date` (date): Data de término da pausa (opcional)
- `pause_reason` (text): Motivo/observações sobre a pausa

### Funcionalidades
- Exibição automática dos campos de pausa quando status "Em Pausa" é selecionado
- Cálculo automático de dias restantes de pausa
- Validação: data de fim deve ser posterior à data de início
- Alertas visuais quando a pausa está vencida

### Métodos no Model Client
```php
isPaused() // Verifica se o cliente está em pausa
getRemainingPauseDays() // Retorna dias restantes (ou 0 se vencida)
```

## 3. Campo de "Fase"

### Descrição
Sistema de fases para acompanhamento do progresso do cliente, com cálculo automático das 27 semanas previstas.

### Campos Adicionados
- `phase` (string): Fase atual do cliente
  - Opções: `fase_1`, `fase_2`, `fase_3`, `fase_4`, `concluido`
- `phase_start_date` (date): Data de início da fase **(obrigatório quando fase é selecionada)**
- `phase_week` (integer): Semana atual dentro da fase (1-27), calculada automaticamente

### Fases Disponíveis
1. **Fase 1 - Inicial**: Primeiras semanas do programa
2. **Fase 2 - Desenvolvimento**: Desenvolvimento das habilidades
3. **Fase 3 - Consolidação**: Consolidação do aprendizado
4. **Fase 4 - Avançado**: Nível avançado
5. **Concluído**: Programa finalizado

### Cálculo Automático
O sistema calcula automaticamente a semana atual (1-27) baseado na `phase_start_date`:
```php
calculatePhaseWeek() // Calcula semana atual baseado na data de início
```

**Exemplo:**
- Data de início: 01/01/2026
- Data atual: 15/01/2026
- Semana calculada: 3/27

## 4. Ferramenta de Busca por Fase

### Filtros Adicionados na Listagem
- **Status**: Filtro por Ativo, Inativo ou Em Pausa
- **Fase**: Filtro por fase do cliente (Fase 1, Fase 2, etc.)
- **Especialidade**: Mantido filtro existente
- **Busca textual**: Mantida busca por nome, CPF, email e telefone

### Visualização na Listagem
A tabela de clientes agora exibe:
- Status com badge colorido
  - Verde: Ativo
  - Amarelo: Em Pausa
  - Vermelho: Inativo
- Fase com badge azul e indicação da semana (ex: "Semana 5/27")
- Dias restantes de pausa (quando aplicável)

## 5. Arquivos Modificados

### Backend
1. **Migration**: `2026_01_20_172359_add_pause_and_phase_fields_to_clients_table.php`
   - Adiciona novos campos ao banco de dados
   
2. **Migration**: `2026_01_20_173055_migrate_active_to_status_in_clients_table.php`
   - Migra dados do campo `active` para o novo campo `status`

3. **Model**: `app/Models/Client.php`
   - Adicionados campos ao `$fillable`
   - Adicionados casts para datas
   - Novos métodos:
     - `getStatusOptions()`: Lista opções de status
     - `getPhaseOptions()`: Lista opções de fases
     - `calculatePhaseWeek()`: Calcula semana atual
     - `isPaused()`: Verifica se está em pausa
     - `getRemainingPauseDays()`: Dias restantes de pausa
   - Atualizado `getStatusLabelAttribute()`

4. **Controller**: `app/Http/Controllers/ClientController.php`
   - Atualizados filtros no método `index()`
   - Adicionadas validações para novos campos em `store()` e `update()`
   - Cálculo automático de `phase_week` ao salvar

### Frontend
1. **Index**: `resources/views/clients/index.blade.php`
   - Adicionado filtro de fase
   - Atualizado filtro de status
   - Nova coluna "Fase" na tabela
   - Melhorada exibição de status com informações adicionais

2. **Create**: `resources/views/clients/create.blade.php`
   - Nova seção "Status e Acompanhamento"
   - Campos de status, fase e detalhes da pausa
   - JavaScript para controle de visibilidade dos campos de pausa
   - Validações em tempo real

3. **Edit**: `resources/views/clients/edit.blade.php`
   - Mesmas melhorias do formulário de criação
   - Exibição da semana atual calculada
   - Indicação de dias restantes de pausa
   - Campos pré-preenchidos com valores existentes

## 6. Validações Implementadas

### Regras de Validação
```php
'status' => 'nullable|in:active,inactive,paused'
'pause_start_date' => 'nullable|date|required_if:status,paused'
'pause_end_date' => 'nullable|date|after:pause_start_date'
'pause_reason' => 'nullable|string|max:500'
'phase' => 'nullable|string|max:50'
'phase_start_date' => 'nullable|date|required_with:phase'
```

### Validações JavaScript
- Exibição/ocultação automática de campos de pausa
- Marcação de campos obrigatórios conforme seleção
- Validação de datas em tempo real

## 7. Compatibilidade

### Campo Legado `active`
O campo booleano `active` foi mantido para compatibilidade com código existente:
- Migração automática de dados existentes
- Sincronização recomendada com o campo `status`
- Sugestão: usar campo `status` em novos desenvolvimentos

### Migração de Dados
Todos os clientes existentes tiveram seus dados migrados automaticamente:
- `active = 1` → `status = 'active'`
- `active = 0` → `status = 'inactive'`

## 8. Exemplos de Uso

### Buscar Clientes em Pausa
```php
$clientsEmPausa = Client::where('status', 'paused')->get();
```

### Buscar Clientes por Fase
```php
$clientesFase2 = Client::where('phase', 'fase_2')->get();
```

### Verificar se Cliente Está em Pausa
```php
if ($client->isPaused()) {
    $diasRestantes = $client->getRemainingPauseDays();
    // Fazer algo com a informação
}
```

### Calcular Semana Atual
```php
$semanaAtual = $client->calculatePhaseWeek();
// Retorna um número de 1 a 27
```

## 9. Próximos Passos Sugeridos

1. **Dashboard de Acompanhamento**
   - Gráfico mostrando distribuição de clientes por fase
   - Indicadores de clientes em pausa
   - Alertas para pausas vencidas

2. **Notificações Automáticas**
   - Email quando pausa estiver próxima do fim
   - Lembrete quando cliente completar uma fase

3. **Relatórios**
   - Relatório de progresso por fase
   - Análise de tempo médio em cada fase
   - Estatísticas de pausas

4. **Integração com Inscrições**
   - Vincular fase do cliente com status das inscrições
   - Atualização automática de fase baseada em progresso

## 10. Considerações Técnicas

### Performance
- Índices sugeridos para otimização:
  ```sql
  ALTER TABLE clients ADD INDEX idx_status (status);
  ALTER TABLE clients ADD INDEX idx_phase (phase);
  ALTER TABLE clients ADD INDEX idx_pause_dates (pause_start_date, pause_end_date);
  ```

### Testes
- Validar cálculo de semanas em diferentes cenários
- Testar filtros combinados
- Verificar comportamento de campos obrigatórios

### Segurança
- Validações server-side implementadas
- Sanitização de entradas de texto
- Validação de datas

---

**Documentação criada em:** 20/01/2026
**Autor:** Sistema MD1
**Versão:** 1.0
