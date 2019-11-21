<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class DebtPasanganRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_kelamin_pas' => 'in:L,P',
            'no_ktp_pas'        => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp',
            'no_ktp_kk_pas'     => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk',
            'no_kk_pas'         => 'digits:16|unique:web.pasangan_calon_debitur,no_kk',
            'no_npwp_pas'       => 'digits:15|unique:web.pasangan_calon_debitur,no_npwp',
            'tgl_lahir_pas'     => 'date_format:d-m-Y',
            'no_telp_pas'       => 'between:11,13|unique:web.pasangan_calon_debitur,no_ktp',
            'lamp_ktp_pas'      => 'mimes:jpg,jpeg,png,pdf',
            'lamp_kk_pas'       => 'mimes:jpg,jpeg,png,pdf'
        ];
    }

    public function messages(){
        return [
            'jenis_kelamin_pas.in'      => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp_pas.digits'         => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_pas.unique'         => ':attribute telah ada yang menggunakan',
            'no_ktp_kk_pas.digits'      => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk_pas.unique'      => ':attribute telah ada yang menggunakan',
            'no_kk_pas.digits'          => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk_pas.unique'          => ':attribute telah ada yang menggunakan',
            'no_npwp_pas.digits'        => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp_pas.unique'        => ':attribute telah ada yang menggunakan',
            'id_provinsi'               => ':attribute harus berupa angka',
            'id_kabupaten'              => ':attribute harus berupa angka',
            'id_kecamatan'              => ':attribute harus berupa angka',
            'id_kelurahan'              => ':attribute harus berupa angka',
            'rt'                        => ':attribute harus berupa angka',
            'rw'                        => ':attribute harus berupa angka',
            'tgl_lahir_pas.date_format' => ':attribute harus berupa angka dengan format :format',
            'no_telp_pas.between'       => ':attribute harus berada diantara :min - :max.',
            'no_telp_pas.unique'        => ':attribute telah ada yang menggunakan',
            'lamp_ktp_pas.mimes'        => ':attribute harus bertipe :values',
            'lamp_kk_pas.mimes'         => ':attribute harus bertipe :values'
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
