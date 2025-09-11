<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\City;

return [
    'name' => $faker->name(),
    'email' => $faker->email(),
    'password_hash' => Yii::$app->getSecurity()->generatePasswordHash($faker->password()),
    'role' => $faker->randomElement(['customer', 'worker']),
    'city_id' => $faker->numberBetween(1,10),
    'avatar_path' => $faker->imageUrl(),
];