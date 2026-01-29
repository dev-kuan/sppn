<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInmateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no_registrasi' => 'required|string|max:255|unique:inmates,no_registrasi',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'agama' => 'required|string|max:100',
            'tingkat_pendidikan' => 'nullable|string|max:100',
            'pekerjaan_terakhir' => 'nullable|string|max:100',
            'lama_pidana_bulan' => 'required|integer|min:1',
            'sisa_pidana_bulan' => 'required|integer|min:0',
            'jumlah_residivisme' => 'nullable|integer|min:0',
            'catatan_kesehatan' => 'nullable|string',
            'pelatihan' => 'nullable|string|max:255',
            'program_kerja' => 'nullable|string|max:255',
            'crime_type_id' => 'required|exists:crime_types,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_bebas' => 'nullable|date|after:tanggal_masuk',
        ];
    }
    public function messages(): array
    {
        return [
            'no_registrasi.unique' => 'Nomor registrasi sudah digunakan.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'tanggal_bebas.after' => 'Tanggal bebas harus setelah tanggal masuk.',
        ];
    }
}
