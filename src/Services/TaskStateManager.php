<?php

declare(strict_types=1);

namespace Xvlvv\Services;

use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;

/**
 * Управляет логикой переходов состояний (статусов) для задачи
 */
final class TaskStateManager
{
    /**
     * Определяет следующий статус задачи на основе текущего статуса и действия
     *
     * @param Status $status Текущий статус
     * @param Action $action Выполняемое действие
     * @return Status|null Новый статус или null, если переход невозможен
     */
    public function getNextStatus(Status $status, Action $action): ?Status
    {
        $transitionMap = [
            Status::NEW->value => [
                Action::START->value => Status::IN_PROGRESS,
                Action::CANCEL->value => Status::CANCELLED,
            ],
            Status::IN_PROGRESS->value => [
                Action::COMPLETE->value => Status::COMPLETED,
                Action::FAIL->value => Status::FAILED,
            ],
        ];

        return $transitionMap[$status->value][$action->value] ?? null;
    }

    /**
     * Возвращает список публичных действий, доступных в указанном статусе
     *
     * @param Status $status Текущий статус
     * @return array Массив Enum'ов Action
     */
    public function getPublicAvailableActions(Status $status): array
    {
        $availableActions = [
            Status::NEW->value => [
                Action::APPLY,
                Action::CANCEL,
            ],
            Status::CANCELLED->value => [],
            Status::IN_PROGRESS->value => [
                Action::COMPLETE,
                Action::FAIL,
            ],
            Status::COMPLETED->value => [],
            Status::FAILED->value => [],
        ];

        return $availableActions[$status->value];
    }

    /**
     * Возвращает полный список всех действий, возможных в указанном статусе
     *
     * @param Status $status Текущий статус
     * @return array Массив Enum'ов Action
     */
    public function getAllAvailableActions(Status $status): array
    {
        $availableActions = [
            Status::NEW->value => [
                Action::START,
                Action::APPLY,
                Action::CANCEL,
            ],
            Status::CANCELLED->value => [],
            Status::IN_PROGRESS->value => [
                Action::COMPLETE,
                Action::FAIL,
            ],
            Status::COMPLETED->value => [],
            Status::FAILED->value => [],
        ];

        return $availableActions[$status->value];
    }
}