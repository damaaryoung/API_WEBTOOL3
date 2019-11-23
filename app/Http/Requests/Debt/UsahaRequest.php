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
            'tunai'                 => 'numeric',
            'kredit'                => 'numeric',
            'biaya_sewa'            => 'numeric',
            'gaji_pegawai'          => 'numeric',
            'belanja_brg'           => 'numeric',
            'telp-listr-air'        => 'numeric',
            'sampah-kemanan'        => 'numeric',
            'biaya_ongkir'          => 'numeric',
            'hutang_dagang'         => 'numeric',
            'lain_lain'             => 'numeric',
            'laba'                  => 'numeric',
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
            'tunai.numeric'               => ':attribute harus berupa angka',
            'kredit.numeric'              => ':attribute harus berupa angka',
            'biaya_sewa.numeric'          => ':attribute harus berupa angka',
            'gaji_pegawai.numeric'        => ':attribute harus berupa angka',
            'belanja_brg.numeric'         => ':attribute harus berupa angka',
            'telp-listr-air.numeric'      => ':attribute harus berupa angka',
            'sampah-kemanan.numeric'      => ':attribute harus berupa angka',
            'biaya_ongkir.numeric'        => ':attribute harus berupa angka',
            'hutang_dagang.numeric'       => ':attribute harus berupa angka',
            'lain_lain.numeric'           => ':attribute harus berupa angka',
            'laba.numeric'                => ':attribute harus berupa angka',
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
