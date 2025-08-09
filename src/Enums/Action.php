<?php

namespace Xvlvv\Enums;

/**
 *
 */
enum Action: string
{
    case APPLY = 'apply';
    case REJECT = 'reject';
    case COMPLETE = 'complete';

    /**
     * Возвращает перевод действия на русском
     *
     * @return string
     */
    public function getName(): string
    {
        return match($this) {
            self::APPLY => 'Откликнуться',
            self::REJECT => 'Отменить',
            self::COMPLETE => 'Выполнено',
        };
    }
}