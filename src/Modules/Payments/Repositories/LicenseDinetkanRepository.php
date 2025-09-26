<?php

namespace Modules\Payments\Repositories;

use App\Models\License;
use App\Models\LicenseDinetkan;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;

class LicenseDinetkanRepository implements LicenseDinetkanRepositoryInterface
{
    public function findById(int $id): ?LicenseDinetkan
    {
        return LicenseDinetkan::find($id);
    }

    public function all(): iterable
    {
        return LicenseDinetkan::all();
    }
}
