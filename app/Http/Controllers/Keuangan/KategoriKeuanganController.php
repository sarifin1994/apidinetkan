<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan\KategoriKeuangan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriKeuanganController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $kategori = KategoriKeuangan::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($kategori)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->status === 1) {
                        return '
            <a href="javascript:void(0)" id="edit" data-id="' . $row->id . '"
                class="btn btn-secondary text-white d-inline-flex align-items-center"
                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <i class="ti ti-edit fs-5 me-1"></i>Edit
            </a>

            <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '"
                class="btn btn-danger text-white d-inline-flex align-items-center"
                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <i class="ti ti-trash fs-5 me-1"></i>Hapus
            </a>';
                    } else {
                        return '';
                    }
                })
                ->toJson();
        }
        return view('backend.keuangan.kategori.index_new');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string', 'min:5', Rule::unique('keuangan_kategori')->where('shortname', multi_auth()->shortname)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $kategori = KategoriKeuangan::create([
            'shortname' => multi_auth()->shortname,
            'category' => $request->category,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $kategori,
        ]);
    }

    public function disable(Request $request)
    {
        $kategori = KategoriKeuangan::where('id', $request->id);
        $kategori->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Kategori Berhasil Dinonaktifkan',
            'data' => $kategori,
        ]);
    }

    public function enable(Request $request)
    {
        $kategori = KategoriKeuangan::where('id', $request->id);
        $kategori->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Kategori Berhasil Diaktifkan',
            'data' => $kategori,
        ]);
    }

    public function show(KategoriKeuangan $kategori)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $kategori,
        ]);
    }

    public function update(Request $request, KategoriKeuangan $kategori)
    {
        $kategori->update([
            'category' => $request->category,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $kategori,
        ]);
    }

    public function destroy($id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        $kategori->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
