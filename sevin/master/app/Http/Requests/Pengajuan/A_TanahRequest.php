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

class A_TanahRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(4);

        if (empty($single)) {

            $rules = [
                // Agunan Tanah
                'tipe_lokasi_agunan.*'  => 'in:PERUM,BIASA',
                'rt_agunan.*'           => 'numeric',
                'rw_agunan.*'           => 'numeric',
                'luas_tanah.*'          => 'numeric',
                'luas_bangunan.*'       => 'numeric',
                'jenis_sertifikat.*'    => 'in:SHM,SHGB',
                // 'tgl_ukur_sertifikat.*' => 'date_format:d-m-Y',
                'tgl_berlaku_shgb.*'    => 'date_format:d-m-Y',
                'lamp_agunan_depan.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kanan.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kiri.*'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_belakang.*'=> 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_dalam.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048'
            ];
        }else{
            $rules = [
               // Agunan Tanah
                'tipe_lokasi_agunan'  => 'in:PERUM,BIASA',
                'rt_agunan'           => 'numeric',
                'rw_agunan'           => 'numeric',
                'luas_tanah'          => 'numeric',
                'luas_bangunan'       => 'numeric',
                'jenis_sertifikat'    => 'in:SHM,SHGB',
                // 'tgl_ukur_sertifikat' => 'date_format:d-m-Y',
                'tgl_berlaku_shgb'    => 'date_format:d-m-Y',
                'lamp_agunan_depan'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kanan'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kiri'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_belakang'=> 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_dalam'   => 'mimes:jpg,jpeg,png,pdf|max:2048'
            ];
        }

        return $rules;
    }

    public function messages()
    {
            return  [
                // Agunan Tanah Array
                'tipe_lokasi_agunan.*.in'           => ':attribute harus salah satu dari jenis berikut :values',
                'rt_agunan.*.numeric'               => ':attribute harus berupa angka',
                'rw_agunan.*.numeric'               => ':attribute harus berupa angka',
                'luas_tanah.*.numeric'              => ':attribute harus berupa angka',
                'luas_bangunan.*.numeric'           => ':attribute harus berupa angka',
                'jenis_sertifikat.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
                // 'tgl_ukur_sertifikat.*.date_format' => ':attribute harus berupa angka dengan format :format',
                'tgl_berlaku_shgb.*.date_format'    => ':attribute harus berupa angka dengan format :format',

                'lamp_agunan_depan.*.mimes'         => ':attribute harus bertipe :values',
                'lamp_agunan_kanan.*.mimes'         => ':attribute harus bertipe :values',
                'lamp_agunan_kiri.*.mimes'          => ':attribute harus bertipe :values',
                'lamp_agunan_belakang.*.mimes'      => ':attribute harus bertipe :values',
                'lamp_agunan_dalam.*.mimes'         => ':attribute harus bertipe :values',

                'lamp_agunan_depan.*.max'           => 'ukuran :attribute max :max kb',
                'lamp_agunan_kanan.*.max'           => 'ukuran :attribute max :max kb',
                'lamp_agunan_kiri.*.max'            => 'ukuran :attribute max :max kb',
                'lamp_agunan_belakang.*.max'        => 'ukuran :attribute max :max kb',
                'lamp_agunan_dalam.*.max'           => 'ukuran :attribute max :max kb',

                // Agunan Tanah
                'tipe_lokasi_agunan.in'           => ':attribute harus salah satu dari jenis berikut :values',
                'rt_agunan.numeric'               => ':attribute harus berupa angka',
                'rw_agunan.numeric'               => ':attribute harus berupa angka',
                'luas_tanah.numeric'              => ':attribute harus berupa angka',
                'luas_bangunan.numeric'           => ':attribute harus berupa angka',
                'jenis_sertifikat.in'             => ':attribute harus salah satu dari jenis berikut :values',
                // 'tgl_ukur_sertifikat.date_format' => ':attribute harus berupa angka dengan format :format',
                'tgl_berlaku_shgb.date_format'    => ':attribute harus berupa angka dengan format :format',

                'lamp_agunan_depan.mimes'         => ':attribute harus bertipe :values',
                'lamp_agunan_kanan.mimes'         => ':attribute harus bertipe :values',
                'lamp_agunan_kiri.mimes'          => ':attribute harus bertipe :values',
                'lamp_agunan_belakang.mimes'      => ':attribute harus bertipe :values',
                'lamp_agunan_dalam.mimes'         => ':attribute harus bertipe :values',

                'lamp_agunan_depan.max'           => 'ukuran :attribute max :max kb',
                'lamp_agunan_kanan.max'           => 'ukuran :attribute max :max kb',
                'lamp_agunan_kiri.max'            => 'ukuran :attribute max :max kb',
                'lamp_agunan_belakang.max'        => 'ukuran :attribute max :max kb',
                'lamp_agunan_dalam.max'           => 'ukuran :attribute max :max kb'
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
