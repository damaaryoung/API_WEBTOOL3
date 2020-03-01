<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class LogTabReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            'no_rekening'             => 'numeric',
            'penghasilan_per_tahun'   => 'integer',
            'pemasukan_per_bulan'     => 'in:A,B,C,D,E',
            'frek_trans_pemasukan'    => 'in:A,B,C,D,E',
            'pengeluaran_per_bulan'   => 'in:A,B,C,D,E',
            'frek_trans_pengeluaran'  => 'in:A,B,C,D,E',
            // 'sumber_dana_setoran'     =>
            'tujuan_pengeluaran_dana' => 'in:KONSUMTIF,MODAL,INVESTASI',
        ];
    }

    public function messages(){
        $in          = ':attribute harus bertipe :values';
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';
        $numeric     = ':attribute harus berupa angka';

        return [
            'no_rekening.integer'           => $numeric,
            'penghasilan_per_tahun.integer' => $integer,
            'pemasukan_per_bulan.in'        => ':attribute harus salah satu dari jenis berikut :values, A untuk < 2jt, B untuk 2jt - 5jt, C untuk 5jt - 10jt, D untuk > 10jt',
            'frek_trans_pemasukan.in'       => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. lebih dari 10 kali',
            'pengeluaran_per_bulan.in'       => ':attribute harus salah satu dari jenis berikut :values, A untuk < 2jt, B untuk 2jt - 5jt, C untuk 5jt - 10jt, D untuk > 10jt',            'frek_trans_pengeluaran.in'     => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. 3.1 - 15 kali, D untuk frek. lebih dari 15 kali',
            'tujuan_pengeluaran_dana.in'    => $in,
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
