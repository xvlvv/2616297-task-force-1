<?php

use app\models\Task;
use Xvlvv\Enums\Status;

$data = [
    [
        'comment' => 'Aut qui voluptas omnis modi quas vel numquam. Et qui assumenda ea porro rerum error blanditiis ducimus. Omnis veritatis placeat qui adipisci veritatis.',
        'rating' => 2,
    ],
    [
        'comment' => 'Illum occaecati mollitia dolores. Blanditiis voluptatibus facere omnis sapiente maiores aut et nihil. Ut cumque et delectus in et et. Minima quae quis fugiat aut.',
        'rating' => 4,
    ],
    [
        'comment' => 'Ipsam dolor dolor in accusantium ab vero et. Nobis perferendis et dolores molestias. Amet odit qui nihil fugit ut aut. Qui omnis ipsa inventore ab eum labore incidunt.',
        'rating' => 1,
    ],
    [
        'comment' => 'Placeat ut esse sit laborum. Et nam accusamus esse dolorem aperiam repudiandae at. Dolor explicabo voluptas aut esse et reprehenderit eaque.',
        'rating' => 2,
    ],
    [
        'comment' => 'Quasi quod occaecati soluta sit. Beatae aut quia dolorem sit veniam. Illo veniam debitis debitis dolorem odio error voluptatem quis.',
        'rating' => 1,
    ],
    [
        'comment' => 'Rerum amet sed ipsam. A quas ad quis velit eos omnis corrupti. Non commodi necessitatibus omnis est et est odio.',
        'rating' => 1,
    ],
    [
        'comment' => 'Sint non optio totam natus est. Odio ut ut laborum nobis. Quo temporibus facere ipsa est.',
        'rating' => 1,
    ],
    [
        'comment' => 'Laborum aut veniam aut id id autem. In aspernatur rerum atque est deleniti voluptatibus eum voluptas. Facere consectetur numquam deserunt cum non est.',
        'rating' => 3,
    ],
    [
        'comment' => 'Ea harum maiores quia a eius vero. Earum deleniti asperiores labore a praesentium velit. Cum molestias ullam dignissimos deleniti. Quia excepturi atque nihil alias.',
        'rating' => 2,
    ],
    [
        'comment' => 'Et odio soluta praesentium repudiandae animi delectus dolore. Ut ut nihil quis molestiae mollitia unde velit. Nisi ea nihil et aut harum aut qui.',
        'rating' => 4,
    ],
    [
        'comment' => 'Earum perferendis quod quia. Quia reprehenderit impedit ipsum tempore dolores ea occaecati. Consequuntur minima quia corrupti at ducimus voluptatem.',
        'rating' => 4,
    ],
    [
        'comment' => 'Consequatur aspernatur sed officia. Officiis beatae eum ex. Natus mollitia illum soluta voluptatem doloremque.',
        'rating' => 1,
    ],
    [
        'comment' => 'Modi expedita possimus id animi. Illum cum distinctio quia inventore repellendus recusandae vero a. Nulla est est nihil optio et non. Ex repellat unde cupiditate quaerat sed delectus.',
        'rating' => 5,
    ],
    [
        'comment' => 'Omnis veniam molestiae vitae numquam et similique omnis. Velit sed architecto placeat quis. Autem numquam ad consequuntur corporis quis aut unde tempora.',
        'rating' => 3,
    ],
    [
        'comment' => 'Quos saepe dolores quae qui totam. Quo dolorem quas ea ut quo iure. Labore nulla sapiente aliquid. Laborum cumque ratione esse voluptatum modi earum.',
        'rating' => 4,
    ],
];

$completedTasks = Task::find()
    ->select(['id', 'customer_id', 'worker_id'])
    ->where(['status' => Status::COMPLETED])
    ->asArray()
    ->all();

$fixtureData = [];
$reviewCount = min(count($completedTasks), count($data));

for ($i = 0; $i < $reviewCount; $i++) {
    $task = $completedTasks[$i];
    $reviewContent = $data[$i];

    $fixtureData[] = [
        'task_id' => $task['id'],
        'customer_id' => $task['customer_id'],
        'worker_id' => $task['worker_id'],
        'comment' => $reviewContent['comment'],
        'rating' => $reviewContent['rating'],
    ];
}

return $fixtureData;
