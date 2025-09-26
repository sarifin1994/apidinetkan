<?php

namespace App\Http\Controllers\Dinetkan;

use App\DataTables\Owner\LicenseDataTable;
use App\DataTables\Owner\LicenseDinetkanDataTable;
use App\Enums\OltDeviceEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dinetkan\LicenseDinetkanRequest;
use App\Http\Requests\Owner\LicenseRequest;
use App\Models\CategoryLicenseDinetkan;
use App\Models\License;
use App\Models\License_dinetkan;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\User;
use App\Models\UserDinetkan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LicenseDinetkanController extends Controller
{
    public function index(LicenseDinetkanDataTable $dataTable)
    {
        $licenseCount = LicenseDinetkan::count();
        $categories = CategoryLicenseDinetkan::get();
        $oltModels = OltDeviceEnum::getSelectOptions();

        return $dataTable->render('backend.dinetkan.license_dinetkan', [
            'licenseCount' => $licenseCount,
            'oltModels' => $oltModels,
            'categories' => $categories
        ]);
    }

    public function store(LicenseDinetkanRequest $request)
    {
        $data = $request->validated();

        try {
            LicenseDinetkan::create($data);
            return response()->json(['message' => 'License created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating license: ' . $e->getMessage()], 500);
        }
    }

    public function edit(LicenseDinetkan $license)
    {
        return response()->json($license);
    }

    public function update(LicenseDinetkanRequest $request, LicenseDinetkan $license)
    {
        $data = $request->validated();

        try {
            $license->update($data);
            return response()->json(['message' => 'License updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating license'], 500);
        }
    }

    public function destroy(LicenseDinetkan $license)
    {
        try {
            $license->delete();
            return redirect()->back()->with('success', 'License deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting license');
        }
    }

    public function by_category($category_id, $type)
    {
        $license = LicenseDinetkan::query()
            ->where('category_id', $category_id)
            ->where('type', $type)
            ->orderBy('name', 'asc')
            ->get();

        if($type == "all"){
            $license = LicenseDinetkan::query()
                ->where('category_id', $category_id)
                ->orderBy('name', 'asc')
                ->get();
        }
        return response()->json($license);
    }

    public function by_license(Request $request, $license_id)
    {
//        ServiceStatusEnum
        $user = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $cekmapping = MappingUserLicense::where('dinetkan_user_id', $request->dinetkan_user_id)->where('license_id', $license_id)->where('status', 1)->first();
        if($cekmapping != null && $request->is_edit == true && $cekmapping->id == $request->id_mapping){
            return response()->json(
                [
                    'success' => false,
                    'mulai' => "",
                    'akhir' => "",
                    'hari_pakai' => "",
                    'harga_asli' => "",
                    'harga_prorate' => "",
                    'ppn' => "",
                    'message' => 'User '.$user->name.' memiliki service tersebut dan aktif'
                ]
            );
        }
        $prorate = hitungProrate($request->payment_method, $request->active_date, $request->payment_date, $license_id, $request->prorata);
        return response()->json($prorate);
    }
}
