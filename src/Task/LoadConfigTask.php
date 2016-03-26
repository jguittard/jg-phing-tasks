<?php

namespace JG\Task;

use \Task;

/**
 * Class LoadConfigTask
 *
 * @package     JG\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@me.com>
 */
class LoadConfigTask extends Task
{
    /**
     * @var string
     */
    protected $fromFile;

    /**
     * @var string
     */
    protected $toProperty;

    /**
     * @var int
     */
    protected $wait;

    /**
     * Set the fromFile
     *
     * @param string $fromFile
     * @return LoadConfigTask
     */
    public function setFromFile($fromFile)
    {
        $this->fromFile = $fromFile;
        return $this;
    }

    /**
     * Set the toProperty
     *
     * @param string $toProperty
     * @return LoadConfigTask
     */
    public function setToProperty($toProperty)
    {
        $this->toProperty = $toProperty;
        return $this;
    }

    /**
     * Set the wait
     *
     * @param int $wait
     * @return LoadConfigTask
     */
    public function setWait($wait)
    {
        $this->wait = (int)$wait;
        return $this;
    }

    /**
     * Task call
     *
     * @throws \Exception
     * @return void
     */
    public function main()
    {
        if (empty($this->fromFile)) {
            throw new \Exception('File path must be set to load config');
        }
        $wait = $this->wait ?: 5;
        for ($i=0; $i<10; $i++) {
            if (!file_exists($this->fromFile)) {
                sleep($wait);
                continue;
            }
            break;
        }
        if (!file_exists($this->fromFile)) {
            throw new \Exception('File path must exist to load config');
        }
        $content = file_get_contents($this->fromFile);
        $config = json_decode($content, true);
        if (!$config) {
            throw new \Exception('File content must be valid JSON');
        }
        foreach ($config as $key => $value) {
            $this->project->setNewProperty($this->toProperty .$key, $value);
        }
    }

}