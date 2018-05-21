<?php

namespace Ackly\YiiHealth;

use yii\base\BootstrapInterface;


/**
 * Class Module
 *
 * To use YiiHealth, include it as a module in the application configuration like the following:
 *
 * ~~~
 * return [
 *     'applicationName' => 'YOUR APP NAME',
 *     'bootstrap' => ['yii-health'],
 *     'modules' => [
 *         'yii' => ['class' => 'Ackly\YiiHealth\Module'],
 *     ],
 *     'checks' => [],
 *     'checkArgs' => [],
 *     'dependencies' => []
 * ]
 * ~~~
 *
 * @package Ackly\YiiHealth
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'Ackly\YiiHealth\Controllers';

    /**
     * @var string|null
     */
    public $applicationName = null;

    /**
     * @var string
     */
    public $route = '/health';

    /**
     * @var array
     */
    public $checks = [];

    /**
     * @var array
     */
    public $checkArgs = [];

    /**
     * @var array
     */
    public $dependencies = [];

    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->route, 'route' => $this->route . '/default/index'],
            ], false);
        }
    }
}