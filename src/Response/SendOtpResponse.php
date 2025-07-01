<?php

namespace ErfanKatebSaber\BaleOtp\Response;

class SendOtpResponse extends BaseResponse
{
    public string $balance;
    /**
     * @param array{balance:int} $data
     * @return static
     */
    public function Factory(array $data)
    {
        $this->balance = $data['balance'];

        return parent::Factory($data);
    }
}
