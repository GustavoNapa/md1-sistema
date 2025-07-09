# MD1 Academy - Sistema de Clientes

Sistema de gerenciamento de clientes para a MD1 Academy (antigo GMC), desenvolvido em Laravel 12 para controle de mentorias médicas.

## 🚀 Funcionalidades

### ✅ Implementado
- **Autenticação completa** com Laravel UI + Bootstrap
- **CRUD de Clientes** com todos os campos necessários
- **Sistema de Importação CSV** com template e validação
- **Banco de dados normalizado** seguindo diagrama ER
- **Interface responsiva** com Bootstrap 5
- **Relacionamentos** entre clientes, inscrições e vendedores

### 🔄 Em desenvolvimento
- CRUD completo de Inscrições
- Sistema de Webhooks (entrada e saída)
- Integração WhatsApp via Evolution API
- Upload de documentos com Spatie MediaLibrary
- Dashboard com métricas
- API REST para integrações

## 📊 Estrutura do Banco

O sistema segue o diagrama ER fornecido com as seguintes entidades principais:

- **Clients** - Dados cadastrais dos médicos
- **Inscriptions** - Inscrições em produtos/mentorias
- **Vendors** - Vendedores/consultores
- **Payments** - Controle financeiro
- **Sessions** - Sessões de mentoria
- **Documents** - Arquivos anexados
- **WhatsApp Messages** - Histórico de conversas

## 🛠️ Tecnologias

- **Laravel 12** - Framework PHP
- **PHP 8.1+** - Linguagem backend
- **SQLite** - Banco de dados (desenvolvimento)
- **Bootstrap 5** - Framework CSS
- **Maatwebsite/Excel** - Importação CSV/Excel
- **Laravel UI** - Scaffolding de autenticação

## 📦 Instalação

```bash
# Clone o repositório
git clone https://github.com/GustavoNapa/md1-sistema.git
cd md1-sistema

# Instale dependências
composer install
npm install

# Configure ambiente
cp .env.example .env
php artisan key:generate

# Execute migrations
php artisan migrate

# Compile assets
npm run build

# Inicie servidor
php artisan serve
```

## 📋 Importação de Dados

O sistema possui funcionalidade completa de importação via CSV:

1. Acesse `/import` no sistema
2. Baixe o template CSV
3. Preencha com os dados dos clientes
4. Faça upload do arquivo

### Campos da Importação

**Obrigatórios:**
- nome, cpf, e_mail

**Opcionais:**
- data_nasc, especialidade, cidade_atendimento
- uf, regiao, instagram, telefone
- vendedor, turma, status, classificacao
- medboss, crmb, datas, valores, observações

## 🔐 Acesso

- **URL:** https://8000-izd76c0vle0f4caesxgxp-5e2ead4c.manusvm.computer
- **Registro:** Criar conta na tela de registro
- **Login:** Email e senha configurados

## 📁 Estrutura do Projeto

```
md1clients/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Models Eloquent
│   └── Imports/             # Classes de importação
├── database/
│   └── migrations/          # Migrations do banco
├── resources/
│   └── views/               # Templates Blade
└── routes/
    └── web.php              # Rotas da aplicação
```

## 🎯 Próximos Passos

1. **Finalizar CRUD de Inscrições** com abas funcionais
2. **Implementar Webhooks** para integrações externas
3. **Integrar WhatsApp** via Evolution API
4. **Sistema de Upload** de documentos
5. **Dashboard** com métricas e relatórios
6. **Testes automatizados** (Feature + Unit)

## 📞 Suporte

Sistema desenvolvido para MD1 Academy seguindo especificações do diagrama ER e planilha de controle fornecidos.

---

**Versão:** 1.0.0  
**Laravel:** 12.x  
**PHP:** 8.1+  
**Última atualização:** Julho 2025

