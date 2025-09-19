<?php

namespace app\fixtures;

use Faker\Factory;
use Yii;
use yii\test\ActiveFixture;

class ExecutorProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ExecutorProfile';
    public $depends = [
        'app\fixtures\UserFixture',
    ];

    protected function getData(): array
    {
        $faker = Factory::create('ru_RU');
        $data = [];
        $workerAliases = [];

        for ($i = 0; $i < UserFixture::WORKER_COUNT; $i++) {
            $workerAliases[] = 'worker' . $i;
        }

        foreach ($workerAliases as $alias) {
            $userId = UserFixture::getId($alias);

            if ($userId === null) {
                continue;
            }

            $age = $faker->numberBetween(18, 65);

            $ageString = Yii::t(
                'app',
                '{n, plural, one{# год} few{# года} many{# лет} other{# лет}}',
                ['n' => $age]
            );

            $bio = implode(', ', [
                $faker->country,
                $faker->city,
                $ageString
            ]);

            $profileAlias = $alias . '_profile';
            $data[$profileAlias] = [
                'user_id' => $userId,
                'day_of_birth' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                'bio' => $bio,
                'description' => $faker->realText(255),
                'phone_number' => $faker->numerify('###########'),
                'telegram_username' => $faker->userName,
                'restrict_contacts' => $faker->boolean,
            ];
        }

        return $data;
    }
}