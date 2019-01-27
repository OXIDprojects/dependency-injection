<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-26
 * Time: 18:40
 */

namespace oxidprojects\DI;

use OxidEsales\Eshop\Core\ConfigFile;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class Container
 * @package oxidprojects\DI
 */
class ContainerFactory
{
    /**
     * @var ContainerFactory
     */
    protected static $instance = null;

    /**
     * @var Container
     */
    protected $project_container = null;

    /**
     * ContainerFactory constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return ContainerFactory
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        // prozess cache
        if ($this->project_container === null) {
            $this->project_container = $this->init();
        }

        return $this->project_container;
    }

    /**
     * @return Container|ProjectServiceContainer
     */
    private function init()
    {
        $filename = $this->getPathCompiledContainer();

        if (false === file_exists($filename) ) {
            $this->buildContainer($filename);
        }

        return $this->loadCompiledContainer($filename);
    }

    /**
     * @return string
     */
    private function getPathCompiledContainer()
    {
        $config = new ConfigFile(OX_BASE_PATH . "config.inc.php");

        if (false == is_dir($config->sCompileDir)) {
            throw new FileNotFoundException($config->sCompileDir);
        }

        $path_compiled_file = $config->sCompileDir . '/ceProjectServiceContainer.php';

        return $path_compiled_file;
    }

    /**
     * @param $path_compiled_file
     * @return \oxidprojects\DI\ProjectServiceContainer
     */
    private function loadCompiledContainer($path)
    {
        include $path;
        return new \oxidprojects\DI\ProjectServiceContainer();
    }

    /**
     * Create new container
     *
     * @param string $savePath
     */
    private function buildContainer($savePath)
    {
        $container = new ContainerBuilder();

        $this->loadModulesServiceYamlTo($container)
             ->compiled($container)
             ->persist($container, $savePath);
    }

    /**
     * Load all service.yml of modules
     *
     * @param ContainerBuilder $container
     *
     * @return $this
     */
    private function loadModulesServiceYamlTo(ContainerBuilder $container)
    {
        $foundServiceYmls = $this->findServiceYmal();

        foreach ($foundServiceYmls as $serviceYml) {
            $dirname = dirname($serviceYml);
            $yamlFileLoader = new YamlFileLoader($container, new FileLocator($dirname));
            try {
                $yamlFileLoader->load('service.yml');
            } catch (\Exception $e) {
                getLogger()->error("$serviceYml has a Error", ['error' => $e->getMessage()]);
            }
        }

        return $this;
    }

    /**
     * Save compiled containter
     *
     * @param ContainerBuilder $container
     *
     * @return $this
     */
    private function compiled(ContainerBuilder $container)
    {
        $container->compile();

        return $this;
    }

    /**
     * @param $container
     *
     * @param $savePath
     */
    private function persist($container, $savePath)
    {
        $dumper = new PhpDumper($container);

        $phpcode = $dumper->dump(['namespace' => 'oxidprojects\DI']);

        file_put_contents($savePath, $phpcode);
    }


    /**
     * Search service.yml in all modules folder
     *
     * @return array|false
     */
    private function findServiceYmal()
    {
        $DS = DIRECTORY_SEPARATOR;
        $vendor = $moduleid = "*";

        $path = OX_BASE_PATH . "modules{$DS}{$vendor}{$DS}{$moduleid}{$DS}service.yml";

        return glob($path);
    }
}
