# Configuração do Banco de Dados Quiz/DISC

Este documento descreve como configurar a conexão com o banco de dados separado que contém os dados dos Testes DISC.

## Variáveis de Ambiente

Adicione as seguintes variáveis ao seu arquivo `.env` (tanto local quanto em produção):

```env
# Quiz/DISC Database Connection
QUIZ_DB_HOST=127.0.0.1
QUIZ_DB_PORT=3306
QUIZ_DB_DATABASE=medicocelebridad_disc
QUIZ_DB_USERNAME=medicocelebridad_disc
QUIZ_DB_PASSWORD=MLmW)Vyw5NDe
```

## Estrutura Implementada

### 1. Nova Conexão
Foi adicionada uma segunda conexão MySQL no arquivo `config/database.php` chamada `quiz_mysql`.

### 2. Modelo QuizResponse
O modelo `App\Models\QuizResponse` foi configurado para usar automaticamente a conexão `quiz_mysql`.

### 3. Tabela no Banco de Dados
A tabela `quiz_responses` deve existir no banco `medicocelebridad_sistema` com a seguinte estrutura:

```sql
CREATE TABLE quiz_responses (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NULL,
    name VARCHAR(255) NULL,
    answers JSON,
    summary JSON NULL,
    response_time_minutes INT NULL,
    report_html LONGTEXT NULL,
    report_filename VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Testando a Conexão

Você pode testar a conexão executando:

```bash
php artisan tinker
```

E então:

```php
\App\Models\QuizResponse::count()
```

Se retornar um número, a conexão está funcionando corretamente!

## Produção

**IMPORTANTE**: Certifique-se de adicionar as variáveis `QUIZ_DB_*` no arquivo `.env` do servidor de produção antes de fazer deploy.

No servidor de produção, adicione ao arquivo `.env`:

```env
QUIZ_DB_HOST=127.0.0.1
QUIZ_DB_PORT=3306
QUIZ_DB_DATABASE=medicocelebridad_disc
QUIZ_DB_USERNAME=medicocelebridad_disc
QUIZ_DB_PASSWORD=MLmW)Vyw5NDe
```

Após adicionar, limpe o cache de configuração:

```bash
php artisan config:cache
```
