<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\License;

interface LicenseRepositoryInterface
{
    public function findById(int $id): ?License;
    public function all(): iterable;
}
