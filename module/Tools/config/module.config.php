<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'doctrinetools' => array(
                    'type' => 'colon',
                    'options' => array(
                        'defaults' => array(
                            'controller' => 'DoctrineTools\Controller\Index',
                            'action' => 'index'
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'DoctrineTools\Controller\Index' => 'DoctrineTools\Controller\IndexController'
        )
    ),
    'route_manager' => array(
        'invokables' => array(
            'colon' => 'DoctrineTools\Mvc\Router\Console\SymfonyCli',
        ),
    ),
    'doctrinetools' => array(
        'migrations' => array(
            'directory' => 'data/DoctrineTools/Migrations',
            'namespace' => 'DoctrineTools\Migrations',
            'table' => 'migrations'
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'doctrinetools.migrations_configuration' => 'Tools\Factory\Migrations\ConfigurationFactory',
            'doctrinetools.helper_set' => 'Tools\Factory\HelperSetFactory',
            'doctrinetools.console_application' => 'Tools\Factory\ConsoleApplicationFactory',
            // Migrations commands
            'doctrinetools.migrations.generate' => 'Tools\Factory\Migrations\CommandGenerateFactory',
            'doctrinetools.migrations.execute' => 'Tools\Factory\Migrations\CommandExecuteFactory',
            'doctrinetools.migrations.migrate' => 'Tools\Factory\Migrations\CommandMigrateFactory',
            'doctrinetools.migrations.status' => 'Tools\Factory\Migrations\CommandStatusFactory',
            'doctrinetools.migrations.version' => 'Tools\Factory\Migrations\CommandVersionFactory',
            'doctrinetools.migrations.diff' => 'Tools\Factory\Migrations\CommandDiffFactory',
        ),
        'invokables' => array(
            // DBAL commands
            'doctrinetools.dbal.runsql' => '\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand',
            'doctrinetools.dbal.import' => '\Doctrine\DBAL\Tools\Console\Command\ImportCommand',
            // ORM Commands
            'doctrinetools.orm.clear-cache.metadata' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
            'doctrinetools.orm.clear-cache.result' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
            'doctrinetools.orm.clear-cache.query' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
            'doctrinetools.orm.schema-tool.create' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
            'doctrinetools.orm.schema-tool.update' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
            'doctrinetools.orm.schema-tool.drop' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
            'doctrinetools.orm.ensure-production-settings' => '\Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
            'doctrinetools.orm.convert-d1-schema' => '\Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand',
            'doctrinetools.orm.generate-repositories' => '\Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand',
            'doctrinetools.orm.generate-entities' => '\Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand',
            'doctrinetools.orm.generate-proxies' => '\Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
            'doctrinetools.orm.convert-mapping' => '\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand',
            'doctrinetools.orm.run-dql' => '\Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
            'doctrinetools.orm.validate-schema' => '\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
            'doctrinetools.orm.info' => '\Doctrine\ORM\Tools\Console\Command\InfoCommand',
        )
    )
);
