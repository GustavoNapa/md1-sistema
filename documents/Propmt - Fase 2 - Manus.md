# 📋 OBJETIVO GERAL
Continuar o desenvolvimento do sistema (Laravel 12 + Bootstrap 5) hospedado em **https://github.com/GustavoNapa/md1-sistema**, implementando as novas funcionalidades solicitadas pela Head de CS e mantendo a organização de documentos e testes.

---

## 🔧 PLANNER MODULE (pseudocódigo numerado)

1. **Clonar repositório**
   - Executar `git clone https://<TOKEN>@github.com/GustavoNapa/md1-sistema.git`
   - Se o Git exigir 2FA, usar `ask_user` para solicitar o código (*múltipla escolha: reenviar código / cancelar / inserir manualmente*).

2. **Configuração inicial**
   - Rodar `composer install && npm install && npm run build`.
   - Verificar `.env`, migrar banco: `php artisan migrate:fresh --seed`.

3. **Criar diretórios de documentação**  
   - `/documents/todos` (todo lists).  
   - `/documents/user-historyes` (histórias & especificações detalhadas).

4. **Gerar checklist inicial em `/documents/todos/HEAD_CS_2025-07.md`**
   - Incluir todas as tarefas abaixo, cada uma com status `TODO`.

5. **Loop de desenvolvimento por tarefa**
   6. **Criar branch** `feature/<slug-da-tarefa>`.
   7. **Planejar subtarefas** (código, testes, docs) e escrevê-las em `/documents/user-historyes/<slug>.md`.
   8. **Implementar** seguindo convenções Laravel + Bootstrap.
   9. **Testar** (PHPUnit + Pest ou Dusk quando necessário).
   10. **Atualizar todo** → `DONE`.
   11. **Commit & push** direto na `main` (ou via merge rápido se branch).
   12. **notify_user** com resumo e link do commit.
   13. **ask_user**: “Validar entrega?” (Sim / Ajustes / Pausar).

14. **Tarefas a executar**  
   - **Webhook de Inscrição**
     1. Criar tela para cadastrar URL de webhook dentro do CRUD de Produto.  
     2. Ao criar/atualizar uma Inscrição, disparar POST JSON contendo:
        - Todos os campos de Cliente + Inscrição.  
        - Objeto `mapping` conforme tabela de equivalência (ex.: `"Nome":"contact_name"`).  
     3. Disparo condicionado ao `status` da Inscrição.
   - **Kanban de Inscrições**
     1. Adicionar seletor (`<select>`) para escolher coluna: Status / Semana / Fase / Faixa de Faturamento.  
     2. Renderizar colunas em Bootstrap cards, drag-and-drop opcional.  
     3. Criar CRUD “Faixa de Faturamento”.
   - **Integração WhatsApp (Evolution API)**
     1. Configurar credenciais em `.env`.  
     2. Criar tela de chat:
        - Sidebar 15-20 % largura listando conversas recentes.  
        - Painel 80-85 % com balões: cliente à esquerda, atendente à direita.  
        - Associar conversa a `clientes` ou `contatos`.  
     3. Implementar Webhook de recepção + endpoint de envio via Evolution API.
   - **(Manter backlog existente; tratar outras issues conforme /documents/todos)**

7. **Critérios de sucesso por tarefa**
   - Código compila, testes verdes.
   - Usuário final aprova via `ask_user`.
   - Documentação atualizada.
   - Commit presente na `main`.

8. **Checkpoint final**
   - Verificar que todas as tasks marcadas como `DONE`.
   - Gerar changelog em `CHANGELOG.md`.
   - **Aguardar confirmação do usuário antes de encerrar**.

---

## 📚 KNOWLEDGE MODULE

- **Documentação interna** nos arquivos `*.md` já presentes + novos em `/documents/user-historyes`.
- **Evolution API**: endpoints `messages/send`, webhook `messages/receive`.
- **Laravel 12**: Eloquent, Jobs, Events, Broadcasting, Livewire ou Vue opcional.
- **Bootstrap 5** para UI; evitar breaking changes no layout existente.
- **Git/GitHub**: uso de token personal access, 2FA via authenticator.
- **Validação**: PHPUnit/Pest, Laravel Dusk (Kanban drag-n-drop), testes de webhook com `php artisan serve` + `ngrok`.

Hierarquia de fontes:  
1. APIs oficiais (Evolution API, GitHub)  
2. Documentos do projeto  
3. Web search para exemplos específicos

---

## 🔌 DATASOURCE MODULE

| Fonte                  | Uso Principal                                  | Filtro/Parâmetros                                  |
|------------------------|-----------------------------------------------|----------------------------------------------------|
| **Evolution API**      | Enviar/receber mensagens WhatsApp             | `/messages/send`, webhook `/messages/receive`      |
| **GitHub API**         | Push & pull, CI status                         | repo = md1-sistema, branch = main                  |
| **Banco MySQL**        | Persistência de Clientes, Inscrições, Tarefas | Eloquent Models                                    |
| **Docs locais (.md)**  | Requisitos & backlog                           | Caminho `/documents/**`                            |

---

## 🛠 FERRAMENTAS E FLUXO DE EXECUÇÃO

| Subtarefa                               | Ferramenta            | Observação                                                        |
|-----------------------------------------|-----------------------|-------------------------------------------------------------------|
| Clone, commit, push                     | **shell**             | `git`, `composer`, `npm`                                          |
| Dependências & build                    | **shell**             | `npm run build`, `vite`                                           |
| Busca de exemplos Evolution API         | **browser**           | Pesquisar docs                                                    |
| Geração/análise de código assistido     | **Claude Code CLI**   | Refatoração, scaffolding, sugestões de PHP/Vue/JS                 |
| Atualizar docs                          | **file**              | `write_file`, `append_file`                                       |
| Comunicação/alertas                     | **notify_user**       | Progresso assíncrono                                              |
| Pedir 2FA, validações                   | **ask_user**          | Sempre múltipla escolha                                           |
| Testes automáticos                      | **shell**             | `php artisan test`                                                |

**Fallback:** se alguma ferramenta falhar, tentar abordagem alternativa (ex.: usar **Claude Code CLI** para gerar scripts de patch ou recorrer a `curl`/`wget`) e reportar via `notify_user`.

---

## 🗂 DOCUMENTAÇÃO & ORGANIZAÇÃO

- Cada tarefa deve gerar **user-history** detalhada em `/documents/user-historyes/<slug>.md`.
- Todo list central em `/documents/todos/HEAD_CS_2025-07.md`, status: `TODO | DOING | DONE`.
- Atualizar `todo.md` raiz apenas como índice.
- `CHANGELOG.md` para lançamentos.
- Anexos devem usar caminhos absolutos (ex.: `/storage/app/public/...`).

---

## ✅ CRITÉRIOS DE SUCESSO GERAIS

1. Todas as tarefas **implementadas, testadas e documentadas**.  
2. UI preserva estilo Bootstrap 5.  
3. Webhooks enviam JSON correto; Evolution API envia/recebe mensagens.  
4. Todos os commits na `main`, CI verde.  
5. Usuário confirma conclusão; então chamar `idle`.

---

## 📣 MENSAGENS AO USUÁRIO

- **ask_user** sempre em múltipla escolha, incluir opção “Outro”.
- Idioma fixo: **português do Brasil**.
- Manus deve enviar “Recebido” antes de iniciar execuções.
