# API de Tasks

API simples em PHP puro com PostgreSQL para consultar tarefas (`tasks`).

## Estrutura

```
API/
├── index.php                  # Ponto de entrada: recebe a requisição e chama o controller
├── config/
│   ├── database.php           # Conexão com o banco via PDO
│   └── Schema.sql             # Tabelas workers, bosses e tasks + dados de exemplo
├── controller/
│   └── ControllerTask.php     # Regras da resposta (o que devolver e com qual status HTTP)
└── model/
    └── Tasks.php              # Acesso ao banco (consultas SQL)
```

A ideia é separar responsabilidades:

- **Model (`Task`)**: só conversa com o banco. Não sabe nada de HTTP.
- **Controller (`ControllerTask`)**: recebe o model, chama a consulta e decide a resposta e o código HTTP.
- **`index.php`**: olha o método HTTP da requisição e encaminha para o controller.

## Como rodar

1. Crie o banco `task` no PostgreSQL e execute `config/Schema.sql`.
2. Ajuste as credenciais em `config/database.php`.
3. Dentro da pasta `API`, suba o servidor embutido do PHP:

   ```
   php -S localhost:8000
   ```

4. Teste: `GET http://localhost:8000/?id=1`

## Métodos e funções usados, e por quê

### Conexão (`config/database.php`)

| O que | Por quê |
|---|---|
| `new PDO('pgsql:...')` | O PDO é a forma padrão do PHP de acessar bancos. Ele funciona com vários bancos e tem suporte a *prepared statements*. |
| `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` | Faz o PDO lançar exceções quando algo dá errado, em vez de falhar em silêncio. Assim o erro pode ser tratado com `try/catch`. |
| `try/catch` + `http_response_code(500)` | Se a conexão falhar, a API devolve um JSON com status 500 (erro do servidor) em vez de quebrar. |
| `exit` | Para a execução: sem banco, não faz sentido continuar. |

### Model (`model/Tasks.php`)

| O que | Por quê |
|---|---|
| `__construct(PDO $conn)` | O model recebe a conexão pronta (injeção de dependência) em vez de criar uma. Isso deixa a classe mais fácil de testar e reaproveitar. |
| `private PDO $conn` | A propriedade é tipada, então o PHP garante que é mesmo um PDO. |
| `prepare()` + `execute(['id' => $id])` | *Prepared statement*: o valor do `id` vai separado do SQL, o que protege contra **SQL Injection**. |
| `fetch(PDO::FETCH_ASSOC)` | Retorna uma linha como array associativo (`['title' => ...]`), que vira JSON direto. Se não encontrar nada, retorna `false`. |

### Controller (`controller/ControllerTask.php`)

| O que | Por quê |
|---|---|
| `__construct(Task $task)` | O controller recebe o model pronto, assim como o model recebe a conexão. Atenção: o nome precisa ser exatamente `__construct`, senão o PHP não chama o construtor automaticamente e a propriedade fica sem valor. |
| `if ($task == false)` | `fetch()` retorna `false` quando o id não existe. O controller detecta esse caso. |
| `http_response_code(404)` | Indica "não encontrado" no status HTTP real da resposta. É esse status que o cliente (Postman, front-end) vê, e não um campo dentro do JSON. |
| `http_response_code(200)` | Indica sucesso. |

### Entrada (`index.php`)

| O que | Por quê |
|---|---|
| `require __DIR__ . "/..."` | `__DIR__` é a pasta do arquivo atual, então o caminho funciona de qualquer lugar de onde o script for executado. |
| `header('Content-Type: application/json')` | Avisa o cliente que a resposta é JSON. |
| `switch ($_SERVER['REQUEST_METHOD'])` | Escolhe o que fazer de acordo com o método HTTP (GET, POST...). |
| `$_GET['id']` / `$_POST['id']` | Lê o id da URL (GET) ou do corpo do formulário (POST). |
| `json_encode()` | Converte o array PHP em JSON. |
| `http_response_code(405)` | *Method Not Allowed*: o cliente usou um método que a API não suporta. É um erro do cliente (4xx), não do servidor (5xx). |

## Status HTTP usados

| Código | Significado | Quando |
|---|---|---|
| 200 | OK | Task encontrada |
| 404 | Not Found | O id não corresponde a nenhuma task |
| 405 | Method Not Allowed | Método HTTP não suportado |
| 500 | Internal Server Error | Falha na conexão com o banco |

## Próximos passos

- Validar o `id` (ausente ou não numérico) e responder **400 Bad Request**.
- Fazer o `index.php` devolver a chave certa quando a resposta for de erro (`message`).
- Usar o POST para **criar** tasks, em vez de buscar.
