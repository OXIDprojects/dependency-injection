<?php
/**
 * Created by PhpStorm.
 * User: tobi
 * Date: 2019-01-26
 * Time: 18:38
 */

/**
 * Symfony DependencyInjection für Privaten gebrauch
 *
 * Ideal um Factory Klassen zu sparen und bei Unit Test die Klassen zu Mocken.
 *
 * Es werden alle Module verzeichnis nach der Datei services.yaml durchsucht und gelesen.
 * `source/modules/[*]/[*]/services.yaml`
 *
 * @see https://symfony.com/doc/3.1/service_container.html
 *
 * @return \Symfony\Component\DependencyInjection\Container
 */
function project_container()
{
    return \oxidprojects\DI\ContainerFactory::getInstance()->getContainer();
}
