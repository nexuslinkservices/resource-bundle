<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Registry;

use Symfony\Cmf\Component\Resource\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Puli\Repository\Api\Resource\Resource;
use Puli\Repository\Api\ResourceRepository;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RepositoryRegistry implements RepositoryRegistryInterface
{
    private $factories = [];
    private $configurations = [];
    private $instances = [];
    private $typeMap = [];

    private $defaultInstanceName;

    public function __construct(
        array $factories,
        array $configurations,
        $defaultInstanceName
    )
    {
        $this->factories = $factories;
        $this->defaultInstanceName = $defaultInstanceName;
        $this->configurations = $configurations;
    }


    /**
     * {@inheritdoc}
     */
    public function get($instanceName)
    {
        if (!isset($this->instances[$repositoryInstanceName])) {
            $this->instances[$repositoryInstanceName] = $this->createRepositoryInstance($instanceName);
        }

        return $this->instances[$repositoryInstanceName];
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryAlias(ResourceRepository $repository)
    {
        foreach ($this->instances as $alias => $repositoryInstance) {
            if ($repositoryInstance === $repository) {
                return $alias;
            }
        }

        throw new \RuntimeException(sprintf(
            'Could not determine registration name for repository of type "%s".' .
            'No matching repository instance found.',
            get_class($repository)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryType(ResourceRepository $repository)
    {
        $repositoryClass = get_class($repository);
        if (!isset($this->typeMap[$repositoryClass])) {
            throw new \InvalidArgumentException(sprintf(
                'No repository has been instantiated of class "%s", cannot determine the type.',
                $repositoryClass
            ));
        }

        return $this->typeMap[$repositoryClass];
    }

    private function createRepositoryInstance($instanceName)
    {
        if (!isset($this->configurations[$instanceName])) {
            throw new \InvalidArgumentException(sprintf(
                'Repository instance "%s" has not been registered, available repository instances: "%s"',
                $instanceName,
                implode('", "', array_keys($this->configurations))
            ));
        }

        $config = $this->configurations[$instanceName];

        if (!isset($config['type'])) {
            throw new \InvalidArgumentException(sprintf(
                'Each instance configuration must have a "type" key (for configuration "%s")',
                $instanceName
            ));
        }

        $type = $config['type'];
        unset($config['type']);

        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown repository type "%s", known types: "%s"',
                $type, implode('", "', array_keys($this->factories))
            ));
        }

        $factory = $this->factories[$type];
        $this->typeMap[get_class($factory)] = $type;
        $defaultConfig = $factory->getDefaultConfig();
        $configDiff = array_diff(array_keys($config), array_keys($defaultConfig));
        if ($configDiff) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration keys "%s" for repository type "%s", valid config keys: "%s"',
                implode('", "', $configDiff),
                $type,
                implode('", "', array_keys($defaultConfig))
            ));
        }

        return $factory->create($config);
    }
}
