<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $depends = ['app\fixtures\CityFixture'];
    public $dataFile = '@app/fixtures/data/user.php';

    public const WORKER_COUNT = 10;
    public const CUSTOMER_COUNT = 10;
    private static array $idMap = [];

    public function load()
    {
        parent::load();
        self::$idMap = [];
        foreach ($this->data as $alias => $row) {
            self::$idMap[$alias] = $row['id'];
        }
    }
    public static function getId(string $alias): ?int
    {
        return self::$idMap[$alias] ?? null;
    }
}