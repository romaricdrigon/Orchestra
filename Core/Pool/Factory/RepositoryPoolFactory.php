<?php

/*
 * This file is part of the Orchestra project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RomaricDrigon\OrchestraBundle\Core\Pool\Factory;

use RomaricDrigon\OrchestraBundle\Core\Repository\RepositoryDefinition;
use RomaricDrigon\OrchestraBundle\Core\Pool\RepositoryPoolInterface;
use RomaricDrigon\OrchestraBundle\Core\Pool\RepositoryPool;
use RomaricDrigon\OrchestraBundle\Core\Repository\RepositoryDefinitionFactory;

/**
 * Class RepositoryPoolFactory
 * @author Romaric Drigon <romaric.drigon@gmail.com>
 */
class RepositoryPoolFactory
{
    /**
     * @var RepositoryDefinition[]
     */
    protected $definitions = [];

    /**
     * @var RepositoryDefinitionFactory
     */
    protected $definitionFactory;

    /**
     * @param RepositoryDefinitionFactory $definitionFactory
     */
    public function __construct(RepositoryDefinitionFactory $definitionFactory)
    {
        $this->definitionFactory = $definitionFactory;
    }

    /**
     * Add a repository, service from Symfony DIC, to the pool
     *
     * @param string $repositoryClass
     * @param string $serviceId
     * @param string $entityClass
     */
    public function addRepository($repositoryClass, $serviceId, $entityClass)
    {
        $repositoryDefinition = $this->definitionFactory->build($repositoryClass, $serviceId, $entityClass);

        // this array is not ordered by slug
        $this->definitions[] = $repositoryDefinition;
    }

    /**
     * Factory method for the EntityPool
     *
     * @return RepositoryPoolInterface
     */
    public function createPool()
    {
        $pool = new RepositoryPool();

        foreach ($this->definitions as $definition) {
            $pool->addRepositoryDefinition($definition);
        }

        return $pool;
    }
}
