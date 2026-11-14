<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\ControllerBoss;
use App\Model\Boss;
use Exception;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ControllerBossTest extends TestCase
{
    public function testUpdateBossReturns200WithTheUpdatedBoss(): void
    {
        $row = ['id' => 2, 'name' => 'Fernando', 'email' => 'fernando@example.com'];

        $boss = $this->createMock(Boss::class);
        $boss->expects($this->once())
            ->method('update')
            ->with(2, ['name' => 'Fernando', 'email' => 'fernando@example.com']);
        $boss->method('getById')->with(2)->willReturn($row);

        $response = (new ControllerBoss($boss))->updateBoss('2', ' Fernando ', 'Fernando@Example.com');

        $this->assertSame(200, $response['status']);
        $this->assertSame($row, $response['body']);
    }

    public function testUpdateBossReturns400WhenIdIsNotInteger(): void
    {
        $boss = $this->createMock(Boss::class);
        $boss->expects($this->never())->method('update');

        $response = (new ControllerBoss($boss))->updateBoss('abc', 'Fernando', 'fernando@example.com');

        $this->assertSame(400, $response['status']);
    }

    public function testUpdateBossReturns400WhenFieldsAreInvalid(): void
    {
        $boss = $this->createMock(Boss::class);
        $boss->expects($this->never())->method('update');

        $controller = new ControllerBoss($boss);

        $this->assertSame(400, $controller->updateBoss('2', '', 'fernando@example.com')['status']);
        $this->assertSame(400, $controller->updateBoss('2', 'Fernando', 'invalid')['status']);
    }

    public function testUpdateBossReturns404WhenBossDoesNotExist(): void
    {
        $boss = $this->createStub(Boss::class);
        $boss->method('update')->willThrowException(new Exception('Boss with id 99 not found', 404));

        $response = (new ControllerBoss($boss))->updateBoss('99', 'Fernando', 'fernando@example.com');

        $this->assertSame(404, $response['status']);
    }

    public function testUpdateBossReturns409WhenEmailBelongsToAnotherBoss(): void
    {
        $duplicate = new class ('duplicate key') extends PDOException {
            protected $code = '23505';
        };

        $boss = $this->createStub(Boss::class);
        $boss->method('update')->willThrowException($duplicate);

        $response = (new ControllerBoss($boss))->updateBoss('2', 'Fernando', 'taken@example.com');

        $this->assertSame(409, $response['status']);
    }
}
