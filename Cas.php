<?php

namespace alcad\cas;

use phpCAS;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;

class Cas extends Module implements BootstrapInterface
{
	public $controllerNamespace = 'alcad\cas\controllers';

	public function bootstrap($app)
	{
		if ($app->hasModule('cas') && ($module = $app->getModule('cas')) instanceof Module)
		{
			$this->_startCAS();
			if (\Yii::$app->user->isGuest)
			{
				$this->_yiiAccess();
			}
		}
	}

	private function _yiiAccess()
	{
		if (!Yii::$app->casUser->isGuest)
		{
			$u = new CasInterface();
			$u->username = Yii::$app->casUser->getUser();

			\Yii::$app->getUser()->login($u);
		}
		else
		{
			\Yii::$app->getUser()->logout(true);
		}
	}

	private function _startCAS()
	{
		$params = Yii::$app->params['cas'];
		$host = $params['host'];
		$port = $params['port'];
		$uri = $params['uri'];
		$filename = $params['log_file'];

		// Enable debugging
		phpCAS::setDebug($filename);

		// Initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0, $host, $port, $uri);
		phpCAS::setNoCasServerValidation();
	}

}
