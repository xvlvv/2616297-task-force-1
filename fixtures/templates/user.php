<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use Xvlvv\Enums\UserRole;

return [
    'name' => $faker->name(),
    'email' => $faker->email(),
    'password_hash' => Yii::$app->getSecurity()->generatePasswordHash($faker->password()),
    'role' => $faker->randomElement([UserRole::CUSTOMER, UserRole::WORKER]),
    'city_id' => $faker->numberBetween(1,10),
    'avatar_path' => $faker->imageUrl(),
];