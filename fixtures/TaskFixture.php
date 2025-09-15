<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Task';
    public $depends = [
        'app\fixtures\CategoryFixture',
        'app\fixtures\CityFixture',
        'app\fixtures\UserFixture',
    ];
    public $dataFile = '@app/fixtures/data/task.php';
}