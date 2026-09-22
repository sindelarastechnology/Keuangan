<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input mutasi bank / transfer rekening (MB).
 */
class StoreMutasiBankRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'rekening_asal_id' => 'required|exists:rekenings,id',
            'rekening_tujuan_id' => 'required|exists:rekenings,id|different:rekening_asal_id',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ];
    }
}
