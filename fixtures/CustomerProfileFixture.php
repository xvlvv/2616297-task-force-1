<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class CustomerProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CustomerProfile';
    public $depends = [
        'app\fixtures\UserFixture',
    ];
    public $dataFile = '@app/fixtures/data/customer_profile.php';
}