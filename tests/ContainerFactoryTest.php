<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-27
 * Time: 17:43
 */

use oxidprojects\DI\ContainerFactory;
use \Symfony\Component\Yaml\Yaml;

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

    public function testGetInstance()
    {
        //Arrange
        $directory = \org\bovigo\vfs\vfsStream::setup('root', 0755, [
            'source' => [
                'config.inc.php' => '<?php $this->sCompileDir  = __DIR__ . "/tmp";',
                'tmp' => [],
                'modules' => [
                    'tm' => [
                        'sunshine' => [
                            'service.yml' => Yaml::dump(['services' => ['unittest' => ['class' => static::class]]])
                        ]
                    ]
                ]
            ]

        ])->url();
        $directory .= DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;

        define('OX_BASE_PATH', $directory);

        //Act
        $container = ContainerFactory::getInstance()->getContainer();

        //Assert
        $this->assertInstanceOf(Symfony\Component\DependencyInjection\Container::class, $container);
        $this->assertFileExists($directory . '/tmp/ceProjectServiceContainer.php');
    }

    public function testGetInstanceSame()
    {
        $expect = ContainerFactory::getInstance()->getContainer();
        $actual = ContainerFactory::getInstance()->getContainer();


        $this->assertSame($expect, $actual);
    }
}
