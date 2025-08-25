<?php

use Xvlvv\Domain\Task\CancelAction;
use Xvlvv\Domain\Task\CompleteAction;
use Xvlvv\Domain\Task\FailAction;
use Xvlvv\Domain\Task\ApplyAction;
use Xvlvv\Entity\Task;
use Xvlvv\Enums\Status;
use Xvlvv\Exception\InvalidActionForTaskException;
use Xvlvv\Services\TaskStateManager;

require_once __DIR__ . '/vendor/autoload.php';

$stateManager = new TaskStateManager();
$customerId = 1;
$workerId = 2;
$anotherUserId = 3;

echo "Running tests...\n";

// Тест 1: Проверяет, что для заказчика у новой задачи доступно только действие "Отменить".
$task = new Task($stateManager, $customerId);
$actionsForCustomer = $task->getAvailableActions($customerId);
assert(count($actionsForCustomer) === 1);
assert($actionsForCustomer[0] instanceof CancelAction);
echo "Test 1 PASSED\n";

// Тест 2: Проверяет, что для постороннего пользователя (исполнителя) у новой задачи доступно только действие "Откликнуться".
$task = new Task($stateManager, $customerId);
$actionsForOtherUser = $task->getAvailableActions($anotherUserId);
assert(count($actionsForOtherUser) === 1);
assert($actionsForOtherUser[0] instanceof ApplyAction);
echo "Test 2 PASSED\n";

// Тест 3: Проверяет переход задачи из статуса "Новое" в "В работе" при назначении исполнителя.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
assert($task->getCurrentStatus() === Status::IN_PROGRESS);
assert($task->getWorkerId() === $workerId);
echo "Test 3 PASSED\n";

// Тест 4: Проверяет, что система выбрасывает исключение при попытке выполнить недопустимое действие (завершить новую задачу).
$task = new Task($stateManager, $customerId);
try {
    $task->finish();
    assert(false, 'Test 4 FAILED: finish() on a NEW task should throw an exception.');
} catch (InvalidActionForTaskException $e) {
    assert(true);
}
echo "Test 4 PASSED\n";

// Тест 5: Проверяет, что для исполнителя у задачи в статусе "В работе" доступно только действие "Отказаться".
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$actionsForWorker = $task->getAvailableActions($workerId);
assert(count($actionsForWorker) === 1);
assert($actionsForWorker[0] instanceof FailAction);
echo "Test 5 PASSED\n";

// Тест 6: Проверяет, что для заказчика у задачи в статусе "В работе" доступно только действие "Выполнено".
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$actionsForCustomer = $task->getAvailableActions($customerId);
assert(count($actionsForCustomer) === 1);
assert($actionsForCustomer[0] instanceof CompleteAction);
echo "Test 6 PASSED\n";

// Тест 7: Проверяет переход задачи в статус "Провалено" после отказа исполнителя.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$task->fail();
assert($task->getCurrentStatus() === Status::FAILED);
echo "Test 7 PASSED\n";

// Тест 8: Проверяет переход новой задачи в статус "Отменено" после отмены заказчиком.
$task = new Task($stateManager, $customerId);
$task->cancel();
assert($task->getCurrentStatus() === Status::CANCELLED);
echo "Test 8 PASSED\n";

echo "\nAll tests completed successfully!\n";