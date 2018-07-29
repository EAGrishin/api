<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 27.07.18
 * Time: 20:10
 */

namespace app\models;

use Yii;
use \yii\filters\RateLimitInterface;


class Rate implements RateLimitInterface
{

    public $rateLimit = 2;
    public $rateTime = 60;


    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->rateTime]; // max $rateLimit request per $rateTime minutes
    }

    public function loadAllowance($request, $action)
    {

        if (Yii::$app->user->identity->isFree()) {
            $cacheKey = 'rate_limit_' . Yii::$app->user->id;
            if (Yii::$app->cache->exists($cacheKey)) {
                $data = Yii::$app->cache->get($cacheKey);
                return [$data['allowance'], $data['timestamp']];
            }
        }
        return [$this->rateLimit, time()];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        if (Yii::$app->user->identity->isFree()) {
            $cacheKey = 'rate_limit_' . Yii::$app->user->id;
            $data = [
                'allowance' => $allowance,
                'timestamp' => $timestamp,
            ];
            Yii::$app->cache->set($cacheKey, $data, $this->rateTime);
        }
    }
}