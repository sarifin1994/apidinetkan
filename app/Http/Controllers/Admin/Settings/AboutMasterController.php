<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutMasterController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::where('group_id', $request->user()->id_group)->first();

        return view('settings.master.about.index', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'file_logo' => 'required|image|mimes:png,jpeg,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with(['error' => 'Logo Gagal Diupload!']);
        }
        // hapus old image
        Storage::disk('local')->delete('public/logo' . $company->logo);

        //upload new image
        $image = $request->file('file_logo');
        $image->storeAs('public/logo', $image->hashName());
        $company->update([
            'logo' => $image->hashName(),
        ]);

        return redirect()
            ->back()
            ->with(['success' => 'Logo Berhasil Diupload!']);
    }

    public function updateCompany(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $company->update([
            'name' => $request->name,
            'nickname' => $request->nickname,
            'email' => $request->email,
            'wa' => $request->wa,
            'address' => $request->address,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $company,
        ]);
    }
}
