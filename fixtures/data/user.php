<?php

use app\models\City;
use Xvlvv\Enums\UserRole;
use app\fixtures\UserFixture;

$faker = Faker\Factory::create('ru_RU');
$cityIds = City::find()->select('id')->column();
$avatarsPath = Yii::getAlias('@app/web/img/avatars');
$avatarFiles = array_diff(scandir($avatarsPath), ['.', '..']);
$avatarsUrlPath = '/img/avatars/';

$data = [];

for ($i = 0; $i < UserFixture::WORKER_COUNT; $i++) {
    $data['worker' . $i] = [
        'name' => $faker->name(),
        'email' => $faker->unique()->email,
        'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('password_123'),
        'role' => UserRole::WORKER->value,
        'avatar_path' => $avatarsUrlPath . $faker->randomElement($avatarFiles),
        'city_id' => $faker->randomElement($cityIds),
    ];
}

for ($i = 0; $i < UserFixture::CUSTOMER_COUNT; $i++) {
    $data['customer' . $i] = [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('password_123'),
        'role' => UserRole::CUSTOMER->value,
        'avatar_path' => $avatarsUrlPath . $faker->randomElement($avatarFiles),
        'city_id' => $faker->randomElement($cityIds),
    ];
}

return $data;