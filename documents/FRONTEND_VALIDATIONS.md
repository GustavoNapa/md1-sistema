# Validações Frontend e Backend - Sistema de Clientes

## Implementações Realizadas

### 1. Máscaras e Validações de Telefone/WhatsApp

#### Frontend (jQuery Mask):
- **Máscara aplicada**: `(00) 00000-0000`
- **Validação em tempo real**: 10 ou 11 dígitos
- **Feedback visual**: Campos ficam verdes (válidos) ou vermelhos (inválidos)
- **Placeholder**: `(11) 99999-9999`

#### Backend (Laravel):
- **Regex**: `/^[\d\s\(\)\-\+]+$/` - aceita dígitos, espaços, parênteses, hífens e sinal de mais
- **Máximo**: 20 caracteres
- **Nullable**: Campo opcional

#### Formatos Aceitos:
- `11999999999`
- `(11) 99999-9999`
- `11 99999-9999`
- `+55 11 99999-9999`

### 2. Validação de Data de Nascimento

#### Frontend:
- **Atributo HTML**: `max="{{ date('Y-m-d') }}"` - não permite datas futuras
- **Validação JavaScript**: Verifica se data selecionada > hoje
- **Feedback**: Mensagem de erro em tempo real

#### Backend:
- **Validação**: `before_or_equal:today`
- **Formato**: YYYY-MM-DD
- **Nullable**: Campo opcional

### 3. Especialidades (CFM)

#### Sistema:
- **Tabela própria**: `specialties` com nome, código CFM e status ativo
- **Select no frontend**: Dropdown com especialidades do banco
- **Validação**: `exists:specialties,name`

#### Especialidades Incluídas:
- Cardiologia, Dermatologia, Neurologia
- Oftalmologia, Ortopedia, Pediatria
- Psiquiatria, Ginecologia, Urologia
- Anestesiologia, e muitas outras...

### 4. Validação de Cidade

#### Frontend:
- **Validação**: Não pode conter apenas números
- **Regex**: `/^\d+$/` - rejeita strings apenas numéricas
- **Pattern HTML**: `^(?!^\d+$).+`

#### Backend:
- **not_regex**: `/^[0-9]+$/`
- **Mensagem**: "A cidade não pode conter apenas números"

### 5. Estado (UF)

#### Frontend:
- **Select dropdown**: Todos os estados brasileiros
- **Valores**: AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO

#### Backend:
- **Validação**: `in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO`
- **Tamanho**: Exatamente 2 caracteres

### 6. Região

#### Frontend:
- **Select dropdown**: Regiões brasileiras
- **Opções**: Norte, Nordeste, Centro-Oeste, Sudeste, Sul

#### Backend:
- **Validação dupla**: 
  - `not_regex:/^[0-9]+$/` (não apenas números)
  - `in:Norte,Nordeste,Centro-Oeste,Sudeste,Sul`

## Recursos JavaScript Implementados

### jQuery Mask Plugin:
```javascript
$('#phone').mask('(00) 00000-0000', {
    placeholder: '(11) 99999-9999',
    translation: {
        '0': {pattern: /[0-9]/}
    }
});

$('#cpf').mask('000.000.000-00', {
    placeholder: '000.000.000-00'
});
```

### Validações em Tempo Real:
- **Telefone**: Verifica 10 ou 11 dígitos
- **Data**: Não pode ser futura
- **Cidade**: Não apenas números
- **Região**: Validação de opções válidas

### Feedback Visual:
- Classes Bootstrap: `is-valid`, `is-invalid`
- Mensagens de erro específicas
- Scroll automático para o primeiro erro

## Testes Implementados

### Testes Feature:
1. **ClientFormValidationTest**: Validações completas do formulário
2. **ClientFrontendValidationTest**: Elementos de frontend (selects, masks)
3. **ClientCompleteWorkflowTest**: Fluxo completo de criação/edição

### Testes Unit:
1. **SpecialtyModelTest**: Modelo de especialidades
2. **ClientJavascriptValidationTest**: Lógica de validação JavaScript

### Cobertura:
- Validação de telefone (formatos válidos/inválidos)
- Data de nascimento (passado/futuro)
- Especialidades (existentes/inexistentes)
- Cidade (texto/apenas números)
- Estado (UF válidas/inválidas)
- Região (válidas/apenas números)
- Fluxo completo de CRUD

## Arquivos Modificados

### Views:
- `resources/views/clients/create.blade.php`
- `resources/views/clients/edit.blade.php`
- `resources/views/layouts/app.blade.php`

### Controllers:
- `app/Http/Controllers/ClientController.php`

### Models:
- `app/Models/Specialty.php`
- `app/Models/Client.php`

### Migrations:
- `database/migrations/create_specialties_table.php`

### Seeders:
- `database/seeders/SpecialtySeeder.php`

### Testes:
- `tests/Feature/ClientFormValidationTest.php`
- `tests/Feature/ClientFrontendValidationTest.php`
- `tests/Feature/ClientCompleteWorkflowTest.php`
- `tests/Unit/SpecialtyModelTest.php`
- `tests/Unit/ClientJavascriptValidationTest.php`

## Como Testar

### No Navegador:
1. Acesse `/clients/create`
2. Teste as máscaras de telefone e CPF
3. Tente inserir data futura
4. Tente inserir cidade com apenas números
5. Veja os selects de UF e região funcionando

### Testes Automatizados:
```bash
php artisan test --filter ClientFormValidationTest
php artisan test --filter ClientFrontendValidationTest
php artisan test --filter ClientCompleteWorkflowTest
```

## Validações Implementadas

### Telefone/WhatsApp:
✅ Máscara automática com jQuery  
✅ Validação de 10-11 dígitos  
✅ Feedback visual em tempo real  
✅ Aceita formatos diversos  

### Data de Nascimento:
✅ Não pode ser futura  
✅ Validação frontend e backend  
✅ Atributo max no HTML  

### Especialidade:
✅ Tabela própria com dados do CFM  
✅ Select dropdown  
✅ Validação exists no backend  

### Cidade:
✅ Não pode ser apenas números  
✅ Validação frontend e backend  

### Estado:
✅ Select com todas as UFs  
✅ Validação in no backend  

### Região:
✅ Select com 5 regiões brasileiras  
✅ Validação dupla (not_regex + in)  

Todas as validações estão funcionando tanto no frontend (JavaScript/jQuery) quanto no backend (Laravel), com testes automatizados cobrindo todos os cenários.
