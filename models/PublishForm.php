<?php

namespace app\models;

use Xvlvv\DTO\SaveTaskFileDTO;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Модель формы для публикации нового задания.
 */
class PublishForm extends Model
{
    /** @var string Название задания */
    public string $name = '';

    /** @var string Подробное описание задания */
    public string $description = '';

    /** @var int ID выбранной категории */
    public int $categoryId = 0;

    /** @var string|null Адрес или название места выполнения задания */
    public ?string $location = null;

    /** @var int|null Бюджет задания */
    public ?int $budget = null;

    /** @var string Срок выполнения задания */
    public string $endDate = '';

    /** @var string|null Широта местоположения */
    public ?string $latitude = null;

    /** @var string|null Долгота местоположения */
    public ?string $longitude = null;

    /** @var string|null Дополнительная информация о местоположении */
    public ?string $additionalInfo = null;

    /**
     * @var UploadedFile[]
     */
    public array $files = [];

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'description', 'categoryId', 'budget', 'endDate'], 'required', 'message' => 'Обязательное поле'],
            [
                'endDate',
                'compare',
                'compareValue' => date('Y-m-d'),
                'operator' => '>',
                'type' => 'date',
                'message' => 'Срок исполнения должен быть в будущем'
            ],
            [['files'], 'file', 'maxFiles' => 5],
            [['latitude', 'longitude', 'additionalInfo'], 'safe'],
            ['budget', 'integer', 'min' => 1, 'message' => 'Бюджет задания должен быть больше 0'],
        ];
    }

    /**
     * Обрабатывает загрузку файлов.
     * Сохраняет файлы во временную директорию и возвращает массив DTO с информацией о них.
     *
     * @return bool|SaveTaskFileDTO[] Массив DTO или false в случае ошибки валидации.
     * @throws Exception
     */
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