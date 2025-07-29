<!-- /documents/user-historyes/01-3-disparo-condicional-por-status.md -->

# 01.3 – Disparar Webhook **somente** quando o Status da Inscrição corresponder às regras

## História do Usuário
> **Como** atendente interno  
> **quero** que o webhook da Inscrição seja enviado **apenas** quando o status da Inscrição coincidir com o **status-gatilho** configurado no Produto  
> **para** evitar integrações indevidas e garantir que sistemas externos só recebam eventos válidos.

---

## Contexto
- Cada Produto possui os campos configuráveis:  
  - **webhook_url**  
  - **webhook_token**  
  - **status_gatilho** (ex.: “Ativo”, “Concluído”)  
- O disparo deve ocorrer tanto na **criação** quanto na **atualização** da Inscrição **somente** se `inscricao.status === produto.status_gatilho`.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Disparo condicional do webhook pelo status
  Cenário: Status coincide com gatilho
    Dado um produto com status_gatilho "Ativo" e webhook configurado
    E estou autenticado como atendente
    Quando crio uma inscrição com status "Ativo"
    Então o webhook é enfileirado para disparo

  Cenário: Status não coincide
    Dado um produto com status_gatilho "Ativo"
    Quando crio uma inscrição com status "Pendente"
    Então nenhum webhook é enfileirado
    E não existe registro correspondente em `webhook_logs`

  Cenário: Atualização muda para status-gatilho
    Dado uma inscrição com status "Pendente"
    Quando atualizo o status para "Ativo"
    Então o webhook é enfileirado exatamente uma vez
Definition of Done (DoD)
 Adicionar verificação if ($inscricao->status === $produto->status_gatilho) antes de despachar o Job.

 Cobertura de testes PHPUnit/Pest para:

Criação com status válido → job enfileirado.

Criação com status inválido → nenhum job.

Atualização que altera para status válido → job único.

 Atualizar Seeder para incluir produtos com status_gatilho para testes locais.

 Documentação deste arquivo concluída e referenciada.

 Checkbox 01.3 marcado DONE após validação do usuário.

Notas Técnicas
Item	Detalhe
Implementação	Lógica no Listener antes de chamar DispatchInscricaoWebhook::dispatch().
Fila	Mantém fila webhooks; se não há disparo, não cria job.
Logs	Não registrar nada quando status não corresponde (silencioso).
Extensibilidade	Campo status_gatilho pode armazenar lista CSV futura (Em análise,Ativo).