<?php

namespace Xvlvv\Services;

use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;

final class TaskStateManager
{
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