<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class DebtPenjaminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'no_ktp_pen'            => 'digits:16|unique:web.penjamin_calon_debitur,no_ktp',
            'no_npwp_pen'           => 'digits:15|unique:web.penjamin_calon_debitur,no_npwp',
            'tgl_lahir_pen'         => 'date_format:d-m-Y',
            'jenis_kelamin_pen'     => 'in:L,P',
            'no_telp_pen'           => 'between:11,13|unique:web.penjamin_calon_debitur,no_telp',
            'lamp_ktp_pen'          => 'mimes:jpg,jpeg,png,pdf',
            'lamp_ktp_pasangan_pen' => 'mimes:jpg,jpeg,png,pdf',
            'lamp_kk_pen'           => 'mimes:jpg,jpeg,png,pdf',
            'lamp_buku_nikah_pen'   => 'mimes:jpg,jpeg,png,pdf',
            'pendapatan_pen'        => 'numeric'
        ];
    }

    public function messages(){
        return [
            'no_ktp_pen.digits'             => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_pen.unique'             => ':attribute telah ada yang menggunakan',
            'no_npwp_pen.digits'            => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp_pen.unique'            => ':attribute telah ada yang menggunakan',
            'tgl_lahir_pen.date_format'     => ':attribute harus berupa angka dengan format :format',
            'jenis_kelamin_pen.in'          => ':attribute harus salah satu dari jenis berikut :values',
            'no_telp_pen.between'           => ':attribute harus berada diantara :min - :max.',
            'no_telp_pen.unique'            => ':attribute telah ada yang menggunakan',
            'lamp_ktp_pen.mimes'            => ':attribute harus bertipe :values',
            'lamp_ktp_pasangan_pen.mimes'   => ':attribute harus bertipe :values',
            'lamp_kk_pen.mimes'             => ':attribute harus bertipe :values',
            'lamp_buku_nikah_pen.mimes'     => ':attribute harus bertipe :values',
            'pendapatan_pen.numeric'        => ':attribute harus berupa angka'
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
                "errors"  => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
