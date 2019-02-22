<?php
/**
 * Created by dependency-injection.
 * Autor: Tobias Matthaiou
 * Date: 22.02.19
 * Time: 16:48
 */

namespace oxidprojects\DI\Tests;

/**
 * Class GlobalFunctionTest
 * @group active
 */
class GlobalFunctionTest extends \PHPUnit\Framework\TestCase
{

    public function testExsitsGlobalFunction()
    {
        //Assert
        $this->assertTrue(function_exists('project_container'));
    }

    public function testProjectContainerGiveSameInstance()
    {
        //Act
        $containerA = project_container();
        $containerB = project_container();

        //Assert
        $this->assertSame($containerA, $containerB);
    }
}
