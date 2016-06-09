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
    const TYPE_INI = 'ini';
    const TYPE_JSON = 'json';

    protected $availableTypes = ['ini', 'json'];

    /**
     * @var string
     */
    protected $fromFile;

    /**
     * @var string
     */
    protected $type;

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
     * Set the type
     *
     * @param string $type
     * @return LoadConfigTask
     */
    public function setType($type)
    {
        if (!in_array($type, $this->availableTypes)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid config file type', $type));
        }
        $this->type = strtolower($type);
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
        if ($this->wait) {
            for ($i=0; $i<10; $i++) {
                if (!file_exists($this->fromFile)) {
                    sleep($this->wait);
                    continue;
                }
                break;
            }
        }
        if (!file_exists($this->fromFile)) {
            throw new \Exception('File path must exist to load config');
        }

        if (false === ($config = $this->loadConfig())) {
            throw new \Exception(sprintf('Invalid %s type content', strtoupper($this->type)));
        }
        foreach ($config as $key => $value) {
            $this->project->setNewProperty($this->toProperty .$key, $value);
        }
    }

    /**
     * @return array|bool|mixed
     */
    protected function loadConfig()
    {
        $config = false;
        switch ($this->type) {
            case self::TYPE_INI:
                $config = parse_ini_file($this->fromFile, null, 2);
                break;
            case self::TYPE_JSON:
                $content = file_get_contents($this->fromFile);
                $config = json_decode($content, true);
                break;
        }
        return $config;
    }
}