<!-- /documents/user-historyes/03-3-associar-conversa-cliente-contato.md -->

# 03.3 – Associar Conversa a **Clientes** ou **Contatos**

## História do Usuário
> **Como** atendente interno  
> **quero** que cada conversa de WhatsApp esteja vinculada ao **Cliente** ou **Contato** correto no sistema  
> **para** visualizar o histórico completo desse relacionamento, acessar dados cadastrais em um clique e garantir métricas fiéis por pessoa.

---

## Contexto

| Item                    | Regra / Implementação                                                                                     |
|-------------------------|-----------------------------------------------------------------------------------------------------------|
| **Identificador**       | Número WhatsApp (`contact_phone`) é a chave principal de vínculo.                                         |
| **Tabela**              | `whatsapp_conversations` recebe colunas `cliente_id` **OU** `contato_id` (apenas uma não-nula).           |
| **Auto-associação**     | Se o número já existir em `clientes.telefone` **ou** `contatos.telefone`, vincular automaticamente.        |
| **Múltiplos matches**   | Se houver mais de um cadastro com o mesmo número, exibir modal para o atendente escolher.                  |
| **Nenhum match**        | Mostrar botão **“Criar Cliente/Contato”** pré-preenchido com telefone.                                    |
| **Mudança de vínculo**  | Atendente com permissão `manage_conversations` pode **reassociar** conversa a outro cliente/contato.      |
| **Logs**                | Registrar em `conversation_links` (conversation_id, old_id, new_id, tipo, user_id, timestamp).            |
| **Exibição UI**         | No cabeçalho do Painel de Chat, clicar no nome abre _off-canvas_ com dados do Cliente/Contato.             |

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Vínculo de conversa a cadastro interno
  Cenário: Associação automática única
    Dado que existe um cliente com telefone "+5511998765432"
    Quando chega primeira mensagem desse número
    Então o sistema cria conversa vinculada ao cliente correspondente
    E no cabeçalho do chat exibe o nome do cliente

  Cenário: Múltiplos cadastros para o mesmo número
    Dado que existem dois contatos com telefone "+551112345678"
    Quando chega mensagem desse número
    Então aparece modal "Selecione Cliente/Contato"
    E o atendente escolhe um
    E o vínculo é salvo

  Cenário: Nenhum cadastro encontrado
    Quando chega mensagem de número desconhecido
    Então o cabeçalho mostra "Contato não cadastrado"
    E vejo botão "Criar Cliente" e "Criar Contato"
    Quando clico em "Criar Cliente"
    Então sou redirecionado ao formulário de cliente com telefone já preenchido

  Cenário: Reassociar conversa
    Dado uma conversa já vinculada ao contato A
    Quando clico em "Alterar vínculo"
    E seleciono cliente B
    Então a conversa passa a mostrar nome do cliente B
    E surge registro em conversation_links com meu user_id
Definition of Done (DoD)
 Migration adiciona cliente_id, contato_id à whatsapp_conversations + índice em contact_phone.

 Seed parser para normalizar telefones (+55DDDNXXXXXXXX) antes de comparar.

 Serviço ConversationLinker executa regras de auto-associação; disparado no webhook de mensagem.

 Modal Seleção de Cadastro componente Vue/Livewire com busca em tempo real.

 Botões “Criar Cliente/Contato” redirecionam com querystring phone.

 Permissão manage_conversations controla quem pode alterar vínculo.

 Logs gravados em conversation_links (auditoria).

 Testes PHPUnit/Pest: auto-associate, multi-match, no-match, reassociation.

 Testes Dusk: fluxo modal, criar cliente a partir de conversa.

 Documentação concluída (este arquivo) + update no README (“Vínculo de conversa”).

 Checkbox 03.3 no /documents/todos/HEAD_CS_2025-07.md marcado DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Normalização	Use libphonenumber via propaganistas/laravel-phone para padronizar e validar números.
Eager Load	Carregar cliente/contato nos queries da conversa para evitar N+1.
UI Component	Badge “novo” para número não cadastrado; ícone de corrente 🔗 para alterar vínculo.
API	POST /api/conversations/{id}/link body {type: 'cliente', id: 42}.
Cache	Cache lookup de número → id (TTL 10 min) para reduzir queries em lote de mensagens históricas.