<?php

namespace Modules\Payments\ValueObjects;

class PriceDataDinetkan
{
    public function __construct(
        public int $price,
        public ?int $ppnPercentage = 0,
        public ?int $discountPercentage = 0,
        public ?int $adminFee = 0,
        public ?int $ppn = 0,
        public ?int $discount = 0,
        public ?int $total = 0,
        public ?int $discountCoupon = 0,
        public ?int $price_otc = 0,
        public ?int $ppnPercentage_otc = 0,
        public ?int $ppn_otc = 0,
        public ?int $total_otc = 0,

    ) {
        $this->discount = ($this->price * $this->discountPercentage) / 100;
        $this->ppn = (($this->price - $this->discount - $this->discountCoupon) * $this->ppnPercentage) / 100;
        $this->ppn_otc = ($this->price_otc * $this->ppnPercentage_otc) / 100;
        $this->total = $this->price - $this->discount - $this->discountCoupon + $this->ppn  + $this->adminFee;
        $this->total_otc = $this->price_otc + $this->ppn_otc;
        $this->total = $this->total + $this->total_otc;
        $this->total = $this->roundTotal();
    }

    public function roundTotal(): int
    {
        return ceil($this->total / 100) * 100;
    }

    public function toArray(): array
    {
        return [
            'price' => $this->price,
            'discount' => $this->discount,
            'ppn' => $this->ppn,
            'admin_fee' => $this->adminFee,
            'total' => $this->total,
        ];
    }
}
