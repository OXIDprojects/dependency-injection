<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-27
 * Time: 17:43
 */

use oxidprojects\DI\ContainerFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ContainerFactoryTest
 */
class ContainerFactoryTest extends \PHPUnit\Framework\TestCase
{

    private static  $url;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        $DS = DIRECTORY_SEPARATOR;
        self::$url = \org\bovigo\vfs\vfsStream::setup('root', 0755, [
            'source' => [
                'config.inc.php' => '<?php $this->sCompileDir  = __DIR__ . "/tmp";',
                'tmp' => [],
                'modules' => [
                    'tm' => [
                        'sunshine' => [
                            'service.yml' => Yaml::dump(['services' => ['container.service.yml' => ['class' => static::class]]]),
                            'services.yaml' => Yaml::dump(['services' => ['container.services.yaml' => ['class' => static::class]]])
                        ],
                        'moonhine' => [
                            'services.yaml' => Yaml::dump(['services' => ['container.moonhine' => ['class' => static::class]]])
                        ],
                    ]
                ]
            ]

        ])->url();
        self::$url  .= DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;

        if (!defined('OX_BASE_PATH')) {
            define('OX_BASE_PATH', self::$url);
        }
    }

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
        $this->assertInstanceOf(Symfony\Component\DependencyInjection\Container::class, $container);
        $this->assertFileExists(self::$url  . '/tmp/ceProjectServiceContainer.php');
        $this->assertContains('container.service.yml', $container->getServiceIds());
        $this->assertContains('container.services.yaml', $container->getServiceIds());
        $this->assertContains('container.moonhine', $container->getServiceIds());
    }

}
