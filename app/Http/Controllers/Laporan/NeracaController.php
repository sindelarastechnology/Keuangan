<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\NeracaExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $data = ReportService::neraca($sampai);

        return view('laporan.neraca', compact('data', 'sampai'));
    }

    public function pdf(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $data = ReportService::neraca($sampai);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');

        $pdf = Pdf::loadView('laporan.pdf.neraca', compact('data', 'sampai', 'namaPerusahaan'));

        return $pdf->download('neraca-'.$sampai.'.pdf');
    }

    public function excel(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        return Excel::download(new NeracaExport($sampai), 'neraca-'.$sampai.'.xlsx');
    }

    public function csv(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        return Excel::download(new NeracaExport($sampai), 'neraca-'.$sampai.'.csv');
    }
}
