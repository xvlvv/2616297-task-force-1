<?php

namespace app\controllers\api;

use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use Xvlvv\Services\Application\LocationService;
use yii\filters\ContentNegotiator;


class LocationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['search'],
                        'roles' => ['@']
                    ],
                ],
            ]
        ];
    }

    /**
     * Поиск локаций для автодополнения в форме публикации задания
     * @param LocationService $locationService
     * @param string|null $query
     * @return array
     */
    public function actionSearch(LocationService $locationService, ?string $query = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->user->identity->getUser()->getId();

        return $locationService->searchForAutocomplete($query, $userId);
    }
}