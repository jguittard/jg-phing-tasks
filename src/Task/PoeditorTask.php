<?php

namespace JG\Task;

use Guzzle\Http\Client;
use \Task;

/**
 * Class PoeditorTask
 *
 * @package     JG\Task
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@mme.com>
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
    protected $project;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $module;

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
     * @var Client
     */
    protected $apiClient;

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
     * Get the id
     *
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set the id
     *
     * @param string $project
     * @return PoeditorTask
     */
    public function setProject($project)
    {
        $parts = explode('-', $project);
        if (count($parts) > 1) {
            $this->module = ucfirst($parts[0]);
            $this->id = $parts[1];
        }
        $this->project = $project;
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
        $request = $client->post(self::API_ENDPOINT, null, $this->getPostParams());
        $response = $request->send();
        if ($response->getStatusCode() != 200) {
            throw new \BuildException('Unable to retrieve translation file');
        }
        $json = $response->json();

        $item = $json['item'];

        $handle = fopen($this->retrieveFileName(), 'w');
        $client->get($item, null, [Client::CURL_OPTIONS => ['CURLOPT_FILE' => $handle]])->send();
        fclose($handle);
    }

    protected function retrieveFileName()
    {
        return $this->getExportPath() . DIRECTORY_SEPARATOR . ucfirst($this->project) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->languages[$this->getLanguage()] . '.' . $this->getType();
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