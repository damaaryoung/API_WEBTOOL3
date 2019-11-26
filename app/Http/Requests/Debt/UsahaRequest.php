<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class UsahaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_calon_debitur'      => 'numeric',
            'id_provinsi'           => 'numeric',
            'id_kabupaten'          => 'numeric',
            'id_kecamatan'          => 'numeric',
            'id_kelurahan'          => 'numeric',
            'rt'                    => 'numeric',
            'rw'                    => 'numeric',
            'pemasukan_tunai'       => 'numeric',
            'pemasukan_kredit'      => 'numeric',
            'biaya_sewa'            => 'numeric',
            'biaya_gaji_pegawai'    => 'numeric',
            'biaya_belanja_brg'     => 'numeric',
            'biaya_telp_listr_air'  => 'numeric',
            'biaya_sampah_kemanan'  => 'numeric',
            'biaya_kirim_barang'    => 'numeric',
            'biaya_hutang_dagang'   => 'numeric',
            'biaya_angsuran'        => 'numeric',
            'biaya_lain_lain'       => 'numeric',
            'laba_usaha'            => 'numeric',
            'lamp_surat_ket_usaha'  => 'mimes:jpg,jpeg,png,pdf',
            'lamp_pembukuan_usaha'  => 'mimes:jpg,jpeg,png,pdf',
            'lamp_rek_tabungan'     => 'mimes:jpg,jpeg,png,pdf',
            'lamp_persetujuan_ideb' => 'mimes:jpg,jpeg,png,pdf',
            'lamp_tempat_usaha'     => 'mimes:jpg,jpeg,png,pdf',
            'tgl_mulai_usaha'       => 'date_format:d-m-Y',
            'telp_tempat_usaha'     => 'numeric'
        ];
    }

    public function messages(){
        return [
            'id_calon_debitur.numeric'    => ':attribute harus berupa angka',
            'id_provinsi.numeric'         => ':attribute harus berupa angka',
            'id_kabupaten.numeric'        => ':attribute harus berupa angka',
            'id_kecamatan.numeric'        => ':attribute harus berupa angka',
            'id_kelurahan.numeric'        => ':attribute harus berupa angka',
            'rt.numeric'                  => ':attribute harus berupa angka',
            'rw.numeric'                  => ':attribute harus berupa angka',
            'pemasukan_tunai.numeric'     => ':attribute harus berupa angka',
            'pemasukan_kredit.numeric'    => ':attribute harus berupa angka',
            'biaya_sewa.numeric'          => ':attribute harus berupa angka',
            'biaya_gaji_pegawai.numeric'  => ':attribute harus berupa angka',
            'biaya_belanja_brg.numeric'   => ':attribute harus berupa angka',
            'biaya_telp_listr_air.numeric'=> ':attribute harus berupa angka',
            'biaya_sampah_kemanan.numeric'=> ':attribute harus berupa angka',
            'biaya_kirim_barang.numeric'  => ':attribute harus berupa angka',
            'biaya_hutang_dagang.numeric' => ':attribute harus berupa angka',
            'biaya_angsuran.numeric'      => ':attribute harus berupa angka',
            'biaya_lain_lain.numeric'     => ':attribute harus berupa angka',
            'laba_usaha.numeric'          => ':attribute harus berupa angka',
            'lamp_surat_ket_usaha.mimes'  => ':attribute harus bertipe :values',
            'lamp_pembukuan_usaha.mimes'  => ':attribute harus bertipe :values',
            'lamp_rek_tabungan.mimes'     => ':attribute harus bertipe :values',
            'lamp_persetujuan_ideb'       => ':attribute harus bertipe :values',
            'lamp_tempat_usaha.mimes'     => ':attribute harus bertipe :values',
            'tgl_mulai_usaha.date_format' => ':attribute harus berupa angka dengan format :format',
            'telp_tempat_usaha.numeric'   => ':attribute harus berupa angka'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            // response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
