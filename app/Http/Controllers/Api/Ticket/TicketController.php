<?php


namespace App\Http\Controllers\Api\Ticket;


use App\Http\Controllers\Controller;
use App\Models\MappingUserLicense;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function list_ticket(Request $request){
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = Ticket::where('dinetkan_user_id', $request->user()->dinetkan_user_id);
        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($tickets);
    }
    public function listService(Request $request)
    {
        $services = MappingUserLicense::query()
            ->join('license_dinetkan','license_dinetkan.id','=','mapping_user_license.license_id')
            ->where('mapping_user_license.dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->select('license_dinetkan.id','license_dinetkan.name','mapping_user_license.service_id')
            ->get();

        return response()->json($services);
    }

    private function generateTicketNumber()
    {
        $prefix = date('Ym'); // YYYYMM

        $last = Ticket::where('ticket_number','like',$prefix.'%')
            ->orderBy('ticket_number','desc')
            ->first();

        if($last){
            $lastNumber = intval(substr($last->ticket_number, 6));
            $next = $lastNumber + 1;
        }else{
            $next = 1;
        }

        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function create_ticket(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'subject'=>'required',
            'department'=>'required',
            'priority'=>'required',
            'message'=>'required',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:30720',

        ]);
        DB::beginTransaction();
        try{
            $ticket = Ticket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'dinetkan_user_id' => $request->user()->dinetkan_user_id,
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'department' => $request->department,
                'priority' => $request->priority,
                'service_id' => $request->service_id,
                'status' => 'open'
            ]);

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'dinetkan_user_id' => $request->user()->dinetkan_user_id,
                'message' => $request->message
            ]);

            $radiusUrl = config('services.radius.url');
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $response = Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post($radiusUrl.'/api/upload-ticket', [
                        'ticketid' => $ticket->id
                    ]);

                    if(!$response->successful()){
                        throw new \Exception('Gagal upload ke devradiusqu');
                    }

                    $filename = $response->json()['filename'];

                    TicketAttachment::create([
                        'ticket_message_id' => $message->id,
                        'file' => $filename
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'data' => $ticket,
                'message' => 'Data berhasil dibuat'
            ], 200);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Data gagal dibuat'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $ticket = Ticket::with('messages.attachments')
            ->with([
                'messages.user' => function ($q) {
                    $q->select('dinetkan_user_id','name','email');
                }
            ])
            ->findOrFail($id);
        $isAdmin = false;
        if($ticket->dinetkan_user_id != $request->user()->dinetkan_user_id){
            $isAdmin = true;
        }
        return response()->json($ticket);
    }
    
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message'=>'required',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,doc,docx,xls,xlsx|max:30720'
        ]);
        DB::beginTransaction();
        try{
            $isAdmin = false;
            $tiket = Ticket::findOrFail($id);

            if($tiket->dinetkan_user_id != $request->user()->dinetkan_user_id){
                $isAdmin = true;
            }

            $message = TicketMessage::create([
                'ticket_id'=>$id,
                'dinetkan_user_id'=> $isAdmin ? null : $request->user()->dinetkan_user_id,
                'message'=>$request->message
            ]);

            $radiusUrl = config('services.radius.url');
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $response = Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post($radiusUrl.'/api/upload-ticket', [
                        'ticketid' => $tiket->id
                    ]);

                    if(!$response->successful()){
                        throw new \Exception('Gagal upload ke devradiusqu');
                    }

                    $filename = $response->json()['filename'];

                    TicketAttachment::create([
                        'ticket_message_id' => $message->id,
                        'file' => $filename
                    ]);
                }
            }

            Ticket::where('id',$id)->update([
                'status' => $isAdmin ? 'answered' : 'open'
            ]);

            DB::commit();
            return response()->json([
                'data' => $tiket,
                'message' => 'Data berhasil dibuat'
            ], 200);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Data gagal dibuat'
            ], 500);
        }
    }

    public function close(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            // HANYA USER PEMILIK
            $ticket = Ticket::where('id',$id)
                ->where('dinetkan_user_id',$request->user()->dinetkan_user_id)
                ->update([
                    'status'=> 'closed',
                    'closed_date'=> Carbon::now()
                ]);
            DB::commit();
            return response()->json([
                'data' => $ticket,
                'message' => 'Data berhasil dibuat'
            ], 200);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Data gagal dibuat'
            ], 500);
        }
    }

    public function list_priority(){
        $list = array(
            'low' => "LOW",
            'medium' => "medium",
            'high' => "high"
        );
        return response()->json([
            'data' => $list,
        ], 200);
    }

    public function list_department(){
        $list = array(
            'support' => "Support",
            'sales' => "Sales",
            'abuse' => "Abuse"
        );
        return response()->json([
            'data' => $list,
        ], 200);
    }
}
