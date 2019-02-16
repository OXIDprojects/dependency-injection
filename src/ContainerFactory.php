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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
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
        include_once $path;
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
     * Load all services.yaml of modules
     *
     * @param ContainerBuilder $container
     *
     * @return $this
     */
    private function loadModulesServiceYamlTo(ContainerBuilder $container)
    {
        $foundServiceYmls = $this->findServiceYmal();

        foreach ($foundServiceYmls as $servicesYaml) {
            $dirname = dirname($servicesYaml);
            $basename = basename($servicesYaml);
            $yamlFileLoader = new YamlFileLoader($container, new FileLocator($dirname));
            try {
                $yamlFileLoader->load($basename);
            } catch (\Exception $e) {
                getLogger()->error("$servicesYaml has a Error", ['error' => $e->getMessage()]);
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
        $this->registerCustomCompilerPass($container);
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
     * Search services.yaml in all modules folder
     *
     * @return array|false
     */
    private function findServiceYmal()
    {
        $DS = DIRECTORY_SEPARATOR;
        $vendor = $moduleid = "*";

        $moduleDir = OX_BASE_PATH . "modules";
        $modulePattern = "{$vendor}{$DS}{$moduleid}{$DS}";

        $service_yml = "{$modulePattern}service.yml";
        $paths = $this->streamSafeGlob($moduleDir, $service_yml);

        $services_yaml = "{$modulePattern}services.yaml";
        $paths = array_merge($paths, $this->streamSafeGlob($moduleDir, $services_yaml));

        return $paths;
    }

    /**
     * Glob that is safe with streams (vfs for example)
     *
     * @param string $directory
     * @param string $pathPattern
     * @see [Investigate glob()](https://github.com/mikey179/vfsStream/issues/2#issuecomment-252271019)
     * @return array
     */
    private function streamSafeGlob($directory, $pathPattern)
    {
        $DS = DIRECTORY_SEPARATOR;
        $files = scandir($directory);
        $found = [];

        $pathPattern = explode($DS, $pathPattern);
        $filePattern = array_shift($pathPattern);

        foreach ($files as $filename) {
            if (in_array($filename, ['.', '..'])) {
                continue;
            }

            if (fnmatch($filePattern, $filename)) {
                $path = "{$directory}{$DS}{$filename}";
                $fnmatch = [];

                if (!empty($pathPattern)) {
                    if (is_dir($path)) {
                        $fnmatch = $this->streamSafeGlob($path, join($DS, $pathPattern));
                    }
                } else {
                    $fnmatch[] = $path; //Success File found
                }
                $found = array_merge($found, $fnmatch);
            }
        }

        return $found;
    }

    /**
     * Search Service with Tag 'compiler.pass'
     *
     * @param ContainerBuilder $container
     * @see https://symfony.com/doc/current/service_container/tags.html#create-a-compiler-pass
     */
    private function registerCustomCompilerPass(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('compiler.pass');

        foreach ($serviceIds as $service => $extras) {
            /** @var CompilerPassInterface $customPass */
            $customPass = $container->get($service);
            $container->addCompilerPass($customPass);
        }
    }
}
