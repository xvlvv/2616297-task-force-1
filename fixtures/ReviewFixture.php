<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class ReviewFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Review';
    public $depends = [
        'app\fixtures\TaskFixture',
        'app\fixtures\UserFixture',
    ];
    public $dataFile = '@app/fixtures/data/review.php';
}