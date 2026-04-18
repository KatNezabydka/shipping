<?php

declare(strict_types=1);

namespace App\HttpClient;

use App\DTO\Request\DhlRegisterShippingRequest;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

readonly class DhlHttpClient extends BaseHttpClient
{
    private const string SHIPPING_REGISTER_URL = 'https://dhlfake.com/register';

    /**
     * @throws Throwable
     * @throws GuzzleException
     */
    public function registerShipping(DhlRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->serializer->normalize($request)
        );

        return true;
    }
}
