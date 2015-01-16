<?php

namespace Tools\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Symfony\Component\Console;

class ConsoleApplicationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $helperSet = $serviceLocator->get('doctrinetools.helper_set');

        $cli = new Console\Application('Doctrine Tools', \DoctrineTools\Version::VERSION);
        $cli->setCatchExceptions(true);
        $cli->setAutoExit(false);
        $cli->setHelperSet($helperSet);
        $cli->addCommands(array(
            $serviceLocator->get('doctrinetools.dbal.runsql'),
            $serviceLocator->get('doctrinetools.dbal.import'),

            $serviceLocator->get('doctrinetools.orm.clear-cache.metadata'),
            $serviceLocator->get('doctrinetools.orm.clear-cache.result'),
            $serviceLocator->get('doctrinetools.orm.clear-cache.query'),
            $serviceLocator->get('doctrinetools.orm.schema-tool.create'),
            $serviceLocator->get('doctrinetools.orm.schema-tool.update'),
            $serviceLocator->get('doctrinetools.orm.schema-tool.drop'),
            $serviceLocator->get('doctrinetools.orm.ensure-production-settings'),
            $serviceLocator->get('doctrinetools.orm.convert-d1-schema'),
            $serviceLocator->get('doctrinetools.orm.generate-repositories'),
            $serviceLocator->get('doctrinetools.orm.generate-entities'),
            $serviceLocator->get('doctrinetools.orm.generate-proxies'),
            $serviceLocator->get('doctrinetools.orm.convert-mapping'),
            $serviceLocator->get('doctrinetools.orm.run-dql'),
            $serviceLocator->get('doctrinetools.orm.validate-schema'),
            $serviceLocator->get('doctrinetools.orm.info'),

            $serviceLocator->get('doctrinetools.migrations.execute'),
            $serviceLocator->get('doctrinetools.migrations.generate'),
            $serviceLocator->get('doctrinetools.migrations.migrate'),
            $serviceLocator->get('doctrinetools.migrations.status'),
            $serviceLocator->get('doctrinetools.migrations.version'),
            $serviceLocator->get('doctrinetools.migrations.diff'),
        ));

        return $cli;
    }
}
