<?php

// Uso: php telegram/bot.php   (Ctrl+C para parar)

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();

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

        $resposta = match ($texto) {
            '/start' => 'Olá! Eu sou o seu bot.',
            '/help'  => "Comandos:\n/start\n/help",
            default  => "Você disse: $texto",
        };

        file_get_contents($api . 'sendMessage?' . http_build_query([
            'chat_id' => $chatId,
            'text'    => $resposta,
        ]));
    }
}
