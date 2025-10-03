<?php

declare(strict_types=1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\TaskListItemDTO;
use Xvlvv\Entity\Task;
use Xvlvv\Entity\Task as TaskEntity;
use Xvlvv\Enums\Status;
use Xvlvv\Repository\TaskRepositoryInterface;

/**
 * Сервис для получения списка заданий пользователя на странице Мои задания
 */
readonly final class MyTasksService
{
    /**
     * @param TaskRepositoryInterface $taskRepository Репозиторий для получения данных о заданиях
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Находит и возвращает список задач для пользователя в виде DTO
     *
     * @param int $userId ID пользователя, для которого ищутся задания
     * @param string $activeTab Идентификатор вкладки ('new', 'in-progress', или 'closed')
     * @return TaskListItemDTO[] Массив DTO для отображения в списке
     */
    public function findTasksForUser(int $userId, string $activeTab): array
    {
        $statuses = $this->getStatusesByTab($activeTab);

        $domainTasks = $this->taskRepository->findForUserByStatuses($userId, $statuses);

        return array_map(
            function (Task $task) {
                return $this->mapEntityToDTO($task);
            },
            $domainTasks
        );
    }

    /**
     * Возвращает массив статусов БД на основе идентификатора вкладки
     *
     * @param string $tab Идентификатор вкладки
     * @return Status[] Массив enum'ов статусов
     */
    private function getStatusesByTab(string $tab): array
    {
        $statusMap = [
            'new' => [Status::NEW],
            'in-progress' => [Status::IN_PROGRESS],
            'closed' => [Status::COMPLETED, Status::FAILED, Status::CANCELLED],
        ];

        return $statusMap[$tab] ?? $statusMap['new'];
    }

    /**
     * Преобразует одну доменную сущность Task в TaskListItemDTO
     *
     * @param TaskEntity $task Доменная сущность задания
     * @return TaskListItemDTO DTO для отображения в списке
     */
    private function mapEntityToDTO(TaskEntity $task): TaskListItemDTO
    {
        return new TaskListItemDTO(
            id: $task->getId(),
            name: $task->getName(),
            budget: $task->getBudget(),
            createdAt: $task->getCreatedDate(),
            description: $task->getDescription(),
            city: $task->getCityName(),
            category: $task->getCategory()->getName()
        );
    }
}