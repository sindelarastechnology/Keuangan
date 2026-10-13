<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\Concerns\ValidasiMilikTenant;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input pembelian (PB).
 */
class StorePembelianRequest extends FormRequest
{
    use ValidasiMilikTenant;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'supplier_id' => ['required', $this->milikTenant('suppliers')],
            'aksi' => 'sometimes|in:posted,pending',
            'gudang_id' => ['nullable', $this->milikTenant('gudangs')],
            'metode_bayar' => 'required|in:tunai,kredit',
            'rekening_id' => [
                'required_if:metode_bayar,tunai',
                'nullable',
                $this->milikTenant('rekenings'),
            ],
            'diskon' => 'nullable|numeric|min:0',
            'diskon_tipe' => 'required|in:nominal,persen',
            'sync_harga' => 'nullable|in:1,0,on,true',
            'pajak_id' => ['nullable', $this->milikTenant('pajak')],
            'ongkir' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => ['required', $this->milikTenant('barang')],
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
        ];
    }
}
