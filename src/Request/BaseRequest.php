<?php

namespace ErfanKatebSaber\BaleOtp\Request;

class BaseRequest
{
    protected string $urlPath = '/';

    public function makeUrl(): string
    {
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->urlPath = ltrim($this->urlPath, '/');

        return $this->baseUrl.'/'.$this->urlPath;
    }
}
