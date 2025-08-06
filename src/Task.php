<?php

namespace xvlvv;

/**
 * Представляет сущность «Задание». Определяет списки действий и статусов, а также выполняет базовую работу с ними
 */
class Task
{
    /**
     * Статус задания «Новое»
     */
    const STATUS_NEW = 'new';
    /**
     * Статус задания «Отменено»
     */
    const STATUS_CANCELLED = 'cancelled';
    /**
     * Статус задания «В работе»
     */
    const STATUS_IN_PROGRESS = 'in_progress';
    /**
     * Статус задания «Выполнено»
     */
    const STATUS_COMPLETED = 'completed';
    /**
     * Статус задания «Провалено»
     */
    const STATUS_FAILED = 'failed';
    /**
     * Действие над заданием «Откликнуться»
     */
    const ACTION_APPLY = 'apply';
    /**
     * Действие над заданием «Отказаться»
     */
    const ACTION_REJECT = 'reject';
    /**
     * Действие над заданием «Выполнено»
     */
    const ACTION_COMPLETE = 'complete';
    /**
     * Текущий статус задания
     *
     * @var string
     */
    private string $currentStatus = self::STATUS_NEW;
    /**
     * Карта перевода статуса/действия, ассоциативный массив, где ключ — внутреннее имя,
     * а значение — название на русском
     *
     * @var array|string[]
     */
    private array $translationMap = [
        self::STATUS_NEW => 'Новое',
        self::STATUS_CANCELLED => 'Отменено',
        self::STATUS_IN_PROGRESS => 'В работе',
        self::STATUS_COMPLETED => 'Выполнено',
        self::STATUS_FAILED => 'Провалено',
        self::ACTION_APPLY => 'Откликнуться',
        self::ACTION_REJECT => 'Отменить',
        self::ACTION_COMPLETE => 'Выполнено',
    ];

    /**
     * Карта перехода на следующий статус, в зависимости от определённого действия
     *
     * @var array|string[][]
     */
    private array $transitionMap = [
        self::STATUS_NEW => [
            self::ACTION_APPLY => self::STATUS_IN_PROGRESS,
            self::ACTION_REJECT => self::STATUS_CANCELLED,
        ],
        self::STATUS_IN_PROGRESS => [
            self::ACTION_COMPLETE => self::STATUS_COMPLETED,
            self::ACTION_REJECT => self::STATUS_CANCELLED,
        ],
    ];

    /**
     * Карта возможных действий над заданием в текущем статусе
     *
     * @var array
     */
    private array $availableActionsMap = [
        self::STATUS_NEW => [
            self::ACTION_APPLY,
            self::ACTION_REJECT,
        ],
        self::STATUS_CANCELLED => [],
        self::STATUS_IN_PROGRESS => [
            self::ACTION_COMPLETE,
            self::ACTION_REJECT,
        ],
        self::STATUS_COMPLETED => [],
        self::STATUS_FAILED => [],
    ];

    /**
     * Инициализирует ID заказчика и исполнителя
     *
     * @param int $customerId
     * @param int $workerId
     */
    public function __construct(
        private int $customerId,
        private int $workerId,
    ) {
    }

    /**
     * Возвращает текущий статус задания
     *
     * @return string
     */
    public function getCurrentStatus(): string
    {
        return $this->currentStatus;
    }

    /**
     * Устанавливает текущий статус задания
     *
     * @param string $status
     * @return void
     */
    public function setCurrentStatus(string $status): void
    {
        $this->currentStatus = $status;
    }

    /**
     * Возвращает следующий статус задания в зависимости от указанного действия, null если действие не найдено либо
     * для текущего статус данное действие невозможно
     *
     * @param string $action
     * @return string|null
     */
    public function getNextStatus(string $action): ?string
    {
        return $this->transitionMap[$this->getCurrentStatus()][$action] ?? null;
    }

    /**
     * Список возможных действий для текущего статуса
     *
     * @return array
     */
    public function getAvailableActions(): array
    {
        return $this->availableActionsMap[$this->getCurrentStatus()];
    }

    /**
     * Возвращает карту перевода
     *
     * @return array|string[]
     */
    public function getTranslationMap(): array
    {
        return $this->translationMap;
    }
}