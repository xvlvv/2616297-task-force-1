<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class CustomerProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CustomerProfile';
    public $depends = [
        'app\fixtures\UserFixture',
    ];

    protected function getData(): array
    {
        $data = [];

        $customerAliases = [];
        for ($i = 0; $i < UserFixture::CUSTOMER_COUNT; $i++) {
            $customerAliases[] = 'customer' . $i;
        }

        foreach ($customerAliases as $alias) {
            $userId = UserFixture::getId($alias);

            if ($userId === null) {
                continue;
            }

            $profileAlias = $alias . '_profile';
            $data[$profileAlias] = [
                'user_id' => $userId,
            ];
        }

        return $data;
    }
}