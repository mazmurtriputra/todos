<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Exports\TodoExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TodoExportController extends Controller
{
    public function export(Request $request): BinaryFileResponse
    {
        return Excel::download(new TodoExport($request), 'todos.xlsx');
    }
}
