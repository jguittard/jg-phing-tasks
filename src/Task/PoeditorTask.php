<?php

namespace JG\Task;

use GuzzleHttp\Client;
use \Task;

/**
 * Class PoeditorTask
 *
 * @package     JG\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@me.com>
 */
class PoeditorTask extends Task
{
    const API_ENDPOINT = 'https://poeditor.com/api/';

    const DEFAULT_TYPE = 'po';

    /**
     * File format
     *
     * @var string
     */
    protected $type;

    /**
     * API token
     * @var string
     */
    protected $token;

    /**
     * Identifier of the project
     *
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * Language code
     *
     * @var string
     */
    protected $language;

    /**
     * Filters of the result
     *
     * @var string
     */
    protected $filters = '';

    /**
     * Tags for result filter
     *
     * @var string
     */
    protected $tags = '';

    /**
     * @var
     */
    protected $exportPath;

    /**
     * @var array
     */
    protected $types = [
        'po',
        'pot',
        'mo',
        'xls',
        'apple_strings',
        'xliff',
        'android_strings',
        'resx',
        'resw',
        'properties',
        'json',
    ];

    protected $languages = [
        'fr' => 'fr_FR',
        'en' => 'en_US',
        'es' => 'es_ES',
    ];

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type
     *
     * @param string $type
     * @return PoeditorTask
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the token
     *
     * @param string $token
     * @return PoeditorTask
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get the module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the module
     *
     * @param string $module
     * @return PoeditorTask
     */
    public function setModule($module)
    {
        $parts = explode('-', $module);
        if (count($parts) > 1) {
            $this->moduleName = ucfirst($parts[0]);
            $this->id = $parts[1];
        }
        $this->module = $module;
        return $this;
    }

    /**
     * Get the language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the language
     *
     * @param string $language
     * @return PoeditorTask
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get the filters
     *
     * @return string
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set the filters
     *
     * @param string $filters
     * @return PoeditorTask
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Get the tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the tags
     *
     * @param string $tags
     * @return PoeditorTask
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get the exportPath
     *
     * @return mixed
     */
    public function getExportPath()
    {
        return $this->exportPath;
    }

    /**
     * Set the exportPath
     *
     * @param mixed $exportPath
     * @return PoeditorTask
     */
    public function setExportPath($exportPath)
    {
        $this->exportPath = $exportPath;
        return $this;
    }

    /**
     *  Called by the project to let the task do it's work. This method may be
     *  called more than once, if the task is invoked more than once. For
     *  example, if target1 and target2 both depend on target3, then running
     *  <em>phing target1 target2</em> will run all tasks in target3 twice.
     *
     *  Should throw a BuildException if someting goes wrong with the build
     *
     *  This is here. Must be overloaded by real tasks.
     */
    public function main()
    {
        $client = new Client();
        $response = $client->request('POST', self::API_ENDPOINT, [
            'form_params' => $this->getPostParams()
        ]);
        $json = json_decode($response->getBody(), true);
        $file = $json['item'];

        echo '[Module ' . ucfirst($this->moduleName) . ']' . 'Downloading ' . strtoupper($this->getLanguage()) . ' file from ' . $file;

        $client->request('GET', $file, ['sink' => $this->retrieveFileName()]);
    }

    protected function retrieveFileName()
    {
        return $this->getExportPath() . DIRECTORY_SEPARATOR . ucfirst($this->moduleName) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->languages[$this->getLanguage()] . '.' . $this->getType();
    }

    /**
     * @return array
     */
    protected function getPostParams()
    {
        return [
            'api_token' => $this->getToken(),
            'action' => 'export',
            'id' => $this->id,
            'type' => $this->getType() ?: self::DEFAULT_TYPE,
            'language' => $this->getLanguage(),
        ];
    }
}