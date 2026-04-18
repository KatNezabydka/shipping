<?php

declare(strict_types=1);

namespace App\HttpClient;

use App\DTO\Request\UpsRegisterShippingRequest;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

readonly class UpsHttpClient extends BaseHttpClient
{
    private const string SHIPPING_REGISTER_URL = 'https://upsfake.com/register';

    /**
     * @throws Throwable
     * @throws GuzzleException
     */
    public function registerShipping(UpsRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->serializer->normalize($request)
        );

        return true;
    }
}
