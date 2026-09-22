<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\LabaRugiExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::labaRugi($dari, $sampai);

        return view('laporan.laba-rugi', compact('data', 'dari', 'sampai'));
    }

    public function pdf(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::labaRugi($dari, $sampai);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');

        $pdf = Pdf::loadView('laporan.pdf.laba-rugi', compact('data', 'dari', 'sampai', 'namaPerusahaan'));

        return $pdf->download('laba-rugi-'.$dari.'-sd-'.$sampai.'.pdf');
    }

    public function excel(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new LabaRugiExport($dari, $sampai), 'laba-rugi-'.$dari.'-sd-'.$sampai.'.xlsx');
    }

    public function csv(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new LabaRugiExport($dari, $sampai), 'laba-rugi-'.$dari.'-sd-'.$sampai.'.csv');
    }
}
