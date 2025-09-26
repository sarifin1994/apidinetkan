<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\TicketGgn;
use App\Models\TicketPsb;
use App\Models\Transaksi;
use App\Models\HotspotUser;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use Illuminate\Support\Carbon;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Combine the HotspotUser queries into one.
         */
        $hotspotStats = HotspotUser::where('group_id', Auth::user()->id_group)
            ->selectRaw("
                COUNT(*) AS total,
                SUM(
                    CASE 
                        WHEN created_at >= NOW() - INTERVAL 7 DAY 
                        THEN 1 ELSE 0 
                    END
                ) AS current7,
                SUM(
                    CASE 
                        WHEN created_at >= NOW() - INTERVAL 14 DAY
                             AND created_at <= NOW() - INTERVAL 7 DAY
                        THEN 1 ELSE 0 
                    END
                ) AS previous7
            ")
            ->first();

        $hotspotUsersTotal = $hotspotStats->total;
        $currentHotspotUsers = $hotspotStats->current7;
        $previousHotspotUsers = $hotspotStats->previous7;
        $hotspotUsersDifference = $currentHotspotUsers - $previousHotspotUsers;
        $hotspotUsersPercentage = $previousHotspotUsers != 0
            ? round(($hotspotUsersDifference / $previousHotspotUsers) * 100, 1)
            : 0;

        /**
         * Combine the PPPoEUser queries into one.
         */
        $pppoeStats = PppoeUser::where('group_id', Auth::user()->id_group)
            ->selectRaw("
                COUNT(*) AS total,
                SUM(
                    CASE 
                        WHEN created_at >= NOW() - INTERVAL 7 DAY 
                        THEN 1 ELSE 0 
                    END
                ) AS current7,
                SUM(
                    CASE 
                        WHEN created_at >= NOW() - INTERVAL 14 DAY
                             AND created_at <= NOW() - INTERVAL 7 DAY
                        THEN 1 ELSE 0
                    END
                ) AS previous7
            ")
            ->first();

        $pppoeUsersTotal = $pppoeStats->total;
        $currentPppoeUsers = $pppoeStats->current7;
        $previousPppoeUsers = $pppoeStats->previous7;
        $pppoeUsersDifference = $currentPppoeUsers - $previousPppoeUsers;
        $pppoeUsersPercentage = $previousPppoeUsers != 0
            ? round(($pppoeUsersDifference / $previousPppoeUsers) * 100, 1)
            : 0;

        /**
         * Combine paid/unpaid invoice queries into one.
         */
        $invoicesStats = Invoice::where('group_id', Auth::user()->id_group)
            ->selectRaw("
                /* Paid Invoices */
                SUM(
                    CASE 
                        WHEN status = 1 
                             AND MONTH(due_date) = MONTH(CURDATE()) 
                        THEN 1 ELSE 0 
                    END
                ) AS currentPaid,
                SUM(
                    CASE 
                        WHEN status = 1 
                             AND MONTH(due_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                        THEN 1 ELSE 0 
                    END
                ) AS previousPaid,

                /* Unpaid Invoices */
                SUM(
                    CASE 
                        WHEN status = 0 
                        THEN 1 ELSE 0 
                    END
                ) AS totalUnpaid,
                SUM(
                    CASE 
                        WHEN status = 0 
                             AND MONTH(due_date) = MONTH(CURDATE()) 
                        THEN 1 ELSE 0 
                    END
                ) AS currentUnpaid,
                SUM(
                    CASE 
                        WHEN status = 0 
                             AND MONTH(due_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                        THEN 1 ELSE 0 
                    END
                ) AS previousUnpaid
            ")
            ->first();

        $paidInvoices = $invoicesStats->currentPaid;
        $currentPaidInvoices = $invoicesStats->currentPaid;
        $previousPaidInvoices = $invoicesStats->previousPaid;
        $paidInvoicesDifference = $currentPaidInvoices - $previousPaidInvoices;
        $paidInvoicesPercentage = $previousPaidInvoices != 0
            ? round(($paidInvoicesDifference / $previousPaidInvoices) * 100, 1)
            : 0;

        $unpaidInvoices = $invoicesStats->totalUnpaid;
        $currentUnpaidInvoices = $invoicesStats->currentUnpaid;
        $previousUnpaidInvoices = $invoicesStats->previousUnpaid;
        $unpaidInvoicesDifference = $currentUnpaidInvoices - $previousUnpaidInvoices;
        $unpaidInvoicesPercentage = $previousUnpaidInvoices != 0
            ? round(($unpaidInvoicesDifference / $previousUnpaidInvoices) * 100, 1)
            : 0;

        /**
         * Online Hotspot users
         */
        $onlineHotspotUsers = RadiusSession::query()
            ->where('shortname', $request->user()->shortname)
            ->where([['status', 1], ['type', 1]])
            ->count();

        /**
         * Online PPPoE users
         */
        $sub = RadiusSession::select('username', DB::raw('MAX(id) as latest_id'))
            ->where('shortname', $request->user()->shortname)
            ->where([['status', 1], ['type', 2]])
            ->groupBy('username');

        $onlinePppoeUsers = RadiusSession::joinSub($sub, 'latest_sessions', function ($join) {
                $join->on('user_session.id', '=', 'latest_sessions.latest_id');
            })
            ->count();

        /**
         * Offline PPPoE users (Updated to match the subquery + left join approach)
         */
        // 1) Subquery for each user’s most recent session
        $latestSessionSub = DB::table('db_radius.user_session as us')
            ->select('us.username', 'us.session_id', 'us.ip', 'us.status', 'us.stop', 'us.update')
            ->join(
                DB::raw('(SELECT username, MAX(`update`) AS max_update
                          FROM db_radius.user_session
                          GROUP BY username) AS x'),
                function ($join) {
                    $join->on('us.username', '=', 'x.username')
                         ->on('us.update', '=', 'x.max_update');
                }
            );

        // 2) Left join that subquery to user_pppoe, filtering for "offline" logic
        //    (status=2 and stop IS NOT NULL) OR (no session row at all).
        $offlinePppoeUsers = DB::table('db_radius.user_pppoe as p')
            ->leftJoinSub($latestSessionSub, 'ls', function ($join) {
                $join->on('p.username', '=', 'ls.username');
            })
            ->where('p.group_id', $request->user()->id_group)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('ls.status', 2)
                      ->whereNotNull('ls.stop');
                })
                ->orWhereNull('ls.username');
            })
            ->count();

        /**
         * Suspended Hotspot users
         */
        $suspendedHotspotUsers = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();

        /**
         * Suspended PPPoE users
         */
        $suspendedPppoeUsers = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();

        // Return data to the view as before
        return view('dashboards.admin', [
            'hotspotUsersTotal' => $hotspotUsersTotal,
            'pppoeUsersTotal' => $pppoeUsersTotal,
            'paidInvoices' => $paidInvoices,
            'unpaidInvoices' => $unpaidInvoices,
            'onlineHotspotUsers' => $onlineHotspotUsers,
            'onlinePppoeUsers' => $onlinePppoeUsers,
            'offlinePppoeUsers' => $offlinePppoeUsers,
            'suspendedHotspotUsers' => $suspendedHotspotUsers,
            'suspendedPppoeUsers' => $suspendedPppoeUsers,
            'hotspotUsersPercentage' => $hotspotUsersPercentage,
            'pppoeUsersPercentage' => $pppoeUsersPercentage,
            'paidInvoicesPercentage' => $paidInvoicesPercentage,
            'unpaidInvoicesPercentage' => $unpaidInvoicesPercentage,
        ]);
    }

    /**
     * The following methods remain the same; only the "offline PPPoE users" query above was updated.
     */
    public function revenueChart()
    {
        $year = date('Y');

        $data = collect(range(1, 12))->map(function ($month) use ($year) {
            $income = Transaksi::where('group_id', Auth::user()->id_group)
                ->where('type', TransactionTypeEnum::INCOME)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('price');

            $expense = Transaksi::where('group_id', Auth::user()->id_group)
                ->where('type', TransactionTypeEnum::EXPENSE)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('price');

            return [
                'month' => Carbon::create()->month($month)->format('M'),
                'income' => $income,
                'expense' => -$expense
            ];
        });

        return response()->json([
            'months' => $data->pluck('month'),
            'income' => $data->pluck('income'),
            'expense' => $data->pluck('expense'),
            'totalIncome' => $data->sum('income'),
            'totalExpense' => abs($data->sum('expense'))
        ]);
    }

    public function newIssuesChart()
    {
        $year = date('Y');

        $newInstallations = TicketPsb::where('group_id', Auth::user()->id_group)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function ($ticket) {
                return [
                    'x' => $ticket->created_at->format('Y-m-d'),
                    'y' => 1
                ];
            })
            ->groupBy('x')
            ->map(function ($group) {
                return [
                    'x' => $group->first()['x'],
                    'y' => $group->count()
                ];
            })
            ->values();

        $troubles = TicketGgn::where('group_id', Auth::user()->id_group)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function ($ticket) {
                return [
                    'x' => $ticket->created_at->format('Y-m-d'),
                    'y' => 1
                ];
            })
            ->groupBy('x')
            ->map(function ($group) {
                return [
                    'x' => $group->first()['x'],
                    'y' => $group->count()
                ];
            })
            ->values();

        return response()->json([
            'installations' => $newInstallations,
            'troubles' => $troubles
        ]);
    }

    public function usersGrowthChart()
    {
        $year = date('Y');

        $data = collect(range(1, 12))->map(function ($month) use ($year) {
            $hotspot = HotspotUser::where('group_id', Auth::user()->id_group)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $pppoe = PppoeUser::where('group_id', Auth::user()->id_group)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            return [
                'month' => Carbon::create()->month($month)->format('M'),
                'hotspot' => $hotspot,
                'pppoe' => $pppoe
            ];
        });

        return response()->json([
            'months' => $data->pluck('month'),
            'hotspot' => $data->pluck('hotspot'),
            'pppoe' => $data->pluck('pppoe'),
            'totalHotspot' => $data->sum('hotspot'),
            'totalPppoe' => $data->sum('pppoe')
        ]);
    }

    public function recentUsersTable()
    {
        $hotspotUsers = HotspotUser::where('group_id', Auth::user()->id_group)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->shortname,
                    'username' => $user->username,
                    'type' => 'Hotspot',
                    'joined' => $user->created_at->diffForHumans()
                ];
            });

        $pppoeUsers = PppoeUser::where('group_id', Auth::user()->id_group)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->member_name,
                    'username' => $user->username,
                    'type' => 'PPPoE',
                    'joined' => $user->created_at->diffForHumans()
                ];
            });

        $recentUsers = $hotspotUsers->concat($pppoeUsers)
            ->sortByDesc('joined')
            ->take(10)
            ->values();

        return response()->json(['users' => $recentUsers]);
    }
}
