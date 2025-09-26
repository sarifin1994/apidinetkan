<?php


namespace App\Http\Controllers\Admin;

use App\Models\ProductDInetkan;
use App\Settings\LicenseDinetkanSettings;
use Illuminate\Http\Request;

class ProductDinetkanController
{

    public function index(Request $request)
    {
        $product = ProductDInetkan::where('dinetkan_user_id', $request->user()->dinetkan_user_id)->get();
        $settings = app(LicenseDinetkanSettings::class);
        $ppn = $settings->ppn_product_mitra;
        $bhp = $settings->bhp_product_mitra;
        $uso = $settings->uso_product_mitra;
        return view('backend.product_dinetkan.index', compact(
            'product','ppn','bhp', 'uso'));
    }

    public function store(Request $request){
        try{
            ProductDInetkan::create([
                'product_name' => $request->product_name,
                'price' => (int) str_replace(".","",$request->price),
                'ppn' => $request->ppn,
                'bhp' => $request->bhp,
                'uso' => $request->uso,
                'dinetkan_user_id' => $request->user()->dinetkan_user_id,
                'kapasitas' => $request->kapasitas,
            ]);

            return redirect()->back()->with('success', 'Product Create successfully');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'Product Create un-successfully '.$e->getMessage());

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
            $masterPop = ProductDInetkan::where('id', $id)->first();
            $data = [
                'product_name' => $request->product_name,
                'price' => (int) str_replace(".","",$request->price),
                'ppn' => $request->ppn,
                'bhp' => $request->bhp,
                'uso' => $request->uso,
                'kapasitas' => $request->kapasitas,
            ];
            $masterPop->update($data);
            return redirect()->back()->with('success', 'Product update successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Product update POP');
        }
    }
}
