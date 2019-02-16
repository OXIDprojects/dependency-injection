<?php
/**
 * Created by LoberonEE.
 * Autor: Tobias Matthaiou <tm@loberon.de>
 * Date: 16.02.19
 * Time: 10:10
 */
namespace oxidprojects\DI\Tests\TagService;

/**
 * Class PlanetCollection
 * @package oxidprojects\DI\Tests\TagService
 */
class PlanetCollection
{
    /**
     * @var array
     */
    public $planet = [];

    /**
     * @param $planet
     */
    public function addPlanet($planet)
    {
        $this->planet[] = get_class($planet);
    }
}
