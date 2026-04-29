<?php

namespace App\Http\Controllers;

use App\Exports\TeacherStatementExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherStatementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function export(Request $request)
    {
        if (!isInstructor()) {
            abort(403, 'Permission Denied');
        }

        $filename = 'teacher-statement-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new TeacherStatementExport((int) auth()->id()), $filename);
    }
}

