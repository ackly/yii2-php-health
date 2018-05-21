<?php

namespace Ackly\YiiHealth\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DefaultController
 *
 * @package Ackly\YiiHealth\Controllers
 */
class DefaultController extends Controller
{
    /**
     * @var \Ackly\YiiHealth\Module;
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $check = new \Ackly\Health\HealthCheck($this->module->applicationName);

        $check->addDependency($this->module->dependencies);

        $checks = $this->module->checks;
        $args = $this->module->checkArgs;

        foreach ($checks as $name => $value) {
            $check->addCheck($name, $value);
        }

        foreach ($args as $name => &$value) {
            if (is_callable($value)) {
                $args[$name] = $value();
            }

            if (isset($value['connection'])) {
                $value['pdo'] = Yii::$app->get($value['connection'])->getMasterPdo();
                unset($value['connection']);
            }
        }

        $content = $check->run($args);

        if ($check->result['status'] == 'error') {
            Yii::$app->response->statusCode = 500;
        }

        return $content;
    }
}