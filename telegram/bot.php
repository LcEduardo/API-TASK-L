<?php

// Uso: php telegram/bot.php   (Ctrl+C para parar)

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/commands.php';

use App\Controller\ControllerTask;
use App\Model\Task;

$controllerTask = new ControllerTask(new Task($db));

$api = 'https://api.telegram.org/bot' . $_ENV['TOKEN_TELEGRAM'] . '/';
$offset = 0;

echo "Bot rodando...\n";

while (true) {
    $updates = json_decode(file_get_contents($api . "getUpdates?timeout=30&offset=$offset"), true);

    foreach ($updates['result'] as $update) {
        $offset = $update['update_id'] + 1;

        $chatId = $update['message']['chat']['id'] ?? null;
        $texto = $update['message']['text'] ?? '';

        if (!$chatId) {
            continue;
        }

        // "/getTask 1" -> comando "/getTask", argumento "1"
        $partes = explode(' ', trim($texto), 2);
        $comando = $partes[0];
        $argumento = trim($partes[1] ?? '');

        $resposta = match ($comando) {
            '/start'   => 'Olá! Eu sou o seu bot.',
            '/help'    => "Comandos:\n/start\n/help\n/getTask id",
            '/getTask' => getTask($controllerTask, $argumento),
            default  => "Você disse: $texto",
        };

        file_get_contents($api . 'sendMessage?' . http_build_query([
            'chat_id' => $chatId,
            'text'    => $resposta,
        ]));
    }
}
