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
                Action::APPLY->value => Status::IN_PROGRESS,
                Action::REJECT->value => Status::CANCELLED,
            ],
            Status::IN_PROGRESS->value => [
                Action::COMPLETE->value => Status::COMPLETED,
                Action::REJECT->value => Status::CANCELLED,
            ],
        ];

        return $transitionMap[$status->value][$action->value] ?? null;
    }

    public function getAvailableActions(Status $status): ?array
    {
        $availableActions = [
            Status::NEW->value => [
                Action::APPLY,
                Action::REJECT,
            ],
            Status::CANCELLED->value => [],
            Status::IN_PROGRESS->value => [
                Action::COMPLETE,
                Action::REJECT,
            ],
            Status::COMPLETED->value => [],
            Status::FAILED->value => [],
        ];

        return $availableActions[$status->value] ?? null;
    }
}