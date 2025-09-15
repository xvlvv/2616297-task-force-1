<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\User;

return [
    'user_id' => $faker->numberBetween(1,10),
    'day_of_birth' => $faker->dateTimeInInterval(interval: '-18 years'),
    'bio' => $faker->text(255),
    'phone_number' => $faker->phoneNumber(),
    'telegram_username' => $faker->userName(),
    'restrict_contacts' => $faker->boolean(),
];