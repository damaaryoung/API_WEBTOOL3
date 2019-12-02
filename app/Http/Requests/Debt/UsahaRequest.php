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
            'pemasukan_tunai'       => 'integer',
            'pemasukan_kredit'      => 'integer',
            'biaya_sewa'            => 'integer',
            'biaya_gaji_pegawai'    => 'integer',
            'biaya_belanja_brg'     => 'integer',
            'biaya_telp_listr_air'  => 'integer',
            'biaya_sampah_kemanan'  => 'integer',
            'biaya_kirim_barang'    => 'integer',
            'biaya_hutang_dagang'   => 'integer',
            'biaya_angsuran'        => 'integer',
            'biaya_lain_lain'       => 'integer'
            // 'laba_usaha'            => 'integer'
            // 'lamp_sku'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
            // 'lamp_pembukuan_usaha'  => 'mimes:jpg,jpeg,png,pdf|max:2048',
            // 'lamp_tempat_usaha'     => 'mimes:jpg,jpeg,png,pdf|max:2048'
        ];
    }

    public function messages(){
        return [
            'pemasukan_tunai.integer'     => ':attribute harus berupa bilangan bulat',
            'pemasukan_kredit.integer'    => ':attribute harus berupa bilangan bulat',
            'biaya_sewa.integer'          => ':attribute harus berupa bilangan bulat',
            'biaya_gaji_pegawai.integer'  => ':attribute harus berupa bilangan bulat',
            'biaya_belanja_brg.integer'   => ':attribute harus berupa bilangan bulat',
            'biaya_telp_listr_air.integer'=> ':attribute harus berupa bilangan bulat',
            'biaya_sampah_kemanan.integer'=> ':attribute harus berupa bilangan bulat',
            'biaya_kirim_barang.integer'  => ':attribute harus berupa bilangan bulat',
            'biaya_hutang_dagang.integer' => ':attribute harus berupa bilangan bulat',
            'biaya_angsuran.integer'      => ':attribute harus berupa bilangan bulat',
            'biaya_lain_lain.integer'     => ':attribute harus berupa bilangan bulat'
            // 'laba_usaha.integer'          => ':attribute harus berupa bilangan bulat',
            // 'lamp_sku.mimes'              => ':attribute harus bertipe :values',
            // 'lamp_pembukuan_usaha.mimes'  => ':attribute harus bertipe :values',
            // 'lamp_tempat_usaha.mimes'     => ':attribute harus bertipe :values',
            // 'lamp_sku.max'                => 'ukuran :attribute max :max kb',
            // 'lamp_pembukuan_usaha.max'    => 'ukuran :attribute max :max kb',
            // 'lamp_tempat_usaha.max'       => 'ukuran :attribute max :max kb'
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
