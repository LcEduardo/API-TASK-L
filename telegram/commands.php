<?php

// Fica separado do bot.php para os testes poderem carregar sem entrar no loop

use App\Controller\ControllerTask;

function getTask(ControllerTask $controllerTask, string $id): string {
    // "/getTask abc" chegaria no Postgres como erro de tipo, então valida antes
    if (!ctype_digit($id)) {
        return "Uso: /getTask id (ex: /getTask 1)";
    }

    $response = $controllerTask->getTask($id);

    if ($response['status'] !== 200) {
        return "Erro: {$response['body']['error']}";
    }

    $t = $response['body'];

    return "Task #{$t['id']}\n"
         . "Título: {$t['title']}\n"
         . "Status: {$t['status']}\n"
         . "Descrição: {$t['description']}";
}
