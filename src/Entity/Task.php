<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use DomainException;
use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;
use Xvlvv\Exception\InvalidActionForTaskException;
use Xvlvv\Services\TaskStateManager;
use Yii;

/**
 * Представляет сущность «Задание». Определяет списки действий и статусов, а также выполняет базовую работу с ними
 */
final class Task
{
    /**
     * Фабричный метод для создания объекта Task с автоматическим резолвом TaskStateManager
     *
     * @param int $customerId
     * @param string $name
     * @param string $description
     * @param string $createdAt
     * @param Category $category
     * @param string $endDate
     * @param int|null $workerId
     * @param int|null $id
     * @param int|null $budget
     * @param Status $currentStatus
     * @param City|null $city
     * @return self
     */
    public static function create(
        int $customerId,
        string $name,
        string $description,
        string $createdAt,
        Category $category,
        string $endDate,
        ?int $workerId = null,
        ?int $id = null,
        ?int $budget = null,
        Status $currentStatus = Status::NEW,
        ?City $city = null
    ): self {
        return new self(
            customerId: $customerId,
            name: $name,
            description: $description,
            createdAt: $createdAt,
            endDate: $endDate,
            category: $category,
            taskStateManager: Yii::$app->taskStateManager,
            id: $id,
            workerId: $workerId,
            budget: $budget,
            currentStatus: $currentStatus,
            city: $city
        );
    }

    /**
     * Инициализирует ID заказчика и исполнителя
     *
     * @param int $customerId
     * @param string $name
     * @param string $description
     * @param string $createdAt
     * @param string $endDate
     * @param Category $category
     * @param TaskStateManager $taskStateManager
     * @param int|null $id
     * @param int|null $workerId
     * @param int|null $budget
     * @param Status $currentStatus
     * @param City|null $city
     */
    private function __construct(
        private int $customerId,
        private string $name,
        private string $description,
        private string $createdAt,
        private string $endDate,
        private Category $category,
        private readonly TaskStateManager $taskStateManager,
        private ?int $id = null,
        private ?int $workerId = null,
        private ?int $budget = null,
        private Status $currentStatus = Status::NEW,
        private ?City $city = null,
    ) {
    }

    /**
     * @return int|null
     */
    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDate(): string
    {
        return $this->createdAt;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * Возвращает текущий статус задания
     *
     * @return Status
     */
    public function getCurrentStatus(): Status
    {
        return $this->currentStatus;
    }

    /**
     * Возвращает следующий статус задания в зависимости от указанного действия, null если действие не найдено либо
     * для текущего статус данное действие невозможно
     *
     * @param Action $action
     * @return Status|null
     */
    public function getNextStatus(Action $action): ?Status
    {
        return $this->taskStateManager->getNextStatus($this->getCurrentStatus(), $action);
    }

    /**
     * Применяет действие к заданию и изменяет его статус
     * @param Action $action
     * @return bool
     * @throws InvalidActionForTaskException
     */
    private function applyAction(Action $action): bool
    {
        $actions = $this->taskStateManager->getAllAvailableActions($this->getCurrentStatus());

        if (!in_array($action, $actions)) {
            throw new InvalidActionForTaskException($action, $this->getCurrentStatus());
        }

        $this->setStatus($this->getNextStatus($action));
        return true;
    }

    /**
     * Проваливает задание
     * @return bool
     */
    public function fail(): bool
    {
        return $this->applyAction(Action::FAIL);
    }

    /**
     * Завершает задание
     * @return bool
     */
    public function finish(): bool
    {
        return $this->applyAction(Action::COMPLETE);
    }

    /**
     * Отменяет задание
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->applyAction(Action::CANCEL);
    }

    /**
     * Назначает исполнителя и переводит задание в статус 'в работе'
     * @param int $workerId ID исполнителя
     * @return bool
     * @throws DomainException
     */
    public function start(int $workerId): bool
    {
        if (null !== $this->getWorkerId()) {
            throw new DomainException('Worker already set for this task');
        }
        $this->setWorkerId($workerId);
        $this->applyAction(Action::START);
        return true;
    }

    /**
     * Устанавливает новый статус
     * @param Status $status
     */
    private function setStatus(Status $status): void
    {
        $this->currentStatus = $status;
    }

    /**
     * Устанавливает ID исполнителя
     * @param int $workerId
     */
    private function setWorkerId(int $workerId): void
    {
        $this->workerId = $workerId;
    }

    /**
     * Список возможных действий для текущего статуса
     *
     * @param int $userId
     * @return array
     */
    public function getAvailableActions(int $userId): array
    {
        $actions = $this->taskStateManager->getPublicAvailableActions($this->getCurrentStatus());

        $filteredActionsByAccessCheck = array_filter(
            $actions,
            function (Action $actionEnum) use ($userId) {
                $actionObject = $actionEnum->getActionObject();
                return $actionObject->canMakeAction($userId, $this->customerId, $this->workerId, $this->getId());
            }
        );

        $availableActionObjects = array_map(
            function (Action $actionEnum) {
                return $actionEnum->getActionObject();
            },
            $filteredActionsByAccessCheck
        );

        return array_values($availableActionObjects);
    }

    /**
     * @return int|null
     */
    public function getWorkerId(): ?int
    {
        return $this->workerId;
    }
}