<?php

namespace JG\Task;

use \Task;
use \BuildException;

/**
 * Class SassTask
 *
 * @package     JG\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@mme.com>
 */
class SassTask extends Task
{
    /**
     * A list of parameters for SASS
     *
     * @var array
     */
    protected $params = [];

    /**
     * The input file
     *
     * @var bool|string
     */
    protected $inFile = false;

    /**
     * The output file
     *
     * @var bool|string
     */
    protected $outFile = false;

    /**
     * Get the params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get the inFile
     *
     * @return bool|string
     */
    public function getInput()
    {
        return $this->inFile;
    }

    /**
     * Get the outFile
     *
     * @return bool|string
     */
    public function getOutput()
    {
        return $this->outFile;
    }

    /**
     * True to recompile all Sass files, even if the CSS file is newer.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setForce($value)
    {
        if ($value === true) {
            $this->params[] = '--force';
        }
        return $this;
    }

    /**
     * True to just check syntax, don't evaluate.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setCheck($value)
    {
        if($value === true) {
            $this->params[] = "--check";
        }
        return $this;
    }

    /**
     * True to disable caching of sassc files.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setNoCache($value)
    {
        if($value === true) {
            $this->params[] = "--no-cache";
        }
        return $this;
    }

    /**
     * Specify the default encoding for Sass files.
     *
     * @param string $encoding
     * @return SassTask
     */
    public function setEncoding($encoding)
    {
        $this->params[] = "-E";
        $this->params[] = $encoding;
        return $this;
    }

    /**
     * True to compress the output.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setCompress($value)
    {
        if ($value === true) {
            $this->params[] = '--style compressed';
        }
        return $this;
    }

    /**
     * True to compact the output.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setCompact($value)
    {
        if ($value === true) {
            $this->params[] = '--style compact';
        }
        return $this;
    }

    /**
     * True to expand the output.
     *
     * @param bool $value
     * @return SassTask
     */
    public function setExpand($value)
    {
        if ($value === true) {
            $this->params[] = '--style expanded';
        }
        return $this;
    }

    /**
     * Adds a sass import path.
     *
     * @param string $path
     * @return SassTask
     */
    public function setPath($path)
    {
        $this->params[] = '--load-path';
        $this->params[] = $path;
        return $this;
    }

    /**
     * Set the input file
     *
     * @param bool|string $filename
     * @return SassTask
     */
    public function setInput($filename)
    {
        $this->inFile = $filename;
        return $this;
    }

    /**
     * Set the output file
     *
     * @param bool|string $filename
     * @return SassTask
     */
    public function setOutput($filename)
    {
        $this->outFile = $filename;
        return $this;
    }

    /**
     * Called by the project to let the task do it's work. This method may be
     * called more than once, if the task is invoked more than once. For
     * example, if target1 and target2 both depend on target3, then running
     * <em>phing target1 target2</em> will run all tasks in target3 twice.
     *
     * Should throw a BuildException if something goes wrong with the build
     *
     * This is here. Must be overloaded by real tasks.
     *
     * @throws BuildException
     */
    public function main()
    {
        if ($this->inFile === false || $this->outFile === false) {
            throw new BuildException("You must specify an input and output file.", $this->location);
        }
        $options = implode(" ", $this->params);
        $this->shell("sass $options {$this->inFile} {$this->outFile}");
    }

    /**
     * Executes a command in the OS
     *
     * @param string $command
     * @return string
     * @throws BuildException
     */
    public function shell($command)
    {
        $command = "$command 2>&1";
        $this->log("Executing: $command");
        exec($command, $output, $code);
        $this->log("RETURN: $code");
        foreach ($output as $line) {
            $this->log($line);
        }
        if ((int)$code !== 0) {
            throw new BuildException("Execution of a shell command returned a non-zero result.", $this->location);
        }
        return $output;
    }
}
