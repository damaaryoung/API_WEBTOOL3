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

class FaspinRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(3);

        if (!empty($single)) {
            $rules = [
                // Fasilitas Pinjaman
                'jenis_pinjaman'        => 'in:KONSUMTIF,MODAL KERJA,INVESTASI',
                'plafon_pinjaman'       => 'integer',
                'tenor_pinjaman'        => 'numeric'
            ];
        }else{
            $rules = [
                // Fasilitas Pinjaman
                'jenis_pinjaman'        => 'required|in:KONSUMTIF,MODAL KERJA,INVESTASI',
                'plafon_pinjaman'       => 'required|integer',
                'tenor_pinjaman'        => 'required|numeric'
            ];
        }

        return $rules;
    }

    public function messages(){
        return [
            // Fasilitas Pinjaman
            'jenis_pinjaman.required'  => ':attribute wajib diisi',
            'plafon_pinjaman.required' => ':attribute wajib diisi',
            'tenor_pinjaman.required'  => ':attribute wajib diisi',

            'jenis_pinjaman.in'        => ':attribute harus bertipe :values',
            'plafon_pinjaman.integer'  => ':attribute harus berupa bilangan bulat',
            'tenor_pinjaman.numeric'   => ':attribute harus berupa angka'
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
