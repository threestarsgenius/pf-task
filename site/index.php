<?php

DEFINE('APP_PATH', __DIR__.'/..');
DEFINE('TEMPLATE_DIR', APP_PATH.'/app/views/');

date_default_timezone_set('Europe/Kiev');
try {

	//Read the configuration
	$config = new \Phalcon\Config\Adapter\Ini(__DIR__.'/../app/config/config.ini');

	//Register an autoloader
	$loader = new \Phalcon\Loader();
	//Register some namespaces
	$loader->registerNamespaces($config->library->toArray());
	$loader->registerDirs($config->path->toArray())->register();

	//Create a DI
	$di = new \Phalcon\DI\FactoryDefault();

	$di->set('config', $config);

	//Setting up the view component
	$di->set('view', call_user_func('InitApp::initView'));

	//Start the session the first time when some component request the session service
	$di->set('db', call_user_func('InitApp::initDb'));

	//add dispatcher which handle wrong controllers and actions and other errors
	$di->set('dispatcher', call_user_func('InitApp::initDispatcher'), true);

	//Handle the request
	$application = new \Phalcon\Mvc\Application($di);

	\Phalcon\Tag::setDoctype(\Phalcon\Tag::HTML401_STRICT);

	$application->getDI()->getResponse()->setContentType('text/html', 'UTF-8');

	echo $application->handle()->getContent();

}
catch(\Phalcon\Exception $e) {
	error_log("PhalconException: ". $e->__toString());
}
catch(Exception $e) {
	error_log("Exception: ". $e->__toString());
}
