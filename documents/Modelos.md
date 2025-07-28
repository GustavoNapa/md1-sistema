## Model ↔ Tabela SQL ↔ Relações Eloquent
| Model                | Tabela SQL              | Relações Eloquent           |
|----------------------|------------------------|-----------------------------|
| Client               | clients                 | hasMany: inscriptions, phones, emails, companies |
| Inscription          | inscriptions            | belongsTo: client, vendor, product; hasMany: payments, sessions, documents, achievements, followUps |
| Payment              | payments                | belongsTo: inscription      |
| Session              | sessions                | belongsTo: inscription      |
| Achievement          | achievements            | belongsTo: inscription, achievementType |
| AchievementType      | achievement_types       | hasMany: achievements       |
| Document             | inscription_documents   | belongsTo: inscription      |
| Vendor               | vendors                 | hasMany: inscriptions       |
| Role                 | roles                   | hasMany: users              |
| Permission           | permissions             | belongsToMany: roles        |
| User                 | users                   | belongsTo: role             |
| EntryChannel         | entry_channels          | hasMany: inscriptions       |

## Campos Importantes e Casts
- Dates: `created_at`, `updated_at`, datas customizadas
- Boolean: `is_verified`, `is_required`, `active`
- Enum: `status`, `category`, `type`

## Fábricas/Seeders Relevantes
- database/seeders/UserSeeder.php
- database/seeders/RoleSeeder.php
- database/seeders/PermissionSeeder.php
- database/seeders/SpecialtySeeder.php
