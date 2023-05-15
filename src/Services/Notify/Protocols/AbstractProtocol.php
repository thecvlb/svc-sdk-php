<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols;

use CVLB\Svc\Api\Services\Notify\Notify;

abstract class AbstractProtocol
{
    /**
     * @var string
     */
    protected string $api_uri;

    /**
     * @var string[] 
     */
    protected array $headers = [
        'Content-Type' => 'application/json',
        'svc-notify-requesting-service' => 'Unknown App (set name)'
    ];

    /**
     * @var Notify
     */
    protected Notify $notify;
    
    /**
     * Name of protocol, e.g. "chat"
     * This is used in the API path and must correspond
     * @var string
     */
    protected string $protocol;

    /**
     * Name of service, e.g. "slack"
     * This is used in the API path and must correspond
     * @var string
     */
    protected string $service;

    /**
     * Turn on verbosity in the logs
     * @var bool
     */
    protected bool $verbose = false;

    /**
     * @return void
     */
    public function setUri()
    {
        $this->api_uri = sprintf("%s/%s/%s", $this->notify::$base_uri, $this->protocol, $this->service);
    }

    /**
     * Get the API uri
     * @return string
     */
    public function getUri(): string
    {
        return $this->api_uri;
    }

    /**
     * Set api version
     * @param string $value
     * @return void
     */
    public function setVersion(string $value)
    {
        $this->version = $value;
        $this->setUri();
        $this->setHeader('svc-notify-requesting-service', $this->notify->sdk->app_name);
    }

    /**
     * Get the current api version
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set a specific header
     * @param string $key
     * @param string $value
     * @return string[]
     */
    public function setHeader(string $key, string $value): array
    {
        $this->headers[$key] = $value;
        
        return $this->headers;
    }

    /**
     * Remove a specific header
     * @param string $key
     * @return string[]
     */
    public function unsetHeader(string $key): array
    {
        unset($this->headers[$key]);

        return $this->headers;
    }

    /**
     * Get the headers array
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set verbosity
     * @param bool $value
     * @return void
     */
    public function setVerbose(bool $value)
    {
        $this->verbose = $value;
    }

    /**
     * Get verbosity
     * @return bool
     */
    public function getVerbose()
    {
        return $this->verbose;
    }
}
