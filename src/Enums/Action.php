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
        return match($this->value) {
            self::APPLY->value => 'Откликнуться',
            self::REJECT->value => 'Отменить',
            self::COMPLETE->value => 'Выполнено',
        };
    }
}