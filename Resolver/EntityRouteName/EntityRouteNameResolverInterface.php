<?php

/*
 * This file is part of the Orchestra project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RomaricDrigon\OrchestraBundle\Resolver\EntityRouteName;

use RomaricDrigon\OrchestraBundle\Core\Entity\EntityReflectionInterface;

/**
 * Interface EntityRouteNameResolverInterface
 * @author Romaric Drigon <romaric.drigon@gmail.com>
 */
interface EntityRouteNameResolverInterface
{
    const NAME_PREFIX = 'orchestra_entity';

    /**
     * @inheritdoc
     */
    public function getRouteName(EntityReflectionInterface $entityReflection, $methodName);
}
