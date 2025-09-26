<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\User;
use App\Models\HotspotReseller;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query()->where('id_group', $request->user()->id_group);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // All returned HTML should not include untrusted user input
                    // The user IDs and statuses are controlled by our app, so they're considered safe.
                    if ($row->status === 0) {
                        return '<a href="javascript:void(0)" id="edit"
                        data-id="' . e($row->id) . '" class="badge b-ln-height badge-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" id="enable"
                        data-id="' . e($row->id) . '" class="badge b-ln-height badge-success">
                            <i class="fas fa-user-check"></i>
                        </a>';
                    } elseif ($row->status === 2) {
                        return '<a href="javascript:void(0)" id="edit"
                        data-id="' . e($row->id) . '" class="badge b-ln-height badge-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" id="activate"
                        data-id_group="' . e($row->id_group) . '"
                        data-username="' . e($row->username) . '"
                        data-license_id="' . e($row->order_license_id) . '"
                        data-next_due="' . e($row->next_due) . '" class="btn btn-sm btn-success">
                            <i class="fas fa-user-check"></i>&nbsp;Activate
                        </a>';
                    } else {
                        return '<a href="javascript:void(0)" id="edit"
                        data-id="' . e($row->id) . '" class="badge b-ln-height badge-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" id="disable"
                        data-id="' . e($row->id) . '" class="badge b-ln-height badge-danger">
                            <i class="fas fa-user-slash"></i>
                        </a>';
                    }
                })
                ->editColumn('name', function ($row) {
                    return e($row->name);
                })
                ->editColumn('email', function ($row) {
                    return e($row->email);
                })
                ->editColumn('username', function ($row) {
                    return e($row->username);
                })
                ->editColumn('role', function ($row) {
                    return e($row->role);
                })
                ->editColumn('status', function ($row) {
                    return $row->status; // Will be rendered in JS safely
                })
                ->toJson();
        }

        $resellers = HotspotReseller::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->select('name', 'wa', 'id')
            ->get();

        $totalTeknisi = User::where('id_group', $request->user()->id_group)
            ->where('role', 'Teknisi')
            ->count();
        $totalHelpdesk = User::where('id_group', $request->user()->id_group)
            ->where('role', 'Helpdesk')
            ->count();
        $totalKasir = User::where('id_group', $request->user()->id_group)
            ->where('role', 'Kasir')
            ->count();
        $totalReseller = User::where('id_group', $request->user()->id_group)
            ->where('role', 'Reseller')
            ->count();
        $totalPelanggan = 0;

        $total = $totalTeknisi + $totalHelpdesk + $totalKasir + $totalReseller + $totalPelanggan;

        return view('settings.users.index', compact('resellers', 'totalTeknisi', 'totalHelpdesk', 'totalKasir', 'totalReseller', 'totalPelanggan', 'total'));
    }

    public function store(Request $request)
    {
        // Authorization check to ensure the user can create users
        // $this->authorize('create', User::class);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:Helpdesk,Teknisi,Kasir,Reseller'],
            'username' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9]+$/', 'min:5', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'whatsapp' => ['required', 'string', 'min:10', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'max:255'],
            'reseller_id' => ['nullable', 'exists:hotspot_resellers,id']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'id_group' => $request->user()->id_group,
            'shortname' => $request->user()->shortname,
            'name' => $request->name,
            'role' => $request->role,
            'reseller_id' => $request->role === 'Reseller' ? $request->reseller_id : null,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => 1,
            'license_id' => $request->user()->license_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
        ]);
    }

    public function show(User $user)
    {
        // $this->authorize('view', $user);

        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // $this->authorize('update', $user);

        // Validate update inputs
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'in:Helpdesk,Teknisi,Kasir,Reseller'],
            'role_admin' => ['nullable', 'in:Admin,Helpdesk,Teknisi,Kasir,Reseller'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'whatsapp' => ['required', 'string', 'min:10', 'max:255', 'unique:users,whatsapp,' . $user->id],
            'username' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9]+$/', 'min:5', 'max:255', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:5', 'max:255'],
            'reseller_id' => ['nullable', 'exists:hotspot_resellers,id']
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        if ($user->id_group !== $request->user()->id_group) {
            return response()->json([
                'error' => 'Unauthorized action.',
            ], 403);
        }

        $roleToUpdate = $request->role_admin ?? $request->role;

        $updateData = [
            'name' => $request->name,
            'role' => $roleToUpdate ?? $user->role,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'username' => $request->username,
            'status' => 1,
        ];

        if ($roleToUpdate === 'Reseller') {
            $updateData['reseller_id'] = $request->reseller_id;
        } else {
            $updateData['reseller_id'] = null;
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
        ]);
    }

    public function disable(Request $request)
    {
        // $this->authorize('update', [User::class, $request->id]);

        $user = User::where('id', $request->id)->firstOrFail();
        $user->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dinonaktifkan',
            'data' => $user,
        ]);
    }

    public function enable(Request $request)
    {
        // $this->authorize('update', [User::class, $request->id]);

        $user = User::where('id', $request->id)->firstOrFail();
        $user->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Diaktifkan',
            'data' => $user,
        ]);
    }

    public function activate(Request $request)
    {
        // $this->authorize('updateLicense', [User::class, $request->id_group]);

        $user = User::where('id_group', $request->id_group)->firstOrFail();
        $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)
            ->addMonthsWithNoOverflow(1)
            ->toDateString();
        $user->update([
            'status' => 1,
            'license_id' => $request->license_id,
            'next_due' => $next_due,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'License Berhasil Diaktifkan!',
            'data' => $user,
        ]);
    }

    public function renew(Request $request)
    {
        // $this->authorize('renewLicense', [User::class, $request->id_group]);

        $user = User::where('id_group', $request->id_group)->firstOrFail();
        $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)
            ->addMonthsWithNoOverflow(1)
            ->toDateString();
        $user->update([
            'status' => 1,
            'next_due' => $next_due,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'License Berhasil Direnew!',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        // $this->authorize('delete', [User::class, $id]);

        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user account.'
            ], 500);
        }
    }
}
