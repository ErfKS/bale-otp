<?php

namespace ErfanKatebSaber\BaleOtp\Request;

use ErfanKatebSaber\BaleOtp\Exceptions\CommonException;
use ErfanKatebSaber\BaleOtp\Exceptions\InvalidClientException;
use ErfanKatebSaber\BaleOtp\Exceptions\ParseRequestException;
use ErfanKatebSaber\BaleOtp\Exceptions\ServerException;
use ErfanKatebSaber\BaleOtp\Response\GetTokenResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GetTokenRequest extends BaseRequest
{
    protected string $urlPath = 'auth/token';

    public function __construct(protected string $username, protected string $password, protected string $baseUrl)
    {
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->urlPath = ltrim($this->urlPath, '/');
    }

    /**
     * @param bool $useCache save & load cache
     *
     * @return GetTokenResponse
     * @throws CommonException
     * @throws InvalidClientException
     * @throws ParseRequestException
     * @throws ServerException
     */
    public function Send(bool $useCache = true): GetTokenResponse
    {
        // اگر کش فعال است و توکن در کش موجود است
        if ($useCache) {
            $cachedToken = Cache::get('bale_otp_token');

            // اگر توکن کش موجود است و هنوز منقضی نشده است
            if ($cachedToken && $cachedToken['expires_at'] > now()->timestamp) {
                return new GetTokenResponse($cachedToken['data']);
            }
        }

        // در غیر این صورت یا کش وجود ندارد یا منقضی شده است، درخواست ارسال می‌شود
        $url = $this->makeUrl();
        $response = Http::asForm()
            ->post($url, [
                'grant_type' => 'client_credentials',
                'client_secret' => $this->password,
                'scope' => 'read',
                'client_id' => $this->username,
            ]);

        $data = $response->json();

        if (count($data) == 0) {
            throw new ParseRequestException($response->body());
        }

        if (array_key_exists('error', $data)) {
            if ($data['error'] == 'invalid_client') {
                throw new InvalidClientException($data);
            }
        }

        if (array_key_exists('message', $data)) {
            if ($data['type'] == 1 && $data['code'] == 2) {
                throw new ServerException($data);
            } else {
                throw new CommonException($data);
            }
        }

        // اگر کش فعال باشد، توکن و زمان انقضا را در کش ذخیره می‌کنیم
        if ($useCache) {
            Cache::put('bale_otp_token', [
                'data' => $data,
                'expires_at' => now()->addSeconds($data['expires_in'])->timestamp,
            ], $data['expires_in']); // ذخیره توکن تا زمان انقضا
        }

        return new GetTokenResponse($data);
    }
}
