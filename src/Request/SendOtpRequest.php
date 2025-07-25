<?php

namespace ErfanKatebSaber\BaleOtp\Request;

use ErfanKatebSaber\BaleOtp\Exceptions\CommonException;
use ErfanKatebSaber\BaleOtp\Exceptions\InvalidPhoneException;
use ErfanKatebSaber\BaleOtp\Exceptions\NotExistUserException;
use ErfanKatebSaber\BaleOtp\Exceptions\PaymentRequiredException;
use ErfanKatebSaber\BaleOtp\Exceptions\RateLimitException;
use ErfanKatebSaber\BaleOtp\Exceptions\ServerException;
use ErfanKatebSaber\BaleOtp\Response\SendOtpResponse;
use Exception;
use Illuminate\Support\Facades\Http;

class SendOtpRequest extends BaseRequest
{
    protected string $urlPath = 'send_otp';
    public function __construct(protected string $token,protected string $baseUrl)
    {
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->urlPath = ltrim($this->urlPath, '/');
    }

    /**
     * @param $phone
     * @param int $otp
     * @return SendOtpResponse
     * @throws CommonException
     * @throws InvalidPhoneException
     * @throws NotExistUserException
     * @throws PaymentRequiredException
     * @throws RateLimitException
     * @throws ServerException
     * @throws Exception
     */
    public function Send($phone,$otp): SendOtpResponse
    {
        $url = $this->makeUrl();

        $response = Http::withToken($this->token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->send('POST', $url, [
                'body' => json_encode([
                    'phone' => $phone,
                    'otp' => Intval($otp),
                ]),
            ]);

        $data = $response->json();

        if(array_key_exists('message', $data)){
            if($data['type'] == 2 && $data['code'] == 8) {
                throw InvalidPhoneException::Factory($data['type'],$data['code'],$data['message']);
            } else if($data['type'] == 3 && $data['code'] == 17) {
                throw NotExistUserException::Factory($data['type'],$data['code'],$data['message']);
            } else if($data['type'] == 2 && $data['code'] == 20) {
                throw PaymentRequiredException::Factory($data['type'],$data['code'],$data['message']);
            } else if($data['type'] == 1 && $data['code'] == 2) {
                throw ServerException::Factory($data['type'],$data['code'],$data['message']);
            } else if($data['type'] == 2 && $data['code'] == 18) {
                throw RateLimitException::Factory($data['type'],$data['code'],$data['message']);
            } else {
                throw CommonException::Factory($data['type'],$data['code'],$data['message']);
            }
        }
        if(array_key_exists('error', $data)){
            throw new Exception($data['error']);
        }

        return new SendOtpResponse($response->json());
    }
}
