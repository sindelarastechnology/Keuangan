<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input penjualan (PJ).
 */
class StorePenjualanRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'aksi' => 'sometimes|in:posted,pending',
            'gudang_id' => 'nullable|exists:gudangs,id',
            'metode_bayar' => 'required|in:tunai,kredit',
            'rekening_id' => 'required_if:metode_bayar,tunai|nullable|exists:rekenings,id',
            'diskon' => 'nullable|numeric|min:0',
            'diskon_tipe' => 'required|in:nominal,persen',
            'sync_harga' => 'nullable|in:1,0,on,true',
            'pajak_id' => 'nullable|exists:pajak,id',
            'ongkir' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
        ];
    }
}
