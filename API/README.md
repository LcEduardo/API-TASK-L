# API de Tasks

API simples em PHP puro com PostgreSQL para consultar tarefas (`tasks`) e funcionários (`workers`).

## Estrutura

```
API/
├── index.php                  # Ponto de entrada e roteador: lê a URL e chama o controller certo
├── config/
│   ├── database.php           # Conexão com o banco via PDO
│   └── Schema.sql             # Tabelas workers, bosses e tasks + dados de exemplo
├── controller/
│   ├── ControllerTask.php     # Monta a resposta de tasks (corpo + status HTTP)
│   └── ControllerWorker.php   # Monta a resposta de workers (corpo + status HTTP)
└── model/
    ├── Tasks.php              # Consultas SQL de tasks
    └── Workers.php            # Consultas SQL de workers
```

## Como rodar

1. Crie o banco `task` no PostgreSQL e execute `config/Schema.sql`.
2. Ajuste as credenciais em `config/database.php`.
3. Dentro da pasta `API`, suba o servidor embutido do PHP:

   ```
   php -S localhost:8000
   ```

4. Teste:
   - `GET http://localhost:8000/tasks/1`
   - `GET http://localhost:8000/workers/1`

## Papel de cada camada

- **Root (`index.php`)**: é a porta de entrada. Lê o método HTTP e a URL, separa em segmentos (`/tasks/1` → recurso `tasks`, id `1`) e encaminha para o controller certo. Também valida se o id foi informado e responde quando a rota não existe ou o método não é suportado. No fim, aplica o status HTTP e devolve o JSON.
- **Controller (`ControllerTask`, `ControllerWorker`)**: recebe o model pronto, chama a consulta e transforma o resultado em resposta (`status` + `body`). É quem trata as exceções vindas do model e decide o código HTTP (200, 404, 500).
- **Model (`Task`, `Worker`)**: só conversa com o banco, usando *prepared statements*. Não sabe nada de HTTP: se não encontra o registro, lança uma exceção e deixa o controller decidir o que responder.

## Status HTTP usados

| Código | Significado | Quando |
|---|---|---|
| 200 | OK | Registro encontrado |
| 400 | Bad Request | O id não foi informado na URL (ex.: `/tasks`) |
| 404 | Not Found | O id não corresponde a nenhum registro, ou a rota não existe |
| 405 | Method Not Allowed | Método HTTP não suportado em `/tasks` ou `/workers` (ex.: `POST`) |
| 500 | Internal Server Error | Falha na conexão ou na consulta ao banco |

## Próximos passos

- Validar se o `id` é numérico e responder **400 Bad Request**.
- Usar o POST para **criar** tasks.
