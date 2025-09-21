<?php

declare(strict_types = 1);

namespace Xvlvv\Enums;

use Xvlvv\Domain\Task\ApplyAction;
use Xvlvv\Domain\Task\CancelAction;
use Xvlvv\Domain\Task\CompleteAction;
use Xvlvv\Domain\Task\FailAction;
use Xvlvv\Domain\Task\StartAction;
use \Xvlvv\Domain\Task\Action as TaskAction;

/**
 * Перечисление возможных действий над задачей
 */
enum Action: string
{
    case START = StartAction::class;
    case APPLY = ApplyAction::class;
    case CANCEL = CancelAction::class;
    case COMPLETE = CompleteAction::class;
    case FAIL = FailAction::class;

    /**
     * Возвращает название действия на русском языке
     *
     * @return string
     */
    public function getName(): string
    {
        return match($this->value) {
            self::START->value => 'Принять',
            self::APPLY->value => 'Откликнуться',
            self::CANCEL->value => 'Отменить',
            self::COMPLETE->value => 'Выполнено',
            self::FAIL->value => 'Отказаться',
        };
    }

    /**
     * Создает и возвращает объект-действие, соответствующий значению enum
     *
     * @return TaskAction
     */
    public function getActionObject(): TaskAction
    {
        $className = $this->value;
        return new $className($this->getName(), $this->getInternalName());
    }

    /**
     * Возвращает внутреннее системное имя действия
     *
     * @return string
     */
    private function getInternalName(): string
    {
        return match($this->value) {
            self::START->value => 'act_start',
            self::APPLY->value => 'act_apply',
            self::CANCEL->value => 'act_cancel',
            self::COMPLETE->value => 'act_complete',
            self::FAIL->value => 'act_fail',
        };
    }
}