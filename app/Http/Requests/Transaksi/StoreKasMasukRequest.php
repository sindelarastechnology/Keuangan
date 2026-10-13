<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\Concerns\ValidasiMilikTenant;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input kas masuk (KM).
 */
class StoreKasMasukRequest extends FormRequest
{
    use ValidasiMilikTenant;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'rekening_id' => ['required', $this->milikTenant('rekenings')],
            'customer_id' => ['nullable', $this->milikTenant('customers')],
            'keterangan' => 'nullable|string',
            'pajak_id' => ['nullable', $this->milikTenant('pajak')],
            'items' => 'required|array|min:1',
            'items.*.akun_id' => ['required', $this->milikTenant('akun_perkiraan')],
            'items.*.keterangan' => 'nullable|string',
            'items.*.nominal' => 'required|numeric|min:0',
        ];
    }
}
