<?php

namespace App\Http\Controllers\Dinetkan;

use App\Enums\InvoiceStatusEnum;
use App\Models\User;
use App\Models\PppoeUser;
use App\Models\Transaksi;
use App\Models\HotspotUser;
use App\Models\UserSession;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnum;
use Illuminate\Support\Carbon;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\DB;
use Matriphe\Larinfo\LarinfoFacade;
use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $larinfo = LarinfoFacade::getinfo();

        $totalUsers = User::where('role', 'Admin')->count();
        $newUsers = User::where('role', 'Admin')->where('created_at', '>=', now()->subDays(7))->count();
        $activeUsers = User::where('role', 'Admin')->where('status', UserStatusEnum::ACTIVE)->count();
        $overdueUsers = User::where('role', 'Admin')->where('status', UserStatusEnum::OVERDUE)->count();

        $recentOnlineUsers = UserSession::whereNotNull('user_id')
            ->whereHas('user', function ($query) {
                $query->where('role', 'Admin');
            })
            ->where('last_activity', '>=', now()->subMinutes(10)->timestamp)
            ->count();

        $recentAdmins = User::where('role', 'Admin')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentRenewedAdmins = $this->recentRenewedAdminsTable();
        $topAdmins = $this->topAdminsTable();

        return view('dinetkan.dashboard', [
            'larinfo' => $larinfo,
            'totalUsers' => $totalUsers,
            'newUsers' => $newUsers,
            'activeUsers' => $activeUsers,
            'overdueUsers' => $overdueUsers,
            'recentOnlineUsers' => $recentOnlineUsers,
            'recentAdmins' => $recentAdmins,
            'recentRenewedAdmins' => $recentRenewedAdmins,
            'topAdmins' => $topAdmins,
        ]);
    }

    public function monthlyRevenueChart()
    {
        $year = date('Y');

        $data = collect(range(1, 12))->map(function ($month) use ($year) {
            $income = Transaksi::where('invoice_type', AdminInvoice::class)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('price');

            return [
                'month' => Carbon::create()->month($month)->format('F'),
                'income' => $income,
            ];
        });

        return response()->json([
            'categories' => $data->pluck('month'),
            'series' => [
                ['name' => 'Revenue', 'data' => $data->pluck('income')],
            ],
            'tooltip' => [
                'type' => 'Rp',
            ],
        ]);
    }

    public function dailyRevenueChart()
    {
        $data = collect(range(6, 0))->map(function ($day) {
            $income = Transaksi::where('invoice_type', AdminInvoice::class)
                ->whereDate('tanggal', now()->subDays($day))
                ->sum('price');

            return [
                'day' => Carbon::now()->subDays($day)->format('d M'),
                'income' => $income,
            ];
        });

        return response()->json([
            'categories' => $data->pluck('day'),
            'series' => [
                ['name' => 'Revenue', 'data' => $data->pluck('income')],
            ]
        ]);
    }

    public function recentAdminsChart()
    {
        // get daily admin registration and group by date Y-m-d
        $data = User::where('role', 'Admin')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d M'),
                    'count' => $item->count,
                ];
            });

        return response()->json([
            'categories' => $data->pluck('date'),
            'series' => [
                ['name' => 'Admins', 'data' => $data->pluck('count')],
            ],
        ]);
    }

    public function recentRenewedAdminsTable()
    {
        $invoices = AdminInvoice::with('admin:id_group,name,shortname,email,username,status,next_due')
            ->where('status', InvoiceStatusEnum::PAID)
            ->orderByDesc('created_at')
            ->distinct('group_id')
            ->limit(10)
            ->select('group_id', 'paid_date', 'status', 'created_at')
            ->get();

        return $invoices;
    }

    public function topAdminsTable()
    {
        $admins = User::with([
            'pppoeUsers' => function ($query) {
                $query->select('group_id', DB::raw('COUNT(*) as pppoe_user_count'))
                    ->groupBy('group_id');
            },
            'hotspotUsers' => function ($query) {
                $query->select('group_id', DB::raw('COUNT(*) as hotspot_user_count'))
                    ->groupBy('group_id');
            }
        ])
            ->select('users.id', 'users.id_group', 'users.name', 'users.shortname', 'users.email', 'users.username', 'users.status')
            ->where('role', 'Admin')
            ->withCount([
                'pppoeUsers',
                'hotspotUsers'
            ])
            ->get()
            ->map(function ($admin) {
                $admin->total_users = $admin->pppoe_users_count + $admin->hotspot_users_count;
                return $admin;
            })
            ->sortByDesc('total_users')
            ->take(5);

        return $admins;
    }
}
