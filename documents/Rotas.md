## Web
| Método | URI                                 | Controller@Action                | Middleware         |
|--------|-------------------------------------|----------------------------------|--------------------|
| GET    | /clients                           | ClientController@index           | auth               |
| POST   | /clients                           | ClientController@store           | auth               |
| GET    | /clients/create                    | ClientController@create          | auth               |
| PUT    | /clients/{client}                  | ClientController@update          | auth               |
| DELETE | /clients/{client}                  | ClientController@destroy         | auth               |
| GET    | /inscriptions                      | InscriptionController@index      | auth               |
| POST   | /inscriptions                      | InscriptionController@store      | auth               |
| GET    | /inscriptions/create               | InscriptionController@create     | auth               |
| PUT    | /inscriptions/{inscription}        | InscriptionController@update     | auth               |
| DELETE | /inscriptions/{inscription}        | InscriptionController@destroy    | auth               |
| ...    | ...                                | ...                              | ...                |

## API
| Método | URI                                 | Controller@Action                | Middleware         |
|--------|-------------------------------------|----------------------------------|--------------------|
| POST   | /api/subscriptions/{id}/bonuses     | BonusController@store/update     | api, auth:sanctum  |
| DELETE | /api/subscriptions/{id}/bonuses/{id}| BonusController@destroy          | api, auth:sanctum  |
| ...    | ...                                | ...                              | ...                |

## Middleware Aplicados
- auth
- permission:manage-permissions
- permission:manage-roles
- permission:manage-users
- api, auth:sanctum
