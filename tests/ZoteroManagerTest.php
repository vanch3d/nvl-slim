<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 13/01/2018
 * Time: 22:20
 */


use PHPUnit\Framework\TestCase;

class ZoteroManagerTest extends TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testGetData()
    {
        $user = new ZoteroManager(null);
        $this->invokeMethod($user, 'getZoteroCacheFilename', array('passwordToCrypt'));
    }

    public function testIsDefined()
    {

    }

    public function testGetName()
    {

    }

}
