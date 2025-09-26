<?php

namespace Modules\Payments\ValueObjects;

class MidtransConfig
{
    public function __construct(
        protected string $idMerchant,
        protected string $clientKey,
        protected string $serverKey,
        protected bool $isProduction = false,
        protected int $adminFee = 0
    ) {
        if (empty($this->idMerchant)) {
            throw new \InvalidArgumentException('ID Merchant is required');
        }

        if (empty($this->clientKey)) {
            throw new \InvalidArgumentException('Client Key is required');
        }

        if (empty($this->serverKey)) {
            throw new \InvalidArgumentException('Server Key is required');
        }
    }

    public function getIdMerchant(): string
    {
        return $this->idMerchant;
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getServerKey(): string
    {
        return $this->serverKey;
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
