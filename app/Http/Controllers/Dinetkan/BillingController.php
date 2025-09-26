<?php

namespace App\Http\Controllers\Dinetkan;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\DataTables\Owner\BillingDataTable;

class BillingController extends Controller
{
    public function index(BillingDataTable $dataTable)
    {
        return $dataTable->render('owner.billing');
    }

    public function renew(User $user)
    {
        $next_due = Carbon::createFromFormat('Y-m-d', $user->next_due)
            ->addMonthsWithNoOverflow(1)
            ->toDateString();

        $user->update([
            'status' => 1,
            'next_due' => $next_due,
        ]);

        return redirect()->back();
    }
}
