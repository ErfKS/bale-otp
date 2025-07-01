<?php

namespace ErfanKatebSaber\BaleOtp\Response;

class GetTokenResponse extends BaseResponse
{
    public string $access_token;
    public int $expires_in;
    public string $scope;
    public string $token_type;
    /**
     * @param array{access_token:string,expires_in:int,scope:string,token_type:string} $data
     * @return static
     */
    public function Factory(array $data)
    {
        $this->access_token = $data['access_token'];
        $this->expires_in = $data['expires_in'];
        $this->scope = $data['scope'];
        $this->token_type = $data['token_type'];

        return parent::Factory($data);
    }
}
