<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class ExecutorProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ExecutorProfile';
    public $depends = [
        'app\fixtures\UserFixture',
    ];
    public $dataFile = '@app/fixtures/data/executor_profile.php';
}