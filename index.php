<?php

use Xvlvv\Domain\Task\CancelAction;
use Xvlvv\Domain\Task\CompleteAction;
use Xvlvv\Domain\Task\FailAction;
use Xvlvv\Domain\Task\ApplyAction;
use Xvlvv\Entity\Task;
use Xvlvv\Enums\Status;
use Xvlvv\Exception\InvalidActionForTaskException;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Services\TaskStateManager;

require_once __DIR__ . '/vendor/autoload.php';

$stateManager = new TaskStateManager();
$customerId = 1;
$workerId = 2;
$anotherUserId = 3;

echo 'Running tests... <br>';

// Тест 1: Проверяет, что для заказчика у новой задачи доступно только действие 'Отменить'.
$task = new Task($stateManager, $customerId);
$actionsForCustomer = $task->getAvailableActions($customerId);
assert(count($actionsForCustomer) === 1);
assert($actionsForCustomer[0] instanceof CancelAction);
echo 'Test 1 PASSED <br>';

// Тест 2: Проверяет, что для постороннего пользователя (исполнителя) у новой задачи доступно только действие 'Откликнуться'.
$task = new Task($stateManager, $customerId);
$actionsForOtherUser = $task->getAvailableActions($anotherUserId);
assert(count($actionsForOtherUser) === 1);
assert($actionsForOtherUser[0] instanceof ApplyAction);
echo 'Test 2 PASSED <br>';

// Тест 3: Проверяет переход задачи из статуса 'Новое' в 'В работе' при назначении исполнителя.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
assert($task->getCurrentStatus() === Status::IN_PROGRESS);
assert($task->getWorkerId() === $workerId);
echo 'Test 3 PASSED <br>';

// Тест 4: Проверяет, что система выбрасывает исключение при попытке выполнить недопустимое действие (завершить новую задачу).
$task = new Task($stateManager, $customerId);
try {
    $task->finish();
    assert(false, 'Test 4 FAILED: finish() on a NEW task should throw an exception.');
} catch (InvalidActionForTaskException $e) {
    assert(true);
}
echo 'Test 4 PASSED <br>';

// Тест 5: Проверяет, что для исполнителя у задачи в статусе 'В работе' доступно только действие 'Отказаться'.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$actionsForWorker = $task->getAvailableActions($workerId);
assert(count($actionsForWorker) === 1);
assert($actionsForWorker[0] instanceof FailAction);
echo 'Test 5 PASSED <br>';

// Тест 6: Проверяет, что для заказчика у задачи в статусе 'В работе' доступно только действие 'Выполнено'.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$actionsForCustomer = $task->getAvailableActions($customerId);
assert(count($actionsForCustomer) === 1);
assert($actionsForCustomer[0] instanceof CompleteAction);
echo 'Test 6 PASSED <br>';

// Тест 7: Проверяет переход задачи в статус 'Провалено' после отказа исполнителя.
$task = new Task($stateManager, $customerId);
$task->start($workerId);
$task->fail();
assert($task->getCurrentStatus() === Status::FAILED);
echo 'Test 7 PASSED <br>';

// Тест 8: Проверяет переход новой задачи в статус 'Отменено' после отмены заказчиком.
$task = new Task($stateManager, $customerId);
$task->cancel();
assert($task->getCurrentStatus() === Status::CANCELLED);
echo 'Test 8 PASSED <br>';

echo 'All tests completed successfully! <br>';

echo 'Running exception tests...';

// Тест 1: Проверка InvalidActionForTaskException для недопустимых действий
echo 'Test 1: Testing InvalidActionForTaskException for invalid actions... <br>';
$task = new Task($stateManager, $customerId);

// Попытка завершить новую задачу
try {
    $task->finish();
    assert(false, 'Should throw InvalidActionForTaskException for finish() on NEW task');
} catch (InvalidActionForTaskException $e) {
    assert(str_contains($e->getMessage(), 'Invalid action for task'), 'Exception message should contain expected text');
    echo '✓ InvalidActionForTaskException correctly thrown for finish() on NEW task <br>';
}

// Попытка отказаться от новой задачи
try {
    $task->fail();
    assert(false, 'Should throw InvalidActionForTaskException for fail() on NEW task');
} catch (InvalidActionForTaskException $e) {
    assert(str_contains($e->getMessage(), 'Invalid action for task'), 'Exception message should contain expected text');
    echo '✓ InvalidActionForTaskException correctly thrown for fail() on NEW task <br>';
}

// Тест 2: Проверка DomainException при повторном назначении исполнителя
echo 'Test 2: Testing DomainException for duplicate worker assignment... <br>';
$task = new Task($stateManager, $customerId);
$task->start($workerId);

try {
    $task->start($anotherUserId);
    assert(false, 'Should throw DomainException for duplicate worker assignment');
} catch (DomainException $e) {
    assert(str_contains($e->getMessage(), 'Worker already set'), 'Exception message should mention worker already set');
    echo '✓ DomainException correctly thrown for duplicate worker assignment <br>';
}

// Тест 3: Проверка исключений для недопустимых переходов статусов
echo 'Test 3: Testing invalid status transitions...';
$task = new Task($stateManager, $customerId);
$task->cancel(); // Переводим в CANCELLED

try {
    $task->start($workerId);
    assert(false, 'Should throw exception for start() on CANCELLED task');
} catch (InvalidActionForTaskException $e) {
    echo '✓ InvalidActionForTaskException correctly thrown for start() on CANCELLED task <br>';
}

// Тест 4: Проверка PermissionDeniedException через canMakeAction
echo 'Test 4: Testing PermissionDeniedException logic...';
$cancelAction = new CancelAction('Отменить', 'act_cancel');

// Заказчик может отменить свою задачу
assert($cancelAction->canMakeAction($customerId, $customerId) === true, 'Customer should be able to cancel their task');

// Исполнитель не может отменить чужую задачу
assert($cancelAction->canMakeAction($workerId, $customerId) === false, 'Worker should not be able to cancel customer task');

$applyAction = new ApplyAction('Откликнуться', 'act_apply');
// Заказчик не может откликнуться на свою задачу
assert($applyAction->canMakeAction($customerId, $customerId) === false, 'Customer should not be able to apply to their own task');

echo '✓ Permission logic working correctly <br>';