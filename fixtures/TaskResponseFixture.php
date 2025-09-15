<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskResponseFixture extends ActiveFixture
{
    public $modelClass = 'app\models\TaskResponse';
    public $depends = [
        'app\fixtures\TaskFixture',
        'app\fixtures\ExecutorProfileFixture',
        'app\fixtures\UserFixture',
    ];
    public $dataFile = '@app/fixtures/data/task_response.php';
}