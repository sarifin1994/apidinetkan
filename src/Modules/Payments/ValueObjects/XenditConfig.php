<?php

namespace Modules\Payments\ValueObjects;

class XenditConfig
{
    public function __construct(
        protected string $publicKey,
        protected string $secretKey,
        protected string $webhookVerificationKey,
        protected bool $isProduction = false,
        protected int $adminFee = 0
    ) {
        if (empty($this->publicKey)) {
            throw new \InvalidArgumentException('Public Key is required');
        }

        if (empty($this->secretKey)) {
            throw new \InvalidArgumentException('Secret Key is required');
        }

        if (empty($this->webhookVerificationKey)) {
            throw new \InvalidArgumentException('Webhook Verification Key is required');
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getWebhookVerificationKey(): string
    {
        return $this->webhookVerificationKey;
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
