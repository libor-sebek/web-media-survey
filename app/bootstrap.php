<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->enableDebugger(__DIR__ . '/../log_application');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

$customConfig = NULL;
if($_SERVER['HTTP_HOST'] == 'localhost')
{
	$filePath = __DIR__ . '/config/config.local.neon';
	if(file_exists($filePath))
	{
		$customConfig = $filePath;
	}
}
else
{
	$filePath = __DIR__ . '/config/config.production.neon';
	if(file_exists($filePath))
	{
		$customConfig = $filePath;
	}

}
if($customConfig !== NULL)
{
	$configurator->addConfig($customConfig);
}


$container = $configurator->createContainer();

return $container;
