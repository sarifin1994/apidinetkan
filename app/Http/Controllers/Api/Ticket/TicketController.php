<?php


namespace App\Http\Controllers\Api\Ticket;


use App\Http\Controllers\Controller;
use App\Library\WaNdiing;
use App\Models\MappingUserLicense;
use App\Models\MasterJenisGangguan;
use App\Models\MasterMetro;
use App\Models\ServiceDetail;
use App\Models\Setting\Company;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\Tiket\Evidence;
use App\Models\Tiket\EvidencePhotos;
use App\Models\Tiket\TiketGangguan;
use App\Models\UserDinetkan;
use App\Models\Whatsapp\Mpwa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function list_ticket(Request $request){
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = Ticket::where('dinetkan_user_id', $request->user()->dinetkan_user_id);
        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($tickets);
    }
    private function generateTicketNumber()
    {
        $prefix = 'DN-2';//date('Ym'); // YYYYMM

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

    public function listService(Request $request)
    {
        $servicesraw = MappingUserLicense::query()
            ->with(['service','service_detail','service_detail.metro'])
            ->where('dinetkan_user_id',$request->user()->dinetkan_user_id)->get();
        $services = $servicesraw->map(function ($s){
            $adds = " - ".$s->service_detail?->full_address ?? '';

            $metro=[];
            if(isset($s->service_detail->metro_id)){
                $metroraw = MasterMetro::query()->where('id', $s->service_detail->metro_id)->first();
                $metro = [
                    'id' => $metroraw->id,
                    'name' => $metroraw->name
                ];
            }
            return array(
                'id' => $s->service->id,
                'name' => $s->service->name.$adds,
                'service_id' => $s->service_id,
                'metro'=> $metro
            );
        });

        return response()->json($services);
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
            'attachments.*' => 'file|mimes:jpg,jpeg,png|max:30720',

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
            $api_key_ext = config('services.radius.API_KEY_EXT');
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $response = Http::withHeaders([
                        'X-API-KEY'     => $api_key_ext,
                    ])->attach(
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
            $api_key_ext = config('services.radius.API_KEY_EXT');
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $response = Http::withHeaders([
                        'X-API-KEY'     => $api_key_ext,
                    ])->attach(
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

    public function store_mitra(Request $request)
    {
        $wanding = new WaNdiing();
//        return response()->json($request->all());
        $validator = Validator::make($request->all(), [
            'jenis_gangguan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {

            /* ================= DATA DASAR ================= */

            $waNding = new WaNdiing();
            $nomor_tiket = 'DN-' . mt_rand(100000, 999999);

            $mapp = MappingUserLicense::where('service_id',$request->service_id)
                ->with('service')->first();

            $serviceDetail = ServiceDetail::where('service_id',$request->service_id)
                ->with(['province','regency','district','village','pop','service_active.service'])
                ->first();

            $gangguan = MasterJenisGangguan::find($request->jenis_gangguan);
            $metro    = MasterMetro::find($serviceDetail->metro_id);
            $mpwa    = Mpwa::where('shortname', "dinetkan")->first();
            $company = Company::where('shortname',"dinetkan")->first();

            $nama_pelanggan = '';
            $email = '';
            if($mapp){
                $ud = UserDinetkan::where('dinetkan_user_id',$mapp->dinetkan_user_id)->first();
                $nama_pelanggan = $ud->name ?? '';
                $email = $ud->email ?? '';
            }

            /* ================= UPLOAD FOTO ================= */

            $path = null;
            $upload_file = null;
            $radiusUrl = config('services.radius.url');
            $api_key_ext = config('services.radius.api_key_ext');
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $response = Http::withHeaders([
                        'api-key-ext'=> $api_key_ext,
                    ])->attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post($radiusUrl.'/api/upload-ticket-evidence');

                    if(!$response->successful()){
                        return response()->json([
                            'message' => "Gagal upload ke devradiusqu",
                            'description' => $response->json()
                        ], 500);
                    }

                    $path = $response->json()['filename'];

                    try{
                        $upload_file = $waNding->uploadFile($file);
                    }catch(\Exception $e){}
                }
            }

            /* ================= SIMPAN TIKET ================= */

            $tiket = TiketGangguan::create([
                'shortname' => "dinetkan",
                'shortname_created' => $request->user()->shortname,
                'nomor_tiket' => $nomor_tiket,
                'service_id' => $request->service_id,
                'nama_pelanggan' => $nama_pelanggan,
                'email' => $email,
                'jenis_gangguan' => $request->jenis_gangguan,
                'prioritas' => $request->priority,
                'note' => $request->message,
                'subject' => $request->subject,
                'teknisi' => $request->teknisi,
                'metro_id' => $request->metro_id,
                'created_by' => $request->user()->username,
                'whatsapp_group_id' => $request->whatsapp_group_id ?? 0,
                'img_path' => "evidence_photos/".$path
            ]);

            $tanggal_tiket = (new \DateTime($tiket->created_at))->format('d/m/Y H:i');

            /* ================= FORMAT ALAMAT ================= */

            $alamat = ($serviceDetail->address ?? '')." ".
                ($serviceDetail->village->name ?? '').", ".
                ($serviceDetail->district->name ?? '').", ".
                ($serviceDetail->regency->name ?? '').", ".
                ($serviceDetail->province->name ?? '')."\n";

            /* ================= MESSAGE ================= */

            $message_mitra =
                "Pelanggan Yth.
Bpk/Ibu *$nama_pelanggan*
Tiket gangguan berhasil dibuat.

Tanggal Laporan : $tanggal_tiket
Prioritas : $request->prioritas
Service ID : {$serviceDetail->service_id}
Nomor Tiket : {$tiket->nomor_tiket}
Jenis Gangguan : {$gangguan->name}
Metro : {$metro->name}
Keluhan : $request->note

Terima kasih.";

            $message_metro =
                "Kepada Yth. Rekan {$metro->name}
                
Tanggal Laporan : $tanggal_tiket
ID Vendor : {$serviceDetail->vendor}
PIC : {$metro->pic}
No Telp : {$metro->pic_phone}
Alamat : $alamat
Longitude : {$serviceDetail->longitude}
Latitude : {$serviceDetail->latitude}
Jenis Gangguan : {$gangguan->name}
Keluhan : $request->note

Mohon segera ditangani.";

            $message_internal =
                "Dear Tim Network mohon dicek gangguan Berikut.
                
Nomor Tiket : $nomor_tiket
Tanggal Laporan : $tanggal_tiket
Nama Mitra : $nama_pelanggan
Service : ".$serviceDetail->service_active->service->name." 
Pelanggan ID : {$serviceDetail->service_id}
Alamat : $alamat
VLAN : {$serviceDetail->vlan}
POP : ".($serviceDetail->pop->name ?? '')."
METRO : {$metro->name}
ID/CID/SO/SID Vendor : {$serviceDetail->vendor}
IP Prefix : {$serviceDetail->ip_prefix}
SN Modem : {$serviceDetail->sn_modem}
Jenis Gangguan : {$gangguan->name}
Keluhan : $request->note

Silahkan login ke dash.radiusqu.com

Terimakasih";

            /* ================= KIRIM WA ================= */

            if($gangguan){

                // INTERNAL
                if(in_array('internal',$gangguan->group_tiket) && $company->group_internal){

                    if(isset($upload_file['data'])){
                        $data = [
                            "to"=>$company->group_internal,
                            "type"=>"image",
                            "file_path"=>$upload_file['data']['path'],
                            "text"=>$message_internal
                        ];
                    }else{
                        $data = [
                            "to"=>$company->group_internal,
                            "type"=>"text",
                            "text"=>$message_internal
                        ];
                    }

                    $json = $wanding->sendMessage($mpwa->api_key, $data);
//                    dispatch_wa_message($mpwa->api_key,$data);
                }

                // MITRA
                if(in_array('mitra',$gangguan->group_tiket) && $serviceDetail->group_id){

                    if(isset($upload_file['data'])){
                        $data = [
                            "to"=>$serviceDetail->group_id,
                            "type"=>"image",
                            "file_path"=>$upload_file['data']['path'],
                            "text"=>$message_mitra
                        ];
                    }else{
                        $data = [
                            "to"=>$serviceDetail->group_id,
                            "type"=>"text",
                            "text"=>$message_mitra
                        ];
                    }

                    $json = $wanding->sendMessage($mpwa->api_key, $data);
//                    dispatch_wa_message($mpwa->api_key,$data);
                }

                // METRO
                if(in_array('metro',$gangguan->group_tiket) && $metro->id_wag){

                    if(isset($upload_file['data'])){
                        $data = [
                            "to"=>$metro->id_wag,
                            "type"=>"image",
                            "file_path"=>$upload_file['data']['path'],
                            "text"=>$message_metro
                        ];
                    }else{
                        $data = [
                            "to"=>$metro->id_wag,
                            "type"=>"text",
                            "text"=>$message_metro
                        ];
                    }

                    $json = $wanding->sendMessage($mpwa->api_key, $data);
//                    dispatch_wa_message($mpwa->api_key,$data);
                }
            }

            DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'Tiket Berhasil Disubmit',
                'data'=>$tiket
            ]);

        } catch (\Exception $e){

            DB::rollBack();

            Log::error('store_mitra_error',[
                'msg'=>$e->getMessage(),
                'line'=>$e->getLine()
            ]);

            return response()->json([
                'success'=>false,
                'message'=>'Tiket Gagal '.$e->getMessage()
            ],500);
        }
    }

    public function jenis_gangguan(){
        $jenisGangguan = MasterJenisGangguan::query()->get();
        $jenisGangguan = $jenisGangguan->map(function ($e){
            return [
                "id"=>$e->id,
                "name"=>$e->name
            ];
        });
        return response()->json($jenisGangguan);
    }

    public function show_new($id){
        $radiusUrl = config('services.radius.url');
        $tiket = TiketGangguan::query()->findOrFail($id);
        $metro = null;
        $jenisGangguan = null;
        if($tiket->metro_id){
            $metro = MasterMetro::query()->findOrFail($tiket->metro_id);
        }
        if($tiket->jenis_gangguan){
            $jenisGangguan = MasterJenisGangguan::query()->findOrFail($tiket->jenis_gangguan);
        }

        $evidencesraw = Evidence::query()->where('tiket_id', $id)->with('photo')->orderBy('tanggal_pengerjaan', 'DESC')->get();

        $detail = [
            'id'=>$tiket->id,
            'nomor_tiket'=>$tiket->nomor_tiket,
            'nama_pelanggan'=>$tiket->nama_pelanggan,
            'email'=>$tiket->email,
            'jenis_gangguan'=>$jenisGangguan->name,
            'pelanggan_id'=>$tiket->pelanggan_id,
            'service_id'=>$tiket->service_id,
            'prioritas'=>$tiket->prioritas,
            'metro' => $metro->name,
            'lampiran'=>$tiket->img_path?"$radiusUrl/api/tickets/show_file/$tiket->id":"",
            'subject'=>$tiket->subject,
            'keluhan'=>$tiket->note
        ];
        $evidences = $evidencesraw->map(function($e) use($radiusUrl){
            $photos = null;
            $photosraw = EvidencePhotos::query()->where('evidence_id', $e->id)->get();
            $photos = $photosraw->map(function($p) use($radiusUrl){
               return [
                   'id'=>$p->id,
                   'url'=>$radiusUrl."/tiket/evidence_show_file/show_file/$p->id"
               ];
            });
            return [
                'id'=>$e->id,
                'tiket_id'=>$e->tiket_id,
                'tanggal_pengerjaan'=>$e->tanggal_pengerjaan,
                'keterangan'=>$e->keterangan,
                'photo'=>$photos
            ];
        });
        $data = [
            'detail' => $detail,
            'history_progress'=>$evidences
        ];
        return response()->json($data);
    }
}
