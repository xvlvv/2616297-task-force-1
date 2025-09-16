<?php

use app\models\Category;
use app\models\City;
use app\models\User;
use Xvlvv\Enums\UserRole;

$faker = Faker\Factory::create();
$cityIds = City::find()->select('id')->column();
$categoryIds = Category::find()->select('id')->column();
$customerIds = User::find()->select('id')->where(['role' => UserRole::CUSTOMER])->column();
$workerIds = User::find()->select('id')->where(['role' => UserRole::WORKER])->column();

return [
    'task0' => [
        'name' => 'Nulla tempore molestias quia iusto.',
        'description' => 'A doloribus molestiae molestiae. Velit dolores aut asperiores omnis incidunt. Expedita voluptatibus voluptatem minus nesciunt modi nam sunt. At consequatur molestiae molestias blanditiis.',
        'category_id' => $faker->randomElement($categoryIds),
        'customer_id' => $faker->randomElement($customerIds),
        'worker_id' => $faker->randomElement($workerIds),
        'city_id' => $faker->randomElement($cityIds),
        'status' => 'completed',
        'budget' => 101620,
        'latitude' => 72.856754,
        'longitude' => -173.378056,
        'end_date' => unserialize('O:8:"DateTime":3:{s:4:"date";s:26:"2026-04-17 17:59:31.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}')->format('Y-m-d H:i:s'),
    ],
    'task1' => [
        'name' => 'Vel est iste mollitia qui laudantium.',
        'description' => 'Similique magnam tenetur aut quo repellendus molestias est vero. Placeat harum veritatis doloremque enim assumenda.',
        'category_id' => $faker->randomElement($categoryIds),
        'customer_id' => $faker->randomElement($customerIds),
        'worker_id' => $faker->randomElement($workerIds),
        'city_id' => $faker->randomElement($cityIds),
        'status' => 'failed',
        'budget' => 521563,
        'latitude' => 19.964954,
        'longitude' => -66.537987,
        'end_date' => unserialize('O:8:"DateTime":3:{s:4:"date";s:26:"2026-02-20 21:32:10.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}')->format('Y-m-d H:i:s'),
    ],
    'task2' => [
        'name' => 'Ut error voluptatem sint.',
        'description' => 'Nemo et quo laudantium iste. Perferendis et dolorem dolores delectus dolorem. Accusantium officiis sed blanditiis.',
        'category_id' => $faker->randomElement($categoryIds),
        'customer_id' => $faker->randomElement($customerIds),
        'city_id' => $faker->randomElement($cityIds),
        'status' => 'new',
        'budget' => 940797,
        'latitude' => -58.323756,
        'longitude' => 173.529798,
        'end_date' => unserialize('O:8:"DateTime":3:{s:4:"date";s:26:"2026-07-11 04:54:20.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}')->format('Y-m-d H:i:s'),
    ],
    'task3' => [
        'name' => 'Voluptatem voluptatem veritatis quidem vero.',
        'description' => 'Quas laudantium voluptate quae est. Minima repudiandae rerum illum et sunt est eos. Ut possimus est at velit sapiente.',
        'category_id' => $faker->randomElement($categoryIds),
        'customer_id' => $faker->randomElement($customerIds),
        'city_id' => $faker->randomElement($cityIds),
        'status' => 'new',
        'budget' => 296880,
        'latitude' => 56.877977,
        'longitude' => -82.669877,
        'end_date' => unserialize('O:8:"DateTime":3:{s:4:"date";s:26:"2026-07-27 09:24:48.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}')->format('Y-m-d H:i:s'),
    ],
    'task4' => [
        'name' => 'Quisquam voluptatem et odio veritatis voluptatem.',
        'description' => 'Ullam et culpa numquam et et doloremque quos. Tempora tempora quibusdam quia harum. Nihil accusantium id omnis. Cupiditate vel dolorem ducimus quas iure quo.',
        'category_id' => $faker->randomElement($categoryIds),
        'customer_id' => $faker->randomElement($customerIds),
        'worker_id' => $faker->randomElement($workerIds),
        'city_id' => $faker->randomElement($cityIds),
        'status' => 'failed',
        'budget' => 836299,
        'latitude' => 16.678188,
        'longitude' => 178.988089,
        'end_date' => unserialize('O:8:"DateTime":3:{s:4:"date";s:26:"2026-06-01 02:54:00.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}')->format('Y-m-d H:i:s'),
    ],
];
