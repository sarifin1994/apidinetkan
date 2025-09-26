<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\Transaksi;

interface TransaksiRepositoryInterface
{
    public function create(array $data): Transaksi;
}
