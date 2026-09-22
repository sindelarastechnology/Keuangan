<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input kas keluar (KK).
 */
class StoreKasKeluarRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan' => 'nullable|string',
            'pajak_id' => 'nullable|exists:pajak,id',
            'items' => 'required|array|min:1',
            'items.*.akun_id' => 'required|exists:akun_perkiraan,id',
            'items.*.keterangan' => 'nullable|string',
            'items.*.nominal' => 'required|numeric|min:0',
        ];
    }
}
