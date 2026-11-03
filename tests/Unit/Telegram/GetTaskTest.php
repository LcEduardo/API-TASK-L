<?php

declare(strict_types=1);

namespace Tests\Unit\Telegram;

use App\Controller\ControllerTask;
use App\Model\Task;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../telegram/commands.php';

final class GetTaskTest extends TestCase
{
    public static function invalidIds(): array
    {
        return [
            'texto'     => ['abc'],
            'sem id'    => [''],
            'negativo'  => ['-1'],
            'decimal'   => ['1.5'],
        ];
    }

    // Regressão: sem a validação, "abc" ia para o Postgres e voltava "Database error"
    #[DataProvider('invalidIds')]
    public function testGetTaskReturnsUsageWhenIdIsNotNumeric(string $id): void
    {
        $task = $this->createMock(Task::class);
        $task->expects($this->never())->method('getById');

        $resposta = \getTask(new ControllerTask($task), $id);

        $this->assertSame('Uso: /getTask id (ex: /getTask 1)', $resposta);
    }

    // Regressão: task inexistente mostra a mensagem do model, não um erro genérico
    public function testGetTaskReturnsModelMessageWhenTaskDoesNotExist(): void
    {
        $task = $this->createStub(Task::class);
        $task->method('getById')->willThrowException(new Exception('Task with id 999 not found', 404));

        $resposta = \getTask(new ControllerTask($task), '999');

        $this->assertSame('Erro: Task with id 999 not found', $resposta);
    }

    public function testGetTaskReturnsTaskFormatted(): void
    {
        $task = $this->createStub(Task::class);
        $task->method('getById')->willReturn([
            'id'          => 1,
            'title'       => 'Create login page',
            'status'      => 'pending',
            'description' => 'Build the login form with validation.',
        ]);

        $resposta = \getTask(new ControllerTask($task), '1');

        $this->assertSame(
            "Task #1\nTítulo: Create login page\nStatus: pending\nDescrição: Build the login form with validation.",
            $resposta
        );
    }
}
