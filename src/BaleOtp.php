<?php

namespace ErfanKatebSaber\BaleOtp;

use ErfanKatebSaber\BaleOtp\Exceptions\CommonException;
use ErfanKatebSaber\BaleOtp\Exceptions\InvalidClientException;
use ErfanKatebSaber\BaleOtp\Exceptions\InvalidPhoneException;
use ErfanKatebSaber\BaleOtp\Exceptions\NotExistUserException;
use ErfanKatebSaber\BaleOtp\Exceptions\ParseRequestException;
use ErfanKatebSaber\BaleOtp\Exceptions\PaymentRequiredException;
use ErfanKatebSaber\BaleOtp\Exceptions\RateLimitException;
use ErfanKatebSaber\BaleOtp\Exceptions\ServerException;
use ErfanKatebSaber\BaleOtp\Request\GetTokenRequest;
use ErfanKatebSaber\BaleOtp\Request\SendOtpRequest;
use ErfanKatebSaber\BaleOtp\Response\GetTokenResponse;
use ErfanKatebSaber\BaleOtp\Response\SendOtpResponse;
use Exception;
use Illuminate\Support\Facades\App;

class BaleOtp
{
    /**
     * @var array{username:string,password:string,url:string}
     */
    protected array $config;

    /**
     * @param array{username:string,password:string,url:string} $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Retrieve an access token from the Bale OTP service.
     *
     * This method authenticates with the service using the configured username, password, and URL,
     * and returns a GetTokenResponse object containing the access token and related information.
     * By default, the token is cached if available.
     *
     * @param bool $useCache Whether to use the cached token if available. Default is true.
     * @return GetTokenResponse The response containing the access token.
     *
     * @throws CommonException If a general error occurs during the request.
     * @throws InvalidClientException If the client credentials are invalid.
     * @throws ParseRequestException If the request cannot be parsed.
     * @throws ServerException If a server error occurs.
     */
    public function getToken(bool $useCache = true): GetTokenResponse
    {
        return (new GetTokenRequest($this->config['username'],$this->config['password'],$this->config['url']))->Send($useCache);
    }

    /**
     * Send an OTP code to a specified phone number via the Bale OTP service.
     *
     * This method normalizes the phone number, retrieves an access token if not provided,
     * and sends the OTP code to the user. It returns a SendOtpResponse object containing the result.
     *
     * @param string $phone The recipient's phone number (in any format).
     * @param string $otp The OTP code to send.
     * @param string|null $token Optional. Access token to use for the request. If null, a new token will be fetched.
     * @return SendOtpResponse The response indicating the result of the OTP sending operation.
     *
     * @throws CommonException If a general error occurs during the request.
     * @throws InvalidClientException If the client credentials are invalid.
     * @throws ParseRequestException If the request cannot be parsed.
     * @throws ServerException If a server error occurs.
     * @throws InvalidPhoneException If the phone number is invalid.
     * @throws RateLimitException If the request rate limit is exceeded.
     * @throws PaymentRequiredException If payment is required for the operation.
     * @throws NotExistUserException If the user does not exist in the system.
     * @throws Exception Other errors
     */
    public function sendOtp($phone, $otp, $token=null): SendOtpResponse
    {
        if(is_null($token)){
            $token = $this->getToken()->access_token;
        }
        $phone = $this->normalizePhoneNumber($phone);
        return (new SendOtpRequest($token,$this->config['url']))->Send($phone,$otp);
    }

    /**
     * Normalize a phone number to international format:
     *  - Removes any non-digit characters (spaces, plus signs, dashes, etc.)
     *  - If the number starts with a leading zero, replaces it with the country code "98"
     *
     * @param string $input The raw phone number input.
     * @return string The normalized phone number.
     */
    private function normalizePhoneNumber(string $input): string
    {
        // Remove any non-digit characters
        $digits = preg_replace('/\D+/', '', $input);

        // If it starts with a "0", replace that leading zero with "98"
        if (isset($digits[0]) && $digits[0] === '0') {
            $digits = '98' . substr($digits, 1);
        }

        return $digits;
    }

    public static function setUp(){
        return App::make(static::class);
    }
}
