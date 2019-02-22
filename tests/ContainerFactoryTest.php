<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-27
 * Time: 17:43
 */

namespace oxidprojects\DI\Tests;

use oxidprojects\DI\ContainerFactory;

/**
 * Class ContainerFactoryTest
 */
class ContainerFactoryTest extends \PHPUnit\Framework\TestCase
{

    public function testGetContainerSame()
    {
        $expect = ContainerFactory::getInstance();
        $actual = ContainerFactory::getInstance();

        $this->assertSame($expect, $actual);
    }

    public function testGetInstanceSame()
    {
        $expect = ContainerFactory::getInstance()->getContainer();
        $actual = ContainerFactory::getInstance()->getContainer();

        $this->assertSame($expect, $actual);
    }

    public function testGetInstance()
    {
        //Act
        $container = ContainerFactory::getInstance()->getContainer();

        //Assert
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Container::class, $container);
        $this->assertFileExists(OxidEsaleFileStructure::$url  . '/tmp/ceProjectServiceContainer.php');
        $this->assertContains('container.service.yml', $container->getServiceIds());
        $this->assertContains('container.services.yaml', $container->getServiceIds());
        $this->assertContains('container.moonhine', $container->getServiceIds());
    }

    public function testTagServices()
    {
        //Arrange
        $container = ContainerFactory::getInstance()->getContainer();

        //Act
        $planetCollection = $container->get('universe');

        //Assert
        $this->assertContains(\oxidprojects\DI\Tests\lib\TagService\MarsPlanet::class, $planetCollection->planet);
        $this->assertContains(\oxidprojects\DI\Tests\lib\TagService\SunPlanet::class, $planetCollection->planet);
    }
}
