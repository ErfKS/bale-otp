# Getting Started
## install package
run this command:
```shell
composer erfankatebsaber/bale-otp
```

## define provider
Add this line in `config/app.php`:
```php
return [
    // other props...
    'providers' => ServiceProvider::defaultProviders()->merge([
        // other providers...
        ErfanKatebSaber\BaleOtp\BaleOtpProvider::class
    ])->toArray(),
];
```

## Publish files (optional)
Run this command to publish config files:
```shell
php artisan vendor:publish --provider="ErfanKatebSaber\BaleOtp\BaleOtpProvider"
```

## ENV
Define this:
```dotenv
BALE_OTP_USERNAME=[YOUR USERNAME]
BALE_OTP_PASSWORD=[YOUR PASSWORD]

```

## Send Otp
Use this code:
```php
use \ErfanKatebSaber\BaleOtp\BaleOtp;

BaleOtp::setUp()->sendOtp(
    "989123456789", // phone number with international prefix
    '12345' // otp code
)
```
