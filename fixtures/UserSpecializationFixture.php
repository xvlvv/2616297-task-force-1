<?php

namespace app\fixtures;

use app\models\Category;
use Faker\Factory;
use yii\test\ActiveFixture;

class UserSpecializationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserSpecialization';

    public $depends = [
        'app\fixtures\UserFixture',
        'app\fixtures\CategoryFixture',
    ];

    protected function getData(): array
    {
        $faker = Factory::create();
        $data = [];

        $categoryIds = Category::find()->select('id')->column();

        $workerAliases = [];
        for ($i = 0; $i < UserFixture::WORKER_COUNT; $i++) {
            $workerAliases[] = 'worker' . $i;
        }

        foreach ($workerAliases as $alias) {
            $userId = UserFixture::getId($alias);

            if ($userId === null) {
                continue;
            }

            $specializationsCount = $faker->numberBetween(1, 3);
            $randomCategoryIds = $faker->randomElements($categoryIds, $specializationsCount);

            foreach ($randomCategoryIds as $categoryId) {
                $recordAlias = 'user_' . $userId . '_category_' . $categoryId;

                $data[$recordAlias] = [
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ];
            }
        }

        return $data;
    }
}