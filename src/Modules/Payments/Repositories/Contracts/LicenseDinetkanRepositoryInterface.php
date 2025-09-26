<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\LicenseDinetkan;

interface LicenseDinetkanRepositoryInterface
{
    public function findById(int $id): ?LicenseDinetkan;
    public function all(): iterable;
}
