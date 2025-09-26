<?php

namespace Modules\Payments\Repositories;

use App\Models\Transaksi;
use Modules\Payments\Repositories\Contracts\TransaksiRepositoryInterface;

class TransaksiRepository implements TransaksiRepositoryInterface
{
    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }
}
