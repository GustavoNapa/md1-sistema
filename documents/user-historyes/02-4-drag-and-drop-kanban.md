## Drag-and-Drop para Cards do Kanban

### 1. Critérios de aceitação

| ID    | Cenário                       | Resultado esperado                                                                                                                                                               |
|-------|-------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| DD-01 | Arrastar card entre colunas   | Card muda de coluna instantaneamente, contador das colunas é atualizado e aparece toast “Inscrição atualizada”.                                                              |
| DD-02 | Persistência no banco         | Campo correspondente (status, semana, fase ou faixa) é gravado em `inscricoes` e logado em `kanban_movements`.                                                               |
| DD-03 | Regras de restrição violadas  | Ao tentar um movimento proibido (p.ex. “Cancelada” → “Ativo”), o card retorna à coluna original e surge toast vermelho “Movimento não permitido”.                               |
| DD-04 | Idempotência                  | Arrastar para a mesma coluna não gera requisição nem log.                                                                                                                |
| DD-05 | Tempo-real multiusuário       | Outros atendentes conectados veem o card mudar via WebSocket em < 1 s.                                                                                                  |
| DD-06 | Acessibilidade                | Card recebe foco após o drop; operação também pode ser feita com setas + Enter.                                                                                          |
| DD-07 | Mobile                        | Arrastar via `press & move`; fallback: menu “Mover para…”.                                                                                                              |

### 2. Campos atualizados por tipo de coluna

| Coluna escolhida no seletor | Campo alterado em `inscricoes` | Observações                                |
|-----------------------------|--------------------------------|--------------------------------------------|
| Status                      | `status`                       | Enum: Pendente, Ativo, Concluído, Cancelada… |
| Semana                      | `semana`                       | Inteiro 1-52; alterar arrasta para semana alvo. |
| Fase                        | `fase_id` (FK)                 | FK para tabela `fases` (pipeline).         |
| Faixa de Faturamento        | `faixa_faturamento_id` (FK)    | FK criada na tarefa 02.3.                  |

### 3. Regras de negócio

#### Fluxo permitido por Status

```
Pendente  → Ativo   → Concluído
  ↑  ↳ Cancelada  (terminal)
```

- `Cancelada` é terminal: não pode voltar.
- `Concluído` permite voltar apenas para `Ativo` (ex.: reabertura).

#### Semana

- Só aceita deslocar para semanas futuras ou a atual.
- Não é possível retroceder para semana já encerrada (flag fechada).

#### Fase

- Ordem definida no pipeline. Só é permitido avançar ou retroceder até 1 fase por movimento (impede “pular” fases).

#### Faixa de Faturamento

- Livre, mas é validado que `valor_total` da inscrição caiba na faixa destino.
- Se não couber, mostra toast de erro.

#### Permissões

- Papel `atendente` pode mover; papel `leitura` só visualiza.
- Movimentos são validados por Policy `moveInscricao`.

### 4. Tela e integração

- **Mesma rota `/inscricoes`**: O componente `<kanban-board>` já renderizado pela view atual receberá o módulo de drag-and-drop (SortableJS/vuedraggable).

- **API**: `POST /api/inscricoes/{id}/move`

  ```json
  { "field": "status", "value": "Ativo" }
  ```
  retorna `200 { "ok":true }` ou `422` com motivo.

- **Broadcast**: Evento `InscricaoMoved` transmitido no canal `kanban`.

### 5. Fluxo técnico do drag-and-drop

1.  **start** – armazena coluna origem.
2.  **drop** – identifica coluna destino → valida regras localmente.
3.  **optimistic UI** – move card na tela.
4.  **AJAX** – chama endpoint; em erro `4xx`/`5xx` faz rollback visual.
5.  **log** – grava em `kanban_movements` (from, to, user_id, motivo).
6.  **broadcast** – notifica outros clientes.

