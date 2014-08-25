<?php

/*
 * This file is part of the Orchestra project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RomaricDrigon\OrchestraBundle\Finder;

/**
 * Interface EntityFinderInterface
 * @author Romaric Drigon <romaric.drigon@gmail.com>
 */
interface EntityFinderInterface
{
    /**
     * Add to EntityPool entities from given namespace
     *
     * @param string $namespace
     */
    public function addEntitiesFromNamespace($namespace);
} 