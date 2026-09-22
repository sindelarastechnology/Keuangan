---
paths:
  - 'resources/views/components/*.blade.php'
  - resources/views/components/modal.blade.php
  - resources/views/components/loading-button.blade.php
---

# Components

## Pisahkan binding Blade (:) vs Alpine (x-bind:) pada komponen
Pada TAG KOMPONEN (mis. <x-button>), atribut ':attr="expr"' dikompilasi Blade sebagai ekspresi PHP — jangan dipakai untuk variabel Alpine. Untuk state Alpine gunakan 'x-bind:attr="expr"' (karakter x-bind aman dikompilasi sebagai atribut polos lalu ditangani Alpine). Trap nyata: <x-loading-button> dulu pakai ':disabled="loading"' dan melempar 'Undefined constant loading'.

## x-modal memutus Alpine scope parent
Komponen x-modal punya x-data sendiri di root div-nya. Slot yang dirender di dalam modal TIDAK bisa mengakses x-data dari parent (misal barangForm, jasaForm). Jangan pakai x-model, x-show, atau memanggil method parent langsung di dalam slot modal. Solusi: beri slot x-data lokal sendiri (misal x-data="{ err: '' }") dan komunikasikan ke parent via window custom events (misal window.dispatchEvent(new CustomEvent('nama-event', { detail: { nilai, setErr } }))) yang di-listen di init() parent.

## loading-button tidak boleh disable tombol submit
Jangan gunakan x-bind:disabled atau $el.disabled = true pada tombol type=submit. Meng-disable tombol submit setelah click menyebabkan browser membatalkan form submission. Loading state hanya untuk tampilan visual (spinner + teks). Jangan pakai statement var/const/let di dalam ekspresi Alpine x-on:click inline.
