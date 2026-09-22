<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\NeracaSaldoExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NeracaSaldoController extends Controller
{
    public function index(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::neracaSaldo($sampai);

        return view('laporan.neraca-saldo', compact('data', 'sampai'));
    }

    public function pdf(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        $data = ReportService::neracaSaldo($sampai);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');

        $pdf = Pdf::loadView('laporan.pdf.neraca-saldo', compact('data', 'sampai', 'namaPerusahaan'));

        return $pdf->download('neraca-saldo-'.$sampai.'.pdf');
    }

    public function excel(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new NeracaSaldoExport($sampai), 'neraca-saldo-'.$sampai.'.xlsx');
    }

    public function csv(Request $request)
    {
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->toDateString();

        return Excel::download(new NeracaSaldoExport($sampai), 'neraca-saldo-'.$sampai.'.csv');
    }
}
