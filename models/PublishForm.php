<?php

namespace app\models;

use Xvlvv\DTO\SaveTaskFileDTO;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class PublishForm extends Model
{
    public string $name = '';
    public string $description = '';
    public int $categoryId = 0;
    public ?string $location = null;
    public ?int $budget = null;
    public string $endDate = '';
    public ?string $latitude = null;
    public ?string $longitude = null;
    public ?string $additionalInfo = null;

    /**
     * @var UploadedFile[]
     */
    public array $files = [];

    public function rules(): array
    {
        return [
            [['name', 'description', 'categoryId', 'budget', 'endDate'], 'required', 'message' => 'Обязательное поле'],
            [['files'], 'file', 'maxFiles' => 5],
            [['latitude', 'longitude', 'additionalInfo'], 'safe'],
            ['budget', 'integer', 'min' => 1, 'message' => 'Бюджет задания должен быть больше 0'],
        ];
    }

    public function upload(): bool|array
    {
        if (!$this->validate()) {
            return false;
        }

        $filesDTO = [];

        $files = UploadedFile::getInstances($this, 'files');

        foreach ($files as $file) {
            $baseName = $file->baseName;

            $aliasedPath = '@app/uploads/' . Yii::$app->getSecurity()->generateRandomString() . '.' . $file->extension;
            $uploadPath = Yii::getAlias($aliasedPath);
            $file->saveAs($uploadPath);
            $filesDTO[] = new SaveTaskFileDTO($baseName, $uploadPath);
        }

        return $filesDTO;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Опишите суть работы',
            'description' => 'Подробности задания',
            'categoryId' => 'Категория',
            'budget' => 'Бюджет',
            'endDate' => 'Срок исполнения',
            'files' => 'Файлы',
            'location' => 'Локация',
        ];
    }
}