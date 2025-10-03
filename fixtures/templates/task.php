<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Category;
use app\models\City;
use app\models\User;
use Xvlvv\Enums\Status;

return [
    'name' => $faker->sentence(4),
    'description' => $faker->text(255),
    'category_id' => $faker->numberBetween(1, 10),
    'customer_id' => $faker->numberBetween(1, 10),
    'worker_id' => $faker->numberBetween(1, 10),
    'city_id' => $faker->numberBetween(1, 10),
    'status' => $faker->randomElement(array_map(fn($status) => $status->value, Status::cases())),
    'budget' => $faker->numberBetween(1, 1000000),
    'latitude' => $faker->latitude,
    'longitude' => $faker->longitude,
    'end_date' => $faker->dateTimeInInterval('now', '+1 years'),
];