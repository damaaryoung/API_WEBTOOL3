<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class AguTaReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_calon_debitur'     => 'numeric',
            'id_kelurahan'         => 'numeric',
            'rt'                   => 'numeric',
            'rw'                   => 'numeric',
            'luas_tanah'           => 'numeric',
            'luas_bangunan'        => 'numeric',
            'tgl_ukur_sertifikat'  => 'date_format:d-m-Y',
            'masa_berlaku_shgb'    => 'date_format:d-m-Y',
            'lam_imb'              => 'mimes:jpg,jpeg,png,pdf',
            'njop'                 => 'numeric',
            'nop'                  => 'numeric',
            'lamp_agunan_depan'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_kanan'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_kiri'     => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_belakang' => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_dalam'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_sertifikat'      => 'mimes:jpg,jpeg,png,pdf',
            'lamp_imb'             => 'mimes:jpg,jpeg,png,pdf',
            'lamp_pbb'             => 'mimes:jpg,jpeg,png,pdf'
        ];
    }

    public function messages(){
        return [
            'id_calon_debitur.numeric'        => ':attribute harus berupa angka',
            'id_kelurahan.numeric'            => ':attribute harus berupa angka',
            'rt.numeric'                      => ':attribute harus berupa angka',
            'rw.numeric'                      => ':attribute harus berupa angka',
            'luas_tanah.numeric'              => ':attribute harus berupa angka',
            'luas_bangunan.numeric'           => ':attribute harus berupa angka',
            'tgl_ukur_sertifikat.date_format' => ':attribute harus berupa angka dengan format :format',
            'masa_berlaku_shgb.date_format'   => ':attribute harus berupa angka dengan format :format',
            'lam_imb.mimes'                   => ':attribute harus bertipe :values',
            'njop.numeric'                    => ':attribute harus berupa angka',
            'nop.numeric'                     => ':attribute harus berupa angka',
            'lamp_agunan_depan.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kanan.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kiri.mimes'          => ':attribute harus bertipe :values',
            'lamp_agunan_belakang.mimes'      => ':attribute harus bertipe :values',
            'lamp_agunan_dalam.mimes'         => ':attribute harus bertipe :values',
            'lamp_sertifikat.mimes'           => ':attribute harus bertipe :values',
            'lamp_imb.mimes'                  => ':attribute harus bertipe :values',
            'lamp_pbb.mimes'                  => ':attribute harus bertipe :values',
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
