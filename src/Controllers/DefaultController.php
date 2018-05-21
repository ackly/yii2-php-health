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

        $response = new Response();
        $response->content = $check->run($args);
        $response->format = Response::FORMAT_JSON;

        if ($check->result['status'] == 'error') {
            $response->setStatusCode(500);
        }

        return $response;
    }
}