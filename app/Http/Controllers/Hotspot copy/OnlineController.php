<?php

namespace App\Http\Controllers\Hotspot;

use App\DataTables\Admin\Hotspot\OnlineDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnlineController extends Controller
{
    public function index(OnlineDataTable $dataTable)
    {
        return $dataTable->render('hotspot.online.index');
    }
}
