/*
 По заданию также нужно сделать схему классов приложения, у меня получилось - сущности с бизнес-логикой: Task и User,
 сущности без логики - City, TaskResponse, Review, TaskFile, Category, и если понадобятся сервисы то это отклик
 на задание, аутентификация через ВК. Не стал заранее прямо диаграмму делать и описывать свойства и методы потому что
 думаю что в процессе обязательно поменяется, ну и в целом достаточно сложно. Что-то нужно добавить или убрать?
 */

DROP DATABASE IF EXISTS task_force;

CREATE DATABASE task_force
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE task_force;

CREATE TABLE city
(
    id         INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(128)   NOT NULL,
    latitude   DECIMAL(10, 8) NOT NULL,
    longitude  DECIMAL(11, 8) NOT NULL COMMENT 'Вместе с latitude указывают на точку центра города',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE category
(
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(128) NOT NULL UNIQUE,
    icon       VARCHAR(128),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user
(
    id                             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name                           VARCHAR(255) NOT NULL,
    email                          VARCHAR(255) NOT NULL UNIQUE,
    password_hash                  VARCHAR(255) NULL COMMENT 'Может быть NULL для пользователей, авторизованных через ВК',
    role                           VARCHAR(255) NOT NULL,
    city_id                        INT UNSIGNED NOT NULL,
    avatar_path                    VARCHAR(255),
    day_of_birth                   DATE,
    bio                            TEXT,
    phone_number                   CHAR(11),
    telegram_username              VARCHAR(64),
    failed_tasks_count             INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество проваленных заданий работника',
    show_contacts_only_to_customer BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at                     TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    updated_at                     TIMESTAMP             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES city (id)
);

CREATE TABLE user_specialization
(
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_category (user_id, category_id) COMMENT 'Исключает что для одного пользователя будет отмечено 2 одинаковых категории'
);

CREATE TABLE file
(
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    original_name VARCHAR(255) NOT NULL,
    path          VARCHAR(255) NOT NULL UNIQUE,
    mime_type     VARCHAR(128) NOT NULL,
    size_bytes    INT UNSIGNED NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE task
(
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT         NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    worker_id   INT UNSIGNED,
    city_id     INT UNSIGNED,
    status      VARCHAR(32)  NOT NULL DEFAULT 'new',
    budget      INT UNSIGNED,
    latitude    DECIMAL(10, 8),
    longitude   DECIMAL(11, 8) COMMENT 'Вместе с latitude указывают на точные координаты места выполнения задания',
    end_date    TIMESTAMP,
    created_at  TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category (id),
    FOREIGN KEY (customer_id) REFERENCES user (id),
    FOREIGN KEY (worker_id) REFERENCES user (id),
    FOREIGN KEY (city_id) REFERENCES city (id)
);

CREATE TABLE task_file
(
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    task_id    INT UNSIGNED NOT NULL,
    file_id    INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE,
    UNIQUE KEY uk_task_file (task_id, file_id) COMMENT 'Исключает что для одного задания будет 2 одинаковых файла'
);

CREATE TABLE task_response
(
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    task_id     INT UNSIGNED NOT NULL,
    worker_id   INT UNSIGNED NOT NULL,
    comment     TEXT,
    price       INT UNSIGNED,
    is_rejected BOOLEAN      NOT NULL DEFAULT FALSE COMMENT 'Флаг, указывает что отклик отклонён',
    created_at  TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES user (id) ON DELETE CASCADE
);

CREATE TABLE review
(
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    task_id     INT UNSIGNED     NOT NULL UNIQUE,
    customer_id INT UNSIGNED     NOT NULL,
    worker_id   INT UNSIGNED     NOT NULL,
    comment     TEXT             NOT NULL COMMENT 'По ТЗ является обязательным полем при завершении задания',
    rating      TINYINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES task (id),
    FOREIGN KEY (customer_id) REFERENCES user (id),
    FOREIGN KEY (worker_id) REFERENCES user (id),
    CONSTRAINT chk_rating_range CHECK (rating BETWEEN 1 AND 5) -- Разрешен рейтинг от 1 до 5
);

-- Индекс для главного списка заданий
CREATE INDEX idx_task_status_created_at ON task (status, created_at);

-- Индекс для заказчика, чтобы быстро находить свои задания по статусу
CREATE INDEX idx_task_customer_id_status ON task (customer_id, status);

-- Индекс для работника, чтобы быстро находить свои задания по статусу
CREATE INDEX idx_task_worker_id_status ON task (worker_id, status);

-- Полнотекстовый индекс для поиска по названию и описанию
CREATE FULLTEXT INDEX ft_task_name_description ON task (name, description);

-- Ускоряет проверку, откликался ли уже этот работник на это задание
CREATE INDEX idx_task_response_worker_id_task_id ON task_response (worker_id, task_id);