<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Setting\Company;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::where('shortname', multi_auth()->shortname)->first();
        if(!$company){
            Company::create([
                'shortname' => multi_auth()->shortname,
                'name' => 'Radiusqu Network',
            ]);
        }
        return view('backend.setting.company.index_new', compact('company'));
    }

    public function update(Request $request, Company $perusahaan)
    {
        $perusahaan->update([
            'name' => $request->name,
            'singkatan' => $request->singkatan,
            'slogan' => $request->slogan,
            'email' => $request->email,
            'wa' => $request->wa,
            'website' => $request->website,
            'address' => $request->address,
            'note' => $request->note,
            'bank' => $request->bank,
            'holder' => $request->holder,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $perusahaan,
        ]);
    }

    public function uploadLogo(Request $request, Company $perusahaan)
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
        Storage::disk('local')->delete('public/logo' . $perusahaan->logo);

        //upload new image
        $image = $request->file('file_logo');
        $image->storeAs('logo', $image->hashName(),'public');
        $perusahaan->update([
            'logo' => $image->hashName(),
        ]);

        return redirect()
            ->back()
            ->with(['success' => 'Logo Berhasil Diupload!']);
    }

    // public function updateCompany(Request $request, Company $company)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'nickname' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors(),
    //         ]);
    //     }

    //     $company->update([
    //         'name' => $request->name,
    //         'nickname' => $request->nickname,
    //         'email' => $request->email,
    //         'wa' => $request->wa,
    //         'address' => $request->address,
    //     ]);

    //     //return response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data Berhasil Disimpan',
    //         'data' => $company,
    //     ]);
    // }
}
