<?php

namespace Modules\Payments\ValueObjects;

class DuitkuConfig
{
    public function __construct(
        protected string $idMerchant,
        protected string $apiKey,
        protected bool $isProduction = false,
        protected int $adminFee = 0
    ) {
        if (empty($this->idMerchant)) {
            throw new \InvalidArgumentException('ID Merchant is required');
        }

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('API Key is required');
        }
    }

    public function getIdMerchant(): string
    {
        return $this->idMerchant;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
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
