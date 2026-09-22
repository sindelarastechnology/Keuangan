<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\PenyusutanExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PenyusutanController extends Controller
{
    public function index(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $data = ReportService::penyusutan($sampai);

        return view('laporan.penyusutan', compact('data', 'sampai'));
    }

    public function pdf(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $data = ReportService::penyusutan($sampai);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');

        $pdf = Pdf::loadView('laporan.pdf.penyusutan', compact('data', 'sampai', 'namaPerusahaan'));

        return $pdf->download('laporan-penyusutan-'.$sampai.'.pdf');
    }

    public function excel(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        return Excel::download(new PenyusutanExport($sampai), 'laporan-penyusutan-'.$sampai.'.xlsx');
    }

    public function csv(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        return Excel::download(new PenyusutanExport($sampai), 'laporan-penyusutan-'.$sampai.'.csv');
    }
}
