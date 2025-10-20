<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Models\ProductDInetkan;
use App\Settings\LicenseDinetkanSettings;
use Illuminate\Http\Request;

class ProductDinetkanController
{

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = ProductDInetkan::where('dinetkan_user_id', $request->user()->dinetkan_user_id);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('kapasitas')) {
            $query->where('kapasitas', 'like', '%' . $request->kapasitas . '%');
        }

        $profile = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($profile);
    }

    public function store(Request $request){
        try{
            $profile = ProductDInetkan::create([
                'product_name' => $request->product_name,
                'price' => (int) str_replace(".","",$request->price),
//                'ppn' => $request->ppn,
//                'bhp' => $request->bhp,
//                'uso' => $request->uso,
                'dinetkan_user_id' => $request->user()->dinetkan_user_id,
                'kapasitas' => $request->kapasitas,
            ]);

            return response()->json($profile);
        }catch (\Exception $e){
            return response()->json($e->getMessage());

        }

    }

    public function single($id){
        $data = ProductDInetkan::where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id){
        try {
            $data = ProductDInetkan::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'POP deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting POP');
        }
    }

    public function update(Request $request,$id){
        try {
            $profile = ProductDInetkan::where('id', $id)->first();
            $data = [
                'product_name' => $request->product_name,
                'price' => (int) str_replace(".","",$request->price),
//                'ppn' => $request->ppn,
//                'bhp' => $request->bhp,
//                'uso' => $request->uso,
                'kapasitas' => $request->kapasitas,
            ];
            $profile->update($data);
            return response()->json($profile);
        }catch (\Exception $e){
            return response()->json($e->getMessage());

        }
    }

}
