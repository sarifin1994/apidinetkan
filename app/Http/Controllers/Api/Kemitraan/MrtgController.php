<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Http\Controllers\Controller;
use App\Models\GrafikMikrotik;
use App\Models\MappingUserLicense;
use App\Models\ServiceDetail;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use Illuminate\Http\Request;
use App\Enums\ServiceStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MrtgController extends Controller
{
    public function list_graph(Request $request){
        $services = MappingUserLicense::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', ServiceStatusEnum::ACTIVE->value)
            ->with('service')
            ->with('service_detail')
            ->with('service_libre')->get();
        $new_services = [];
        if($services){
            foreach ($services as $s){
                $active_graph = "";
                $graph_mikrotik = [];
                $graph_cacti = [];
                $graph_libre = [];

                // cacti
                if($s->service_detail->graph_type == 'cacti'){
                    $active_graph = 'cacti';
                }
                $graph = UserDinetkanGraph::query()
                    ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
                    ->where('service_id', $s->service_id)
                    ->get();
                if($graph){
                    foreach ($graph as $g){
                        $graph_cacti[]=[
                            'data' => $g,
                            'link' => array(
                                'daily' => '/api/kemitraan/mrtg/daily_graph_json/'.$g->id,
                                'weekly' => '/api/kemitraan/mrtg/weekly_graph_json/'.$g->id,
                                'monthly' => '/api/kemitraan/mrtg/monthly_graph_json/'.$g->id,
                            )
                        ];
                    }
                }

                // mikrotik
                if($s->service_detail->graph_type == 'mikrotik'){
                    $active_graph = 'mikrotik';
                }
                $graph_mikrotik[] = array(
                    'link' => array(
                        'daily' => '/api/kemitraan/mrtg/graph_json_mikrotik_daily/'.$s->service_id,
                        'weekly' => '/api/kemitraan/mrtg/graph_json_mikrotik_weekly/'.$s->service_id,
                        'monthly' => '/api/kemitraan/mrtg/graph_json_mikrotik_monthly/'.$s->service_id,
                    )
                );

                // libre
                if($s->service_detail->graph_type == 'libre'){
                    $active_graph = 'libre';
                }
                if($s->service_libre){
                    foreach ($s->service_libre as $l){
                        $graph_libre[] = array(
                            'data' => $l,
                            'link' => '/api/kemitraan/mrtg/get_ifname_image/'.$l->hostname.'/'.rawurlencode($l->ifName)
                        );
                    }
                }

                $new_services[] = array(
                    'id' => $s->id,
                    'service_id' => $s->service_id,
                    'service_name' => $s->service->name,
                    'active_graph' => $active_graph,
                    'mikrotik' => $graph_mikrotik,
                    'cacti' => $graph_cacti,
                    'libre' =>$graph_libre
                );
            }
        }
        return response()->json($new_services);
    }

    function get_ifname_image($hostname, $ifname){
        return getPortbits($hostname, $ifname);
    }

    public function daily_graph_json(Request $request, $graph_id){
        $list_graph = $this->get_graph($request,'daily', $graph_id);
        $newlist = [];
        foreach ($list_graph as $row){
            $newlist[] = $row;
        }
        return response()->json($newlist);
    }

    public function weekly_graph_json(Request $request, $graph_id){
        $list_graph = $this->get_graph($request, 'weekly',$graph_id);
        $newlist = [];
        foreach ($list_graph as $row){
            $newlist[] = $row;
        }
        return response()->json($newlist);
    }

    public function monthly_graph_json(Request $request, $graph_id){
        $list_graph = $this->get_graph($request, 'monthly',$graph_id);
        $newlist = [];
        foreach ($list_graph as $row){
            $newlist[] = $row;
        }
        return response()->json($newlist);
    }

//    public function year_get_graph_json(){
//        $list_graph = $this->get_graph('yearly');
//        $newlist = [];
//        foreach ($list_graph as $row){
//            $newlist[] = $row;
//        }
//        return response()->json($newlist);
//    }

    protected function get_graph(Request $request,$type, $graph_id){

        $colors = array(
            '#00FF9C',
            '#FFEB00',
            '#73EC8B',
            '#FF204E',
            '#836FFF',
            '#97FFF4'
        );
        $userdinetkan = UserDinetkan::where('id', $request->user()->id)->first();

        $userdinetkangraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)->where('id', $graph_id)->get();
        $list_graph = [];

        foreach ($userdinetkangraph as $row){
            $graph = $this->get_tree_node_mrtg_summary($request, $row->graph_id,$type);
            $content =  $this->get_tree_node_mrtg($request, $row->graph_id,$type);
            $convert = ($content);
            $legend=[];
            $min = 0;
            $max = 0;
            foreach ($convert as $key=>$val){
                if(Str::contains($key, 'legend')){
                    $val = Str::replace('"', '', $val);
                    $val = Str::trim($val);
                    $legend[]=$val;
                }
                if(Str::contains($key, 'value_min')){
                    $floatValue = number_format((float)str_replace(',', '', $val), 2, '.', '');
                    $min = $val;
                }
                if(Str::contains($key, 'value_min')){
                    $floatValue = number_format((float)str_replace(',', '', $val), 2, '.', '');
                    $max = $val;
                }
            }

            $labels=[];
            $label=[];
            $yvalue = '';
            if(isset($graph[0]['Vertical Label'])){
                $yvalue = $graph[0]['Vertical Label'];
            }
            if(isset($graph[1])){
                foreach($graph[1] as $row2){
                    foreach($row2 as $key=>$val){
                        if($key == "Date"){
                            $labels[] = $val;
                        }else{
                            if(!in_array($key, $label)){
                                $label[]=$key;
                            }
                        }
                    }
                }
            }
            $label = array_unique($label);
            $datasets=[];
            $idx=0;
            foreach ($label as $lbl){
                if(!Str::contains($lbl, 'col')){
                    $datavalue=[];
                    foreach($graph[1] as $row3){
                        foreach($row3 as $key=>$val) {
                            if($key==$lbl){
                                $floatValue = number_format((float)str_replace(',', '', $val), 2, '.', '');

                                if($floatValue > 1000000){
                                    $datavalue[]=$floatValue / 1000000;
                                }else{
                                    $datavalue[]=$floatValue;
                                }
                            }
                        }
                    }
                    $datasets[] = array(
                        'label' => $lbl,
                        'data' => $datavalue,
                        'backgroundColor' => $colors[$idx],
                        'borderColor' => $colors[$idx],
                        'fill' => false
                    );
                    $idx++;
                }
            }
            $labels = implode("\",\"",$labels);
            $labels = "\"".$labels."\"";
            $datasets = json_encode($datasets);

            $list_graph[$row->graph_id]=array(
                'data' => $row,
                'legends' => $legend,
                'labels' => $labels,
                'datasets' => $datasets,
                'id' => $row->graph_id,
                'yvalue' => $yvalue,
                'min' => $min,
                'max' => $max
            );
        }
        return $list_graph;
    }

    public function get_tree_node_mrtg_summary(Request $request, $graph, $type = 'daily'){
        // step 2
        try {

            $now = Carbon::now()->subHours(2)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
            $end =  Carbon::now()->subMinutes(5)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            if($type == 'daily'){
                $now = Carbon::now()->subHours(2)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(5)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }

            if($type == 'weekly'){
                $now = Carbon::now()->subWeeks(1)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(30)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
//                echo $now." -- ".$end;exit;
            }

            if($type == 'monthly'){
                $now = Carbon::now()->subMonth()->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(120)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }

            if($type == 'yearly'){
                $now = Carbon::now()->subYear()->format('Y-m-d'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subDay()->format('Y-m-d'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }
            $params = array(
                "local_graph_id" => $graph,
                "rra_id" => "0",
                "format" => "table",
                "graph_start" => $now,
                "graph_end" => $end,
                "graph_height" => "200",
                "graph_width" => "700"
            );
            $_id = Str::lower(Str::replace(' ', '', $request->user()->name));
            $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_xport/'.$_id.'?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::get($apiUrl);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? [];
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return [];
    }

    public function get_tree_node_mrtg(Request $request, $graph, $type = 'daily'){
        // step 2
        $_id = Str::lower(Str::replace(' ', '', $request->user()->name));
        try {
            $now = Carbon::now()->subHours(2)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
            $end =  Carbon::now()->subMinutes(5)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            if($type == 'daily'){
                $now = Carbon::now()->subHours(2)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(5)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }

            if($type == 'weekly'){
                $now = Carbon::now()->subWeeks(1)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(30)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
//                echo $now." -- ".$end;exit;
            }

            if($type == 'monthly'){
                $now = Carbon::now()->subMonth()->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subMinutes(120)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }

            if($type == 'yearly'){
                $now = Carbon::now()->subYear()->format('Y-m-d'); // Carbon::now()->format('Y-m-d 00:00');
                $end =  Carbon::now()->subDay()->format('Y-m-d'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            }
            $params = array(
                "rra_id" => "0",
                "local_graph_id" => $graph,
                "graph_start" => $now,
                "graph_end" => $end,
                "graph_height" => "200",
                "graph_width" => "700"
            );
            $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_json/'.$_id.'?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::get($apiUrl);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? [];
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return [];
    }
    public function graph_json_mikrotik_daily(Request $request, $service_id = 0){
        $service = ServiceDetail::query()->where('service_id', $service_id)->first();
        if($service){
            $list_graph = $this->get_graph_mikrotik($request, 'daily', $service);
            return $list_graph;
        }
    }

    public function graph_json_mikrotik_weekly(Request $request, $service_id = 0){
        $service = ServiceDetail::query()->where('service_id', $service_id)->first();
        if($service){
            $list_graph = $this->get_graph_mikrotik($request, 'weekly', $service);
            return $list_graph;
        }
    }

    public function graph_json_mikrotik_monthly(Request $request, $service_id = 0){
        $service = ServiceDetail::query()->where('service_id', $service_id)->first();
        if($service){
            $list_graph = $this->get_graph_mikrotik($request, 'monthly', $service);
            return $list_graph;
        }
    }

    public function get_graph_mikrotik(Request $request, $type = 'daily', $service){
        $results = null;
        if($type == 'daily'){
            $HoursAgo = Carbon::now()->subHours(12);
            $results = GrafikMikrotik::query()
                ->select([
                    'id',
                    'vlan_name',
                    'created_at',
                    'rx_bits_per_second',
                    'tx_bits_per_second'
                ])
                ->where('id_mikrotik', $service->id_mikrotik)
                ->where('vlan_id', $service->vlan_id)
                ->where('created_at', '>=', $HoursAgo)
                ->orderBy('created_at', 'asc')
                ->get();

            // Siapkan data untuk Chart.js
            $labels = [];
            $rxData = [];
            $txData = [];
            $from = [];
            foreach ($results as $row) {
                $from[] = \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i');
                $vlan_name = $row->vlan_name;

                $rxData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->rx_bits_per_second / 1000
                ];
                $txData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->tx_bits_per_second / 1000
                ];
            }
        }
        if($type == 'weekly'){
            $HoursAgo = Carbon::now()->subWeek();
            $results = GrafikMikrotik::query()
                ->select([
                    'id',
                    'vlan_name',
                    'created_at',
                    'rx_bits_per_second',
                    'tx_bits_per_second'
                ])
                ->where('id_mikrotik', $service->id_mikrotik)
                ->where('vlan_id', $service->vlan_id)
                ->where('created_at', '>=', $HoursAgo)
                ->orderBy('created_at', 'asc')
                ->get();

            // Siapkan data untuk Chart.js
            $labels = [];
            $rxData = [];
            $txData = [];
            $from = [];
            foreach ($results as $row) {
                $from[] = \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i');
                $vlan_name = $row->vlan_name;

                $rxData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->rx_bits_per_second / 1000
                ];
                $txData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->tx_bits_per_second / 1000
                ];
            }
        }


        if($type == 'monthly'){
            $HoursAgo = Carbon::now()->subMonth();
            $results = GrafikMikrotik::query()
                ->select([
                    'id',
                    'vlan_name',
                    'created_at',
                    'rx_bits_per_second',
                    'tx_bits_per_second'
                ])
                ->where('id_mikrotik', $service->id_mikrotik)
                ->where('vlan_id', $service->vlan_id)
                ->where('created_at', '>=', $HoursAgo)
                ->orderBy('created_at', 'asc')
                ->get();

            // Siapkan data untuk Chart.js
            $labels = [];
            $rxData = [];
            $txData = [];
            $from = [];
            foreach ($results as $row) {
                $from[] = \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i');
                $vlan_name = $row->vlan_name;

                $rxData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->rx_bits_per_second / 1000
                ];
                $txData[] = [
                    'x' => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'y' => (int)$row->tx_bits_per_second / 1000
                ];
            }
        }

        $delayBetweenPoints = 0;
        if(count($rxData) > 0){
            $delayBetweenPoints = 10000 / count($rxData);
        }
        return response()->json([
            'from' => $from,
            'vlan_name' => $vlan_name ?? '',
            'service_id' => $service->service_id,
            'labels' => $labels,
            'rx' => $rxData,
            'tx' => $txData,
            'delayBetweenPoints' => $delayBetweenPoints
        ]);
    }

}
