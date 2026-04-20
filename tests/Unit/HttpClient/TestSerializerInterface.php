<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

interface TestSerializerInterface extends SerializerInterface, NormalizerInterface {}
