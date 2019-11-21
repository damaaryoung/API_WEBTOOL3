<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class DebtRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_kelamin'         => 'in:L,P',
            'status_nikah'          => 'in:SINGLE,NIKAH,CERAI',
            'no_ktp'                => 'digits:16|unique:web.calon_debitur,no_ktp',
            'no_ktp_kk'             => 'digits:16|unique:web.calon_debitur,no_ktp_kk',
            'no_kk'                 => 'digits:16|unique:web.calon_debitur,no_kk',
            'no_npwp'               => 'digits:15|unique:web.calon_debitur,no_npwp',
            'tgl_lahir'             => 'date_format:d-m-Y',
            'agama'                 => 'in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
            'rt_ktp'                => 'numeric',
            'rw_ktp'                => 'numeric',
            'id_provinsi_ktp'       => 'numeric',
            'id_kabupaten_ktp'      => 'numeric',
            'id_kecamatan_ktp'      => 'numeric',
            'id_kelurahan_ktp'      => 'numeric',
            'rt_domisili'           => 'numeric',
            'rw_domisili'           => 'numeric',
            'id_provinsi_domisili'  => 'numeric',
            'id_kabupaten_domisili' => 'numeric',
            'id_kecamatan_domisili' => 'numeric',
            'id_kelurahan_domisili' => 'numeric',
            'jumlah_tanggungan'     => 'numeric',
            'no_telp'               => 'between:11,13|unique:web.calon_debitur,no_telp',
            'no_hp'                 => 'between:11,13|unique:web.calon_debitur,no_telp',
            'tgl_lahir_anak1'       => 'date_format:d-m-Y',
            'tgl_lahir_anak2'       => 'date_format:d-m-Y',
            'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf',
            'tinggi_badan'          => 'numeric',
            'berat_badan'           => 'numeric',
            'lamp_buku_tabungan'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf',
            'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf',
            'jenis_jaminan'         => 'in:TANAH,KENDARAAN'
        ];
    }

    public function messages(){
        return [
            'jenis_kelamin.in'               => ':attribute harus salah satu dari jenis berikut :values',
            'status_nikah.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp.digits'                  => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp.unique'                  => ':attribute telah ada yang menggunakan',
            'no_ktp_kk.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk.unique'               => ':attribute telah ada yang menggunakan',
            'no_kk.digits'                   => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk.unique'                   => ':attribute telah ada yang menggunakan',
            'no_npwp.digits'                 => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp.unique'                 => ':attribute telah ada yang menggunakan',
            'tgl_lahir.date_format'          => ':attribute harus berupa angka dengan format :format',
            'agama.in'                       => ':attribute harus salah satu dari jenis berikut :values',
            'rt_ktp.numeric'                 => ':attribute harus berupa angka',
            'rw_ktp.numeric'                 => ':attribute harus berupa angka',
            'id_provinsi_ktp.numeric'        => ':attribute harus berupa angka',
            'id_kabupaten_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kecamatan_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kelurahan_ktp.numeric'       => ':attribute harus berupa angka',
            'rt_domisili.numeric'            => ':attribute harus berupa angka',
            'rw_domisili.numeric'            => ':attribute harus berupa angka',
            'id_provinsi_domisili.numeric'   => ':attribute harus berupa angka',
            'id_kabupaten_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kecamatan_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kelurahan_domisili.numeric'  => ':attribute harus berupa angka',
            'jumlah_tanggungan.numeric'      => ':attribute harus berupa angka',
            'no_telp.between'                => ':attribute harus berada diantara :min - :max.',
            'no_telp.unique'                 => ':attribute telah ada yang menggunakan',
            'no_hp.between'                  => ':attribute harus berada diantara :min - :max.',
            'no_hp.unique'                   => ':attribute telah ada yang menggunakan',
            'tgl_lahir_anak1'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'tgl_lahir_anak2'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'lamp_surat_cerai.mimes'         => ':attribute harus bertipe :values',
            'tinggi_badan.numeric'           => ':attribute harus berupa angka',
            'berat_badan.numeric'            => ':attribute harus berupa angka',
            'pekerjaan.in'                   => ':attribute harus salah satu dari jenis berikut :values',
            'lamp_buku_tabungan.mimes'       => ':attribute harus bertipe :values',
            'lamp_ktp.mimes'                 => ':attribute harus bertipe :values',
            'lamp_kk.mimes'                  => ':attribute harus bertipe :values',
            'jenis_jaminan.in'               => ':attribute harus salah satu dari jenis berikut :values'
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
