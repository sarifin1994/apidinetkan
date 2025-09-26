<?php

namespace App\Http\Controllers\Dinetkan;

use App\DataTables\Owner\LicenseDataTable;
use App\Enums\OltDeviceEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\LicenseRequest;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LicenseController extends Controller
{
    public function index(LicenseDataTable $dataTable)
    {
        $licenseCount = License::count();
        $oltModels = OltDeviceEnum::getSelectOptions();

        return $dataTable->render('owner.license', [
            'licenseCount' => $licenseCount,
            'oltModels' => $oltModels
        ]);
    }

    public function store(LicenseRequest $request)
    {
        $data = $request->validated();

        try {
            License::create($data);
            return response()->json(['message' => 'License created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating license: ' . $e->getMessage()], 500);
        }
    }

    public function edit(License $license)
    {
        return response()->json($license);
    }

    public function update(LicenseRequest $request, License $license)
    {
        $data = $request->validated();

        try {
            $license->update($data);
            return response()->json(['message' => 'License updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating license'], 500);
        }
    }

    public function destroy(License $license)
    {
        try {
            $license->delete();
            return redirect()->back()->with('success', 'License deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting license');
        }
    }
}
