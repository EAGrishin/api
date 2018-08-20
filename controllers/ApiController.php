<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\RateLimiter;
use yii\filters\AccessControl;
use app\models\Rate;

class ApiController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders' => false,
            'user' => new Rate()
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['ip'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }


    public function actionIp()
    {
        $ip = json_decode(Yii::$app->request->post('body'), true);
        $data = Yii::$app->geoip->ip($ip);
        if ($data) {
            return [
                'country' => $data->country,
                'city' => $data->city
            ];
        } else {
            return "Sorry...This country could not be found";
        }
    }

}
