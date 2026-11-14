<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\ControllerWorker;
use App\Model\Worker;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ControllerWorkerTest extends TestCase
{
    public function testGetWorkerReturns200WithTheWorker(): void
    {
        $row = ['id' => 1, 'name' => 'Ana Silva', 'email' => 'ana.silva@example.com'];

        // createStub cria um Worker falso: nada vai para o banco
        $worker = $this->createStub(Worker::class);
        $worker->method('getById')->willReturn($row);

        $response = (new ControllerWorker($worker))->getWorker('1');

        $this->assertSame(200, $response['status']);
        $this->assertSame($row, $response['body']);
    }

    public function testGetWorkerReturns400WhenIdIsNotInteger(): void
    {
        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())->method('getById');

        $response = (new ControllerWorker($worker))->getWorker('abc');

        $this->assertSame(400, $response['status']);
    }

    public function testGetWorkerReturnsNullBodyWhenIdIsNotSent(): void
    {
        // sem worker_id não deve nem consultar o banco
        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())->method('getById');

        $controller = new ControllerWorker($worker);

        $this->assertSame(['status' => 200, 'body' => null], $controller->getWorker(null));
        $this->assertSame(['status' => 200, 'body' => null], $controller->getWorker(''));
    }

    public function testCreateWorkerSavesEmailInLowercase(): void
    {
        $worker = $this->createMock(Worker::class);
        $worker->expects($this->once())
            ->method('create')
            ->with(['name' => 'Ana', 'email' => 'ana@example.com'])
            ->willReturn(7);
        $worker->method('getById')->willReturn(['id' => 7]);

        $response = (new ControllerWorker($worker))->createWorker('  Ana ', 'ANA@Example.com');

        $this->assertSame(201, $response['status']);
    }

    public function testCreateWorkerReturns409WhenEmailAlreadyExists(): void
    {
        // o PDO do Postgres guarda o SQLSTATE como string em $code (23505 = email duplicado)
        $duplicate = new class ('duplicate key') extends PDOException {
            protected $code = '23505';
        };

        $worker = $this->createStub(Worker::class);
        $worker->method('create')->willThrowException($duplicate);

        $response = (new ControllerWorker($worker))->createWorker('Ana', 'ana@example.com');

        $this->assertSame(409, $response['status']);
    }

    public function testUpdateWorkerReturns200WithTheUpdatedWorker(): void
    {
        $row = ['id' => 3, 'name' => 'Ana', 'email' => 'ana@example.com'];

        $worker = $this->createMock(Worker::class);
        $worker->expects($this->once())
            ->method('update')
            ->with(3, ['name' => 'Ana', 'email' => 'ana@example.com']);
        $worker->method('getById')->with(3)->willReturn($row);

        $response = (new ControllerWorker($worker))->updateWorker('3', '  Ana ', 'ANA@Example.com');

        $this->assertSame(200, $response['status']);
        $this->assertSame($row, $response['body']);
    }

    public function testUpdateWorkerReturns400WhenIdIsNotInteger(): void
    {
        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())->method('update');

        $response = (new ControllerWorker($worker))->updateWorker('abc', 'Ana', 'ana@example.com');

        $this->assertSame(400, $response['status']);
    }

    public function testUpdateWorkerReturns400WhenFieldsAreInvalid(): void
    {
        // PUT substitui o registro inteiro: name e email são obrigatórios
        $worker = $this->createMock(Worker::class);
        $worker->expects($this->never())->method('update');

        $controller = new ControllerWorker($worker);

        $this->assertSame(400, $controller->updateWorker('3', null, 'ana@example.com')['status']);
        $this->assertSame(400, $controller->updateWorker('3', 'Ana', null)['status']);
        $this->assertSame(400, $controller->updateWorker('3', 'Ana', 'not-an-email')['status']);
        $this->assertSame(400, $controller->updateWorker('3', ['Ana'], 'ana@example.com')['status']);
    }

    public function testUpdateWorkerReturns404WhenWorkerDoesNotExist(): void
    {
        $worker = $this->createStub(Worker::class);
        $worker->method('update')->willThrowException(new \Exception('Worker with id 99 not found', 404));

        $response = (new ControllerWorker($worker))->updateWorker('99', 'Ana', 'ana@example.com');

        $this->assertSame(404, $response['status']);
    }

    public function testUpdateWorkerReturns409WhenEmailBelongsToAnotherWorker(): void
    {
        $duplicate = new class ('duplicate key') extends PDOException {
            protected $code = '23505';
        };

        $worker = $this->createStub(Worker::class);
        $worker->method('update')->willThrowException($duplicate);

        $response = (new ControllerWorker($worker))->updateWorker('3', 'Ana', 'taken@example.com');

        $this->assertSame(409, $response['status']);
    }
}
