<?php

use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;
use Xvlvv\Task;
use Xvlvv\TaskStateManager;

require_once __DIR__ . '/vendor/autoload.php';

$taskStateManager = new TaskStateManager();
$customerId = 1;
$workerId = 2;

// Проверка начального статуса новой задачи
$task = new Task($customerId, $workerId, $taskStateManager);
assert(Status::NEW === $task->getCurrentStatus(), 'New task should have status NEW');

// Проверка доступных действий для статуса NEW
$availableActions = $task->getAvailableActions();
assert(true === in_array(Action::APPLY->value, $availableActions), 'APPLY action should be available for NEW status');
assert(true === in_array(Action::REJECT->value, $availableActions), 'REJECT action should be available for NEW status');

// Проверка применения действия APPLY (NEW → IN_PROGRESS)
$result = $task->applyAction(Action::APPLY);
assert(true === $result, 'APPLY action should succeed');
assert(Status::IN_PROGRESS === $task->getCurrentStatus(), 'Status should change to IN_PROGRESS after APPLY');

// Проверка недопустимого действия COMPLETE для NEW статуса
$task = new Task($customerId, $workerId, $taskStateManager);
$result = $task->applyAction(Action::COMPLETE);
assert(false === $result, 'COMPLETE action should fail for NEW status');
assert(Status::NEW === $task->getCurrentStatus(), 'Status should remain NEW after invalid action');

// Проверка перехода IN_PROGRESS → COMPLETED
$task = new Task($customerId, $workerId, $taskStateManager);
$task->applyAction(Action::APPLY);
$result = $task->applyAction(Action::COMPLETE);
assert(true === $result, 'COMPLETE action should succeed for IN_PROGRESS status');
assert(Status::COMPLETED === $task->getCurrentStatus(), 'Status should change to COMPLETED after COMPLETE');

// Проверка перехода IN_PROGRESS → CANCELLED
$task = new Task($customerId, $workerId, $taskStateManager);
$task->applyAction(Action::APPLY);
$result = $task->applyAction(Action::REJECT);
assert(true === $result, 'REJECT action should succeed for IN_PROGRESS status');
assert(Status::CANCELLED === $task->getCurrentStatus(), 'Status should change to CANCELLED after REJECT');

// Проверка отсутствия действий для завершенного статуса COMPLETED
$task = new Task($customerId, $workerId, $taskStateManager);
$task->applyAction(Action::APPLY);
$task->applyAction(Action::COMPLETE);
$availableActions = $task->getAvailableActions();
assert(true === empty($availableActions), 'No actions should be available for COMPLETED status');
$result = $task->applyAction(Action::APPLY);
assert(false === $result, 'No actions should succeed for COMPLETED status');

// Проверка русских названий статусов
assert('Новое' === Status::NEW->getName(), 'Incorrect Russian translation for NEW status');
assert('Отменено' === Status::CANCELLED->getName(), 'Incorrect Russian translation for CANCELLED status');

// Проверка русских названий действий
assert('Откликнуться' === Action::APPLY->getName(), 'Incorrect Russian translation for APPLY action');
assert('Отменить' === Action::REJECT->getName(), 'Incorrect Russian translation for REJECT action');

echo 'test result OK';