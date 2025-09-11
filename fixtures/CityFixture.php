<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class CityFixture extends ActiveFixture
{
    public $modelClass = 'app\models\City';
    public $dataFile = '@app/fixtures/data/city.php';

}