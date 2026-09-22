<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\ArusKasExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ArusKasController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::arusKas($dari, $sampai);

        return view('laporan.arus-kas', compact('data', 'dari', 'sampai'));
    }

    public function pdf(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::arusKas($dari, $sampai);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');

        $pdf = Pdf::loadView('laporan.pdf.arus-kas', compact('data', 'dari', 'sampai', 'namaPerusahaan'));

        return $pdf->download('arus-kas-'.$dari.'-sd-'.$sampai.'.pdf');
    }

    public function excel(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new ArusKasExport($dari, $sampai), 'arus-kas-'.$dari.'-sd-'.$sampai.'.xlsx');
    }

    public function csv(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new ArusKasExport($dari, $sampai), 'arus-kas-'.$dari.'-sd-'.$sampai.'.csv');
    }
}
