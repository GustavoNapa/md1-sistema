<!-- /documents/user-historyes/03-1-configurar-credenciais-env.md -->

# 03.1 – Configurar credenciais da Evolution API no `.env`

## História do Usuário
> **Como** administrador técnico  
> **quero** informar todas as variáveis de ambiente necessárias para a Evolution API no arquivo `.env`  
> **para** que o sistema se conecte com segurança às instâncias do WhatsApp, sem hard-code de tokens no código-fonte.

---

## Contexto
Para cada instância (número) a Evolution API devolve `instanceId` e `token`.  
Algumas chaves globais também são exigidas:

| Variável               | Descrição                                  | Exemplo / Observação                                |
|------------------------|--------------------------------------------|-----------------------------------------------------|
| `EVOLUTION_BASE_URL`   | URL raiz da API Evolution                  | `https://evolution.gmc.app.br`                      |
| `EVOLUTION_API_KEY`    | Chave de acesso master (se houver)        | (fornecida pelo provedor)                           |
| `EVOLUTION_SANDBOX`    | `true/false` para alternar sandbox        | `true` em ambiente de teste                         |
| `EVOLUTION_TIMEOUT`    | Timeout (seg.) das requisições            | `30`                                               |
| `EVOLUTION_WEBHOOK_URL`| Endpoint público para receber callbacks   | `https://app.md1.com.br/api/webhooks/evolution`     |

As credenciais são carregadas no **config/services.php** e utilizadas pelo `EvolutionClient`.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Configuração de credenciais Evolution
  Cenário: Variáveis presentes no .env.example
    Dado que abro o arquivo .env.example
    Então vejo as chaves EVOLUTION_BASE_URL, EVOLUTION_API_KEY, EVOLUTION_SANDBOX, EVOLUTION_TIMEOUT, EVOLUTION_WEBHOOK_URL

  Cenário: Bootstrap do cliente lê variáveis
    Dado que preencho .env com valores válidos
    Quando o serviço EvolutionClient é instanciado
    Então o atributo baseUrl é igual ao valor de EVOLUTION_BASE_URL
    E o timeout é igual a EVOLUTION_TIMEOUT

  Cenário: Falta de variável gera exceção clara
    Dado que a variável EVOLUTION_BASE_URL não está definida
    Quando o container resolve EvolutionClient
    Então recebo exceção "Evolution base URL não configurado"
Definition of Done (DoD)
 .env.example atualizado com chaves Evolution e comentários explicativos.

 config/services.php adiciona seção:

php
Copiar
Editar
'evolution' => [
    'base_url'   => env('EVOLUTION_BASE_URL'),
    'api_key'    => env('EVOLUTION_API_KEY'),
    'sandbox'    => (bool) env('EVOLUTION_SANDBOX', false),
    'timeout'    => (int) env('EVOLUTION_TIMEOUT', 30),
    'webhook'    => env('EVOLUTION_WEBHOOK_URL'),
],
 EvolutionClient injeta config e lança exceções amigáveis se variáveis faltarem.

 Pipeline CI carrega secrets (EVOLUTION_*) via GitHub Actions Secrets.

 Teste PHPUnit confirma leitura correta das variáveis e exceção quando ausente.

 Documento (este arquivo) finalizado; README seção “Configuração WhatsApp” atualizada.

 Checkbox 03.1 marcado DONE após revisão e merge.

Notas Técnicas
Item	Detalhe
Segurança	Em produção, valores devem vir de Vault ou Secrets Manager (não commit).
Hot-reload	Usar config caching (php artisan config:cache) após alterar variáveis.
Sandbox	Quando EVOLUTION_SANDBOX=true, EvolutionClient troca base URL e logs em nível debug.
Timeout	Alinhado ao retryUntil das filas; recomenda-se 30 s.
