<?php

namespace Modules\Payments\ValueObjects;

class PaymentMethod
{
    public function __construct(
        public string $code,
        public string $name,
        public int $fee,
    ) {}
}
