<?php

/*
 * This file is part of the Orchestra project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RomaricDrigon\OrchestraBundle\Resolver\CommandFactory;

/**
 * Interface CommandFactoryResolverInterface
 * @author Romaric Drigon <romaric.drigon@gmail.com>
 */
interface CommandFactoryResolverInterface
{
    /**
     * @param string $commandClass name of the Command class
     * @return string name of the entity method to use as a Factory
     */
    public function getCommandFactory($commandClass);
}
