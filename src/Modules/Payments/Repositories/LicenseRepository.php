<?php

namespace Modules\Payments\Repositories;

use App\Models\License;
use Modules\Payments\Repositories\Contracts\LicenseRepositoryInterface;

class LicenseRepository implements LicenseRepositoryInterface
{
    public function findById(int $id): ?License
    {
        return License::find($id);
    }

    public function all(): iterable
    {
        return License::all();
    }
}
