<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\Concerns\ValidasiMilikTenant;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input mutasi bank / transfer rekening (MB).
 */
class StoreMutasiBankRequest extends FormRequest
{
    use ValidasiMilikTenant;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'rekening_asal_id' => ['required', $this->milikTenant('rekenings')],
            'rekening_tujuan_id' => [
                'required',
                $this->milikTenant('rekenings'),
                'different:rekening_asal_id',
            ],
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ];
    }
}
