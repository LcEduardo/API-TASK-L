# Guia de uso da API de Tasks

Este guia é para quem vai **consumir** a API (um front-end, um app, um parceiro). Ele mostra o que pode ser chamado, o que enviar e o que esperar de volta.

## Visão geral

| | |
|---|---|
| **URL base** | `http://localhost:8000` (ambiente local; troque pelo endereço do servidor onde a API estiver publicada) |
| **Formato das respostas** | JSON (`Content-Type: application/json`) |
| **Formato do envio (POST)** | `application/x-www-form-urlencoded` ou `multipart/form-data`. **JSON no corpo ainda não é aceito.** |
| **Autenticação** | Nenhuma |

### Endpoints

| Método | Rota | O que faz |
|---|---|---|
| `GET` | `/tasks/{id}` | Busca uma task pelo id |
| `POST` | `/tasks` | Cria uma task |
| `GET` | `/workers/{id}` | Busca um funcionário pelo id |

Não existe rota para listar todos os registros, nem para editar ou apagar.

### Formato dos erros

Todo erro vem com o código HTTP correspondente e um corpo neste formato:

```json
{ "error": "Mensagem explicando o problema" }
```

---

## Tasks

### O objeto Task

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | inteiro | Identificador da task |
| `title` | texto | Título (até 150 caracteres) |
| `description` | texto ou `null` | Descrição livre |
| `status` | texto | `pending`, `in_progress` ou `done` |
| `created` | texto (data e hora) | Quando a task foi criada, ex.: `2026-10-29 14:05:12.481203` |
| `modified` | texto (data e hora) | Última alteração |
| `worker` | texto ou `null` | **Nome** do funcionário responsável, ou `null` se não houver |
| `boss` | texto ou `null` | **Nome** do chefe que criou a task |

> `worker` e `boss` vêm como nome, não como id.

### `GET /tasks/{id}`: buscar uma task

**Exemplo**

```
curl http://localhost:8000/tasks/1
```

**Resposta `200 OK`**

```json
{
  "id": 1,
  "title": "Create login page",
  "description": "Build the login form with validation.",
  "status": "pending",
  "created": "2026-10-29 14:05:12.481203",
  "modified": "2026-10-29 14:05:12.481203",
  "worker": "Ana Silva",
  "boss": "Fernando Alves"
}
```

**Possíveis erros**

| Código | Quando | Corpo |
|---|---|---|
| `400` | O id não foi informado (`/tasks`) | `{"error": "ID not provided"}` |
| `404` | Não existe task com esse id | `{"error": "Task with id 99 not found"}` |
| `500` | Falha no banco de dados. Hoje isso também acontece se o id não for um número (ex.: `/tasks/abc`) | `{"error": "Database error"}` |

### `POST /tasks`: criar uma task

Toda task nova começa com status `pending`.

**Campos do corpo**

| Campo | Obrigatório | Tipo | Descrição |
|---|---|---|---|
| `title` | **sim** | texto, até 150 caracteres | Título da task |
| `boss_id` | **sim** | inteiro | Id do chefe que está criando a task |
| `worker_id` | não | inteiro | Id do funcionário responsável. Se não enviar (ou enviar vazio), a task fica sem responsável |
| `description` | não | texto | Descrição livre |

**Exemplo com funcionário**

```
curl -X POST http://localhost:8000/tasks \
  -d "title=Revisar contrato" \
  -d "description=Conferir as cláusulas novas" \
  -d "boss_id=1" \
  -d "worker_id=2"
```

**Exemplo sem funcionário**

```
curl -X POST http://localhost:8000/tasks \
  -d "title=Planejar sprint" \
  -d "boss_id=1"
```

**Resposta `201 Created`**

Devolve a task criada, no mesmo formato do `GET /tasks/{id}`:

```json
{
  "id": 6,
  "title": "Planejar sprint",
  "description": null,
  "status": "pending",
  "created": "2026-10-29 15:20:44.102938",
  "modified": "2026-10-29 15:20:44.102938",
  "worker": null,
  "boss": "Fernando Alves"
}
```

Guarde o `id` da resposta se for consultar a task depois.

**Possíveis erros**

| Código | Quando | Corpo |
|---|---|---|
| `400` | `boss_id` não foi enviado ou não é um número inteiro | `{"error": "The boss_id isn't interger"}` |
| `400` | `worker_id` foi enviado mas não é um número inteiro | `{"error": "The worker_id isn't interger"}` |
| `404` | Não existe chefe com o `boss_id` informado | `{"error": "Boss with id 99 not found"}` |
| `404` | Não existe funcionário com o `worker_id` informado | `{"error": "Worker with id 99 not found"}` |

Se `boss_id` e `worker_id` estiverem errados ao mesmo tempo, só o erro do `boss_id` é devolvido.

> **Sempre envie `title`.** Hoje a API ainda não verifica esse campo antes de gravar. Sem ele, a task não é criada e a resposta de erro sai fora do padrão descrito acima.

---

## Workers (funcionários)

### `GET /workers/{id}`: buscar um funcionário

**Exemplo**

```
curl http://localhost:8000/workers/1
```

**Resposta `200 OK`**

```json
{
  "id": 1,
  "name": "Ana Silva",
  "email": "ana.silva@example.com",
  "created": "2026-10-29 14:05:12.481203"
}
```

**Possíveis erros**

| Código | Quando | Corpo |
|---|---|---|
| `400` | O id não foi informado (`/workers`) | `{"error": "ID not provided"}` |
| `400` | O id não é um número inteiro | `{"error": "The worker_id isn't interger"}` |
| `404` | Não existe funcionário com esse id | `{"error": "Worker with id 99 not found"}` |
| `500` | Falha no banco de dados | `{"error": "Database error"}` |

---

## Erros gerais

Valem para qualquer chamada:

| Código | Quando | Corpo |
|---|---|---|
| `404` | A rota não existe (ex.: `/bosses`, `/projetos`) | `{"error": "Route not found"}` |
| `405` | A rota existe, mas o método não é suportado (ex.: `DELETE /tasks/1`, `POST /workers`) | `{"error": "Method not allowed"}` |
| `500` | A API não conseguiu se conectar ao banco | `{"error": "Database connection failed", "code": ...}` |

## Resumo dos códigos HTTP

| Código | Significado para o cliente |
|---|---|
| `200` | Deu certo. O registro está no corpo |
| `201` | A task foi criada. Ela está no corpo |
| `400` | O pedido está incompleto ou com formato errado. Corrija e envie de novo |
| `404` | O registro ou a rota não existe |
| `405` | Esse método não é aceito nessa rota |
| `500` | Problema no servidor. Não é culpa do cliente; tente de novo mais tarde |

## Limitações atuais

- O corpo do `POST` precisa ser form-data ou x-www-form-urlencoded. JSON ainda não é aceito.
- Em `GET /tasks/{id}`, um id que não é número devolve `500` em vez de `400`.
- Criar uma task sem `title` não devolve uma mensagem de erro clara.
- A resposta `405` traz o cabeçalho `Allow: GET` mesmo em `/tasks`, que também aceita `POST`.
- Ainda não há rotas para listar, editar ou apagar registros, nem para consultar chefes (`/bosses`).
