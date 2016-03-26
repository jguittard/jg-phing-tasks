<?php

namespace JGTests\Task;

use JG\Task\LoadConfigTask;

/**
 * Class LoadConfigTaskTest
 *
 * @package     JGTest\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@me.com>
 */
class LoadConfigTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoadConfigTask
     */
    protected $task;
    /**
     * Setup
     *
     * @return void
     */
    protected function setUp()
    {
        $this->task = new LoadConfigTask();
    }
    /**
     * Test the fromFile Setter
     *
     * @return void
     */
    public function testFromFileSetter()
    {
        $fromFile = '/usr/null';
        $this->assertSame($this->task, $this->task->setFromFile($fromFile));
        $this->assertAttributeSame($fromFile, 'fromFile', $this->task);
    }
    /**
     * Test the toProperty Setter
     *
     * @return void
     */
    public function testToPropertySetter()
    {
        $toProperty = 'myConfig';
        $this->assertSame($this->task, $this->task->setToProperty($toProperty));
        $this->assertAttributeSame($toProperty, 'toProperty', $this->task);
    }
    /**
     * Test the wait Setter
     *
     * @return void
     */
    public function waitPropertySetter()
    {
        $wait = 5;
        $this->assertSame($this->task, $this->task->setWait($wait));
        $this->assertAttributeSame($wait, 'wait', $this->task);
    }
}