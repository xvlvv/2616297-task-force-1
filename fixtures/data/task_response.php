<?php

use app\models\Task;
use app\models\User;
use Xvlvv\Enums\Status;
use Xvlvv\Enums\UserRole;

$data = [
    'task_response0' => [
        'comment' => 'Est laborum et quas modi sed id. Inventore temporibus doloribus earum perferendis magnam.',
        'is_rejected' => false,
    ],
    'task_response1' => [
        'comment' => 'Quis maiores omnis eum eligendi modi nihil aut. Doloremque enim reprehenderit blanditiis eveniet iusto. Et voluptatibus quisquam harum quaerat non aliquid officia officiis.',
        'is_rejected' => true,
    ],
    'task_response2' => [
        'comment' => 'Aliquam dicta perspiciatis perferendis facere amet veniam. Laudantium aliquid corporis saepe veritatis omnis id est. Voluptas similique totam excepturi illum.',
        'is_rejected' => true,
    ],
    'task_response3' => [
        'comment' => 'Sit nisi aliquam neque. Quisquam alias dolorem nulla quis sunt aut. Eius sunt ullam alias adipisci aut.',
        'is_rejected' => false,
    ],
    'task_response4' => [
        'comment' => 'Cum expedita temporibus illum voluptatem cumque ullam. Occaecati non nesciunt ullam culpa distinctio natus. Aliquid aut maiores quia et.',
        'is_rejected' => true,
    ],
    'task_response5' => [
        'comment' => 'At dolorum nobis voluptas deleniti. Expedita vero sapiente velit ipsa non soluta officiis qui. Et porro delectus consectetur voluptatem minus.',
        'is_rejected' => false,
    ],
    'task_response6' => [
        'comment' => 'Eum et quasi est assumenda qui. Culpa odit aut beatae nam autem. Voluptatem quidem quia nisi. Et et sint placeat nobis est. Aut aut corporis eum atque accusantium.',
        'is_rejected' => false,
    ],
    'task_response7' => [
        'comment' => 'Laboriosam accusantium neque numquam quaerat architecto. Est itaque voluptatem perferendis a est.',
        'is_rejected' => true,
    ],
    'task_response8' => [
        'comment' => 'Exercitationem molestiae ut rem nisi. Et enim dicta consequatur officiis quo dolor ea. Odio voluptatem nam doloribus saepe qui asperiores aspernatur incidunt.',
        'is_rejected' => false,
    ],
    'task_response9' => [
        'comment' => 'Qui sed rem suscipit tenetur dignissimos. Debitis earum tempore magni. Consectetur et ducimus soluta et.',
        'is_rejected' => true,
    ],
];

$faker = Faker\Factory::create();
$tasks = Task::find()->select(['id', 'budget'])->where(['status' => Status::NEW])->all();
$workerIds = User::find()->select('id')->where(['role' => UserRole::WORKER])->column();

foreach ($data as &$taskResponse) {
    $task = $tasks[array_rand($tasks)];
    $taskResponse['worker_id'] = $faker->randomElement($workerIds);
    $taskResponse['task_id'] = $task['id'];
    $taskResponse['price'] = $faker->numberBetween(0, $task['budget']);
}

return $data;
