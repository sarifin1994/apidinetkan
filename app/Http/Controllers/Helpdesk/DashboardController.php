<?php

namespace App\Http\Controllers\Helpdesk;

use App\Models\TicketGgn;
use App\Models\TicketPsb;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\OutageTicketStatusEnum;
use App\Enums\NewClientTicketStatusEnum;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $newClientOpenTickets = TicketPsb::where('group_id', $request->user()->id_group)
            ->where('status', [NewClientTicketStatusEnum::PENDING, NewClientTicketStatusEnum::OPEN])
            ->count();
        $outageOpenTickets = TicketGgn::where('group_id', $request->user()->id_group)
            ->where('status', OutageTicketStatusEnum::OPEN)
            ->count();
        $closedNewClientTickets = TicketPsb::where('group_id', $request->user()->id_group)
            ->where('status', NewClientTicketStatusEnum::CLOSED)
            ->count();
        $closedOutageTickets = TicketGgn::where('group_id', $request->user()->id_group)
            ->where('status', OutageTicketStatusEnum::CLOSED)
            ->count();

        return view('dashboards.helpdesk', compact(
            'newClientOpenTickets',
            'outageOpenTickets',
            'closedNewClientTickets',
            'closedOutageTickets'
        ));
    }

    public function newIssuesChart(Request $request)
    {
        $year = date('Y');

        // Get all tickets with their dates and convert to scatter plot format
        $newInstallations = TicketPsb::where('group_id', $request->user()->id_group)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function ($ticket) {
                return [
                    'x' => $ticket->created_at->format('Y-m-d'),
                    'y' => 1 // Each installation counts as 1
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

        $troubles = TicketGgn::where('group_id', $request->user()->id_group)
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
}
