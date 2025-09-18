<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use Xvlvv\Enums\UserRole;

$avatarsPath = Yii::getAlias('@app/web/img/avatars');
$avatarFiles = array_diff(scandir($avatarsPath), ['.', '..']);
$avatarsUrlPath = '/img/avatars/';

return [
    'name' => $faker->name(),
    'email' => $faker->email(),
    'password_hash' => Yii::$app->getSecurity()->generatePasswordHash($faker->password()),
    'role' => $faker->randomElement([UserRole::CUSTOMER, UserRole::WORKER]),
    'avatar_path' => $avatarsUrlPath . $faker->randomElement($avatarFiles),
];