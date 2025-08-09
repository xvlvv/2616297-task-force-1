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
        return match($this) {
            self::NEW => 'Новое',
            self::CANCELLED => 'Отменено',
            self::IN_PROGRESS => 'В работе',
            self::COMPLETED => 'Выполнено',
            self::FAILED => 'Провалено',
        };
    }
}