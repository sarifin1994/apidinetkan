<?php

namespace Modules\Payments\ValueObjects;

class TripayConfig
{
    public function __construct(
        protected string $apiKey,
        protected string $merchantCode,
        protected string $privateKey,
        protected bool $isProduction = false,
        protected int $adminFee = 6500,
    ) {
        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('API Key is required');
        }

        if (empty($this->merchantCode)) {
            throw new \InvalidArgumentException('Merchant Code is required');
        }

        if (empty($this->privateKey)) {
            throw new \InvalidArgumentException('Private Key is required');
        }
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    public function getAdminFee(): int
    {
        return $this->adminFee;
    }
}
