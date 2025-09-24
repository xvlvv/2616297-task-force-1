<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\DTO\ViewNewTasksDTO;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Entity\Task;

/**
 * Интерфейс репозитория для задания
 */
interface TaskRepositoryInterface
{
    /**
     * Сохраняет новую задание
     *
     * @param SaveTaskDTO $taskDTO
     * @return int|null
     */
    public function save(SaveTaskDTO $taskDTO): ?int;

    /**
     * Обновляет существующую задачу
     *
     * @param Task $task
     * @return void
     */
    public function update(Task $task): void;

    /**
     * Проверяет, откликался ли уже пользователь на задание
     *
     * @param int $taskId
     * @param int $userId
     * @return bool
     */
    public function hasAlreadyResponded(int $taskId, int $userId): bool;

    /**
     * Проверяет, является ли пользователь автором задания
     *
     * @param int $taskId
     * @param int $userId
     * @return bool
     */
    public function isAuthor(int $taskId, int $userId): bool;

    /**
     * Проверяет, является ли пользователь исполнителем задания
     *
     * @param int $taskId
     * @param int $userId
     * @return bool
     */
    public function isWorker(int $taskId, int $userId): bool;

    /**
     * Возвращает ID исполнителя задания или выбрасывает исключение
     *
     * @param int $taskId
     * @return int
     */
    public function getWorkerByIdOrFail(int $taskId): int;

    /**
     * Находит задание по ID или выбрасывает исключение
     *
     * @param int $taskId
     * @return Task
     */
    public function getByIdOrFail(int $taskId): Task;

    /**
     * Получает новые задания с фильтрацией и пагинацией
     *
     * @param GetNewTasksDTO $dto
     * @return ViewNewTasksDTO
     */
    public function getNewTasks(GetNewTasksDTO $dto): ViewNewTasksDTO;

    /**
     * Получает количество отфильтрованных заданий
     *
     * @param GetNewTasksDTO $dto
     * @return int
     */
    public function getFilteredTasksQueryCount(GetNewTasksDTO $dto): int;

    /**
     * Получает данные для страницы просмотра задания
     *
     * @param int $id
     * @return ViewTaskDTO
     */
    public function getTaskForView(int $id, int $userId): ViewTaskDTO;

    /**
     * Проверяет, есть ли у исполнителя активное задание
     *
     * @param int $id
     * @return bool
     */
    public function workerHasActiveTask(int $id): bool;

    /**
     * Получает количество завершенных заданий исполнителя
     *
     * @param int $workerId
     * @return int
     */
    public function getCompletedTasksCountByWorkerId(int $workerId): int;
}