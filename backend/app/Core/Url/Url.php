<?php

namespace App\Core\Url;

use Slim\Psr7\Factory\UriFactory;

class Url
{
    protected $uri;
    protected $fallbackBasePath;

    public function __construct()
    {
        $this->uri = factory(UriFactory::class)->createFromGlobals($_SERVER);
    }

    public function setFallbackBasePath($path)
    {
        $this->fallbackBasePath = $path;

        return $this;
    }

    public function getUriInstance()
    {
        return $this->uri;
    }

    public function baseUrl()
    {
        if (! empty($this->getHost())) {
            return $this->getScheme().'://'.$this->getHost();
        }

        if (! empty($this->fallbackBasePath)) {
            return $this->fallbackBasePath;
        }

        return '';
    }

    public function __call($method, $arguments)
    {
        return $this->uri->{$method}($arguments);
    }

    public function __toString()
    {
        return (string) $this->uri;
    }
}
