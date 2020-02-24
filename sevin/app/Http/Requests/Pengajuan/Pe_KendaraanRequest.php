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

class Pe_KendaraanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(4);

        if (empty($single)){
            $rules = [
                // Pemeriksaan Agunan Kendaraan
                'status_pengguna_ken.*' => 'in:PEMILIK,PENYEWA',
                'jml_roda_ken.*'        => 'integer',
                'km_ken.*'              => 'integer',
            ];
        }else{
            $rules = [
                // Pemeriksaan Agunan Kendaraan
                'status_pengguna_ken' => 'in:PEMILIK,PENYEWA',
                'jml_roda_ken'        => 'integer',
                'km_ken'              => 'integer',
            ];
        }

        return $rules;
    }

    public function messages()
    {
            return [
                // Pemeriksaan Agunan Kendaraan
                'status_pengguna_ken.*.in'  => ':attribute harus salah satu dari jenis berikut :values',
                'jml_roda_ken.*.integer'    => ':attribute harus berupa angka / bilangan bulat',
                'km_ken.*.integer'          => ':attribute harus berupa angka / bilangan bulat',

                // Pemeriksaan Agunan Kendaraan
                'status_pengguna_ken.in'  => ':attribute harus salah satu dari jenis berikut :values',
                'jml_roda_ken.integer'    => ':attribute harus berupa angka / bilangan bulat',
                'km_ken.integer'          => ':attribute harus berupa angka / bilangan bulat'
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
