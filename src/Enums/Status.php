<?php

namespace Xvlvv\Enums;

enum Status: string
{
    case NEW = 'new';
    case CANCELLED = 'cancelled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    /**
     * Возвращает перевод статуса на русском
     *
     * @return string
     */
    public function getName(): string
    {
        return match($this->value) {
            self::NEW->value => 'Новое',
            self::CANCELLED->value => 'Отменено',
            self::IN_PROGRESS->value => 'В работе',
            self::COMPLETED->value => 'Выполнено',
            self::FAILED->value => 'Провалено',
        };
    }
}