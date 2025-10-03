<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\DTO\ViewNewTasksDTO;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Entity\Task;
use yii\web\NotFoundHttpException;

/**
 * Интерфейс репозитория для работы с сущностями Задание.
 * Определяет контракт для получения, сохранения и обновления данных о заданиях.
 */
interface TaskRepositoryInterface
{
    /**
     * Сохраняет новое задание в хранилище данных.
     *
     * @param SaveTaskDTO $taskDTO DTO с данными для создания задания.
     * @return int|null ID созданного задания или null в случае ошибки.
     */
    public function save(SaveTaskDTO $taskDTO): ?int;

    /**
     * Обновляет данные существующего задания в хранилище.
     *
     * @param Task $task Доменная сущность задания с измененными данными.
     * @return void
     */
    public function update(Task $task): void;

    /**
     * Проверяет, откликался ли уже указанный пользователь на данное задание.
     *
     * @param int $taskId ID задания.
     * @param int $userId ID пользователя.
     * @return bool true, если отклик уже существует, иначе false.
     */
    public function hasAlreadyResponded(int $taskId, int $userId): bool;

    /**
     * Проверяет, является ли указанный пользователь автором (заказчиком) задания.
     *
     * @param int $taskId ID задания.
     * @param int $userId ID пользователя для проверки.
     * @return bool true, если пользователь является автором, иначе false.
     */
    public function isAuthor(int $taskId, int $userId): bool;

    /**
     * Проверяет, является ли указанный пользователь назначенным исполнителем задания.
     *
     * @param int $taskId ID задания.
     * @param int $userId ID пользователя для проверки.
     * @return bool true, если пользователь является исполнителем, иначе false.
     */
    public function isWorker(int $taskId, int $userId): bool;

    /**
     * Возвращает ID исполнителя, назначенного на задание.
     *
     * @param int $taskId ID задания.
     * @return int ID исполнителя.
     * @throws NotFoundHttpException если у задания нет назначенного исполнителя.
     */
    public function getWorkerByIdOrFail(int $taskId): int;

    /**
     * Находит задание по ID и возвращает его в виде доменной сущности.
     *
     * @param int $taskId ID задания
     * @return Task Доменная сущность задания
     * @throws NotFoundHttpException если задание с указанным ID не найдено
     */
    public function getByIdOrFail(int $taskId): Task;

    /**
     * Получает DTO с новыми заданиями для отображения, с учетом фильтрации и пагинации
     *
     * @param GetNewTasksDTO $dto DTO с параметрами фильтрации
     * @return ViewNewTasksDTO DTO с результатами для отображения
     */
    public function getNewTasks(GetNewTasksDTO $dto): ViewNewTasksDTO;

    /**
     * Получает общее количество отфильтрованных новых заданий (для пагинации)
     *
     * @param GetNewTasksDTO $dto DTO с параметрами фильтрации
     * @return int Количество заданий
     */
    public function getFilteredTasksQueryCount(GetNewTasksDTO $dto): int;

    /**
     * Получает DTO с полной информацией о задании для страницы детального просмотра
     *
     * @param int $id ID задания
     * @param int $userId ID текущего пользователя (для определения доступных действий)
     * @return ViewTaskDTO DTO с данными для отображения
     */
    public function getTaskForView(int $id, int $userId): ViewTaskDTO;

    /**
     * Проверяет, есть ли у исполнителя активное задание (в статусе "в работе")
     *
     * @param int $id ID исполнителя
     * @return bool true, если у исполнителя нет активных заданий, иначе false
     */
    public function workerHasActiveTask(int $id): bool;

    /**
     * Получает количество завершенных заданий для указанного исполнителя
     *
     * @param int $workerId ID исполнителя
     * @return int Количество завершенных заданий
     */
    public function getCompletedTasksCountByWorkerId(int $workerId): int;

    /**
     * Получает список заданий для конкретного пользователя, отфильтрованный по статусу
     * Включает задания, где пользователь является заказчиком или откликнувшимся исполнителем
     *
     * @param int $userId ID пользователя
     * @param string[] $statuses Массив статусов для фильтрации
     * @return Task[] Массив доменных сущностей Task
     */
    public function findForUserByStatuses(int $userId, array $statuses): array;
}