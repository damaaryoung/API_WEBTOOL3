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

class DebUsahaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        return [
            // Usaha
            'pemasukan_tunai'      => 'integer',
            'pemasukan_kredit'     => 'integer',
            'biaya_sewa'           => 'integer',
            'biaya_gaji_pegawai'   => 'integer',
            'biaya_belanja_brg'    => 'integer',
            'biaya_telp_listr_air' => 'integer',
            'biaya_sampah_kemanan' => 'integer',
            'biaya_kirim_barang'   => 'integer',
            'biaya_hutang_dagang'  => 'integer',
            'biaya_angsuran'       => 'integer',
            'biaya_lain_lain'      => 'integer'
        ];
    }

    public function messages(){
        return [
            // Usaha
            'pemasukan_tunai.integer'      => ':attribute harus berupa angka / bilangan bulat',
            'pemasukan_kredit.integer'     => ':attribute harus berupa angka / bilangan bulat',
            'biaya_sewa.integer'           => ':attribute harus berupa angka / bilangan bulat',
            'biaya_gaji_pegawai.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'biaya_belanja_brg.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'biaya_telp_listr_air.integer' => ':attribute harus berupa angka / bilangan bulat',
            'biaya_sampah_kemanan.integer' => ':attribute harus berupa angka / bilangan bulat',
            'biaya_kirim_barang.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'biaya_hutang_dagang.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'biaya_angsuran.integer'       => ':attribute harus berupa angka / bilangan bulat',
            'biaya_lain_lain.integer'      => ':attribute harus berupa angka / bilangan bulat'
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
