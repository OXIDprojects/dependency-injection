<?php
/**
 * Created by LoberonEE.
 * Autor: Tobias Matthaiou
 * Date: 16.02.19
 * Time: 10:13
 */

namespace oxidprojects\DI\Tests\lib\TagService;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ComposerPassPlanet
 * @package oxidprojects\DI\Tests
 */
class ComposerPassPlanet implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('universe')) {
            return;
        }

        $planetCollectionDef = $container->findDefinition('universe');

        $taggedServices = $container->findTaggedServiceIds('planet.universe');

        foreach ($taggedServices as $id => $tags) {
            $planetCollectionDef->addMethodCall('addPlanet', [new Reference($id)]);
        }
    }
}
