<?php

namespace ErfanKatebSaber\BaleOtp\Response;

class BaseResponse
{
    public array $original_data;
    public function __construct(array $data)
    {
        $this->original_data = $data;
        $this->Factory($this->original_data);
    }

    public function Factory(array $data)
    {
        return $this;
    }
}
