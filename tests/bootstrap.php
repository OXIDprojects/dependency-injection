<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-28
 * Time: 08:19
 */
namespace oxidprojects\DI\Tests;

use \Symfony\Component\Yaml\Yaml;

/**
 * Class OxidEsaleFileStructure
 *
 * Simuliert die OXID Framework strucktur
 */
class OxidEsaleFileStructure
{
    public static $url;

    public static function init()
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
                        'tagService' => [
                            'services.yaml' => file_get_contents(__DIR__ . $DS . 'lib' . $DS . 'TagService' . $DS . 'services.yaml')
                        ],
                    ],
                    'oe' => [
                        'paydings' => [
                            'metadata.php' => '<?php'
                        ],
                        'vendormetadata.php' => '',
                    ]
                ]
            ]

        ])->url();
        self::$url .= DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;

        define('OX_BASE_PATH', self::$url);
    }
}

(new OxidEsaleFileStructure)->init();
