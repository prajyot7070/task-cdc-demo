<?php

declare(strict_types=1);

namespace App\Tests;

use App\Task;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testValidTitleReturnsTrue(): void
    {
        $this->assertTrue(Task::validate('Testing'));
        $this->assertTrue(Task::validate('Testing is the testing'));
    }

    public function testInvalidTitleReturnsFalse(): void
    {
        $this->assertFalse(Task::validate('ab'));           // too short
        $this->assertFalse(Task::validate('Buy milk!'));    // special character !
        $this->assertFalse(Task::validate('a'));            // 1 character
    }

    public function testConstructorSetsTitleAndStatus(): void
    {
        $task = new Task([
            'title' => 'Finish CDC project',
            'status' => 'in-progress'
        ]);

        $this->assertSame('Finish CDC project', $task->title);
        $this->assertSame('in-progress', $task->status);
    }

    public function testConstructorUsesDefaultStatus(): void
    {
        $task = new Task(['title' => 'No status given']);
        $this->assertSame('open', $task->status);
    }
}