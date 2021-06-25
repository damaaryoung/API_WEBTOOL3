<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class TeleAssignRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        $rules = [
            'no_hp' => 'between:9,13',
            'no_hp2' => 'between:9,13',
            'new_plafond' => 'required|numeric',
            'new_angsuran' => 'required|numeric',
            'new_tenor' => 'required|numeric',
            'baki_debet' => 'required|numeric'
        ];
        return $rules;
    }

    public function messages()
    {
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';
        $nominal     = ':attribute harus berupa nominal';
        $hp     = ':attribute nomor handphone harus 9 sampai 13 digit';

        return [
            // Tele Assign
            'no_hp.between:9,13'  => ':attribute nomor handphone harus 9 sampai 13 digit',
            'no_hp2.between:9,13'  => ':attribute nomor handphone harus 9 sampai 13 digit',
            'new_plafond.numeric'  => $nominal,
            'new_angsuran.numeric' => $nominal,
            'new_tenor.numeric'      => $integer,
            'baki_debet.numeric'        => $nominal
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
