<?php

namespace JGTests\Task;
use JG\Task\SassTask;

/**
 * Class SaasTaskTest
 *
 * @package     JGTest\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@mme.com>
 */
class SaasTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SassTask
     */
    protected $task;

    protected function setUp()
    {
        $this->task = new SassTask();
    }

    public function testForceSetter()
    {
        $this->assertNotContains('--force', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setForce(true));
        $this->assertContains('--force', $this->task->getParams());
    }

    public function testCheckSetter()
    {
        $this->assertNotContains('--check', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setCheck(true));
        $this->assertContains('--check', $this->task->getParams());
    }

    public function testNoCacheSetter()
    {
        $this->assertNotContains('--no-cache', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setNoCache(true));
        $this->assertContains('--no-cache', $this->task->getParams());
    }

    public function testEncodingSetter()
    {
        $encoding = 'UTF-8';
        $this->assertNotContains('-E', $this->task->getParams());
        $this->assertNotContains($encoding, $this->task->getParams());
        $this->assertSame($this->task, $this->task->setEncoding($encoding));
        $this->assertContains('-E', $this->task->getParams());
        $this->assertContains($encoding, $this->task->getParams());
    }

    public function testCompressSetter()
    {
        $this->assertNotContains('--style compressed', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setCompress(true));
        $this->assertContains('--style compressed', $this->task->getParams());
    }

    public function testCompactSetter()
    {
        $this->assertNotContains('--style compact', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setCompact(true));
        $this->assertContains('--style compact', $this->task->getParams());
    }

    public function testExpandSetter()
    {
        $this->assertNotContains('--style expanded', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setExpand(true));
        $this->assertContains('--style expanded', $this->task->getParams());
    }

    public function testPathSetter()
    {
        $this->assertNotContains('--load-path', $this->task->getParams());
        $this->assertSame($this->task, $this->task->setPath(true));
        $this->assertContains('--load-path', $this->task->getParams());
    }

    public function testInputEncapsulation()
    {
        $this->assertFalse($this->task->getInput());
        $this->assertSame($this->task, $this->task->setInput('input'));
        $this->assertSame('input', $this->task->getInput());
    }

    public function testOutputEncapsulation()
    {
        $this->assertFalse($this->task->getOutput());
        $this->assertSame($this->task, $this->task->setOutput('output'));
        $this->assertSame('output', $this->task->getOutput());
    }

    public function testMainWillThrowExceptionIfNoFilesDefined()
    {
        $this->setExpectedException('BuildException');
        $this->task->main();
    }
}
