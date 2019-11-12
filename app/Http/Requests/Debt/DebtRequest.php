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
            'nama_lengkap'          => 'required',
            'jenis_kelamin'         => 'required|in:L,P',
            'status_nikah'          => 'required|in:SINGLE,NIKAH,CERAI',
            'ibu_kandung'           => 'required',
            'no_ktp'                => 'required|digits:16|unique:web.calon_debitur,no_ktp',
            'no_ktp_kk'             => 'required|digits:16|unique:web.calon_debitur,no_ktp_kk',
            'no_kk'                 => 'required|digits:16|unique:web.calon_debitur,no_kk',
            'no_npwp'               => 'required|digits:15|unique:web.calon_debitur,no_npwp',
            'tempat_lahir'          => 'required',
            'tgl_lahir'             => 'required|date_format:d-m-Y',
            'agama'                 => 'required|in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
            'alamat_ktp'            => 'required',
            'rt_ktp'                => 'required|numeric',
            'rw_ktp'                => 'required|numeric',
            'id_provinsi_ktp'       => 'required|numeric',
            'id_kabupaten_ktp'      => 'required|numeric',
            'id_kecamatan_ktp'      => 'required|numeric',
            'id_kelurahan_ktp'      => 'required|numeric',
            'alamat_domisili'       => 'required',
            'rt_domisili'           => 'required|numeric',
            'rw_domisili'           => 'required|numeric',
            'id_provinsi_domisili'  => 'required|numeric',
            'id_kabupaten_domisili' => 'required|numeric',
            'id_kecamatan_domisili' => 'required|numeric',
            'id_kelurahan_domisili' => 'required|numeric',
            'pendidikan_terakhir'   => 'required',
            'jumlah_tanggungan'     => 'numeric',
            'no_telp'               => 'required|between:11,13|unique:web.calon_debitur,no_telp',
            'alamat_surat'          => 'required',
            'tgl_lahir_anak1'       => 'date_format:d-m-Y',
            'tgl_lahir_anak2'       => 'date_format:d-m-Y',
            'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf',
            'pekerjaan'             => 'required',
            'lamp_buku_tabungan'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf',
            'jenis_jaminan'         => 'required|in:TANAH,KENDARAAN'
        ];
    }

    public function messages(){
        return [
            'nama_lengkap.required'          => ':attribute belum diisi',
            'jenis_kelamin.required'         => ':attribute belum diisi',
            'jenis_kelamin.in'               => ':attribute harus salah satu dari jenis berikut :values',
            'status_nikah.required'          => ':attribute belum diisi',
            'status_nikah.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'ibu_kandung.required'           => ':attribute belum diisi',
            'no_ktp.required'                => ':attribute belum diisi.',
            'no_ktp.digits'                  => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp.unique'                  => ':attribute telah ada yang menggunakan',
            'no_ktp_kk.required'             => ':attribute belum diisi',
            'no_ktp_kk.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk.unique'               => ':attribute telah ada yang menggunakan',
            'no_kk.required'                 => ':attribute belum diisi',
            'no_kk.digits'                   => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk.unique'                   => ':attribute telah ada yang menggunakan',
            'no_npwp.required'               => ':attribute belum diisi',
            'no_npwp.digits'                 => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp.unique'                 => ':attribute telah ada yang menggunakan',
            'tempat_lahir.required'          => ':attribute belum diisi',
            'tgl_lahir.required'             => ':attribute belum diisi',
            'tgl_lahir.date_format'          => ':attribute harus berupa angka dengan format :format',
            'agama.in'                       => ':attribute harus salah satu dari jenis berikut :values',
            'alamat_ktp.required'            => ':attribute belum diisi',
            'rt_ktp.required'                => ':attribute belum diisi',
            'rt_ktp.numeric'                 => ':attribute harus berupa angka',
            'rw_ktp.required'                => ':attribute belum diisi',
            'rw_ktp.numeric'                 => ':attribute harus berupa angka',
            'id_provinsi_ktp.required'       => ':attribute belum diisi',
            'id_provinsi_ktp.numeric'        => ':attribute harus berupa angka',
            'id_kabupaten_ktp.required'      => ':attribute belum diisi',
            'id_kabupaten_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kecamatan_ktp.required'      => ':attribute belum diisi',
            'id_kecamatan_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kelurahan_ktp.required'      => ':attribute belum diisi',
            'id_kelurahan_ktp.numeric'       => ':attribute harus berupa angka',
            'alamat_domisili.required'       => ':attribute belum diisi',
            'rt_domisili.required'           => ':attribute belum diisi',
            'rt_domisili.numeric'            => ':attribute harus berupa angka',
            'rw_domisili.required'           => ':attribute belum diisi',
            'rw_domisili.numeric'            => ':attribute harus berupa angka',
            'id_provinsi_domisili.required'  => ':attribute belum diisi',
            'id_provinsi_domisili.numeric'   => ':attribute harus berupa angka',
            'id_kabupaten_domisili.required' => ':attribute belum diisi',
            'id_kabupaten_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kecamatan_domisili.required' => ':attribute belum diisi',
            'id_kecamatan_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kelurahan_domisili.required' => ':attribute belum diisi',
            'id_kelurahan_domisili.numeric'  => ':attribute harus berupa angka',
            'pendidikan_terakhir.required'   => ':attribute hbelum diisi',
            'jumlah_tanggungan.numeric'      => ':attribute harus berupa angka',
            'no_telp.required'               => ':attribute belum diisi',
            'no_telp.between'                => ':attribute harus berada diantara :min - :max.',
            'no_telp.unique'                 => ':attribute telah ada yang menggunakan',
            'alamat_surat.required'          => ':attribute belum diisi.',
            'tgl_lahir_anak1'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'tgl_lahir_anak2'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'lamp_surat_cerai.mimes'         => ':attribute harus bertipe :values',
            'pekerjaan.required'             => ':attribute belum diisi.',
            'pekerjaan.in'                   => ':attribute harus salah satu dari jenis berikut :values',
            'lamp_buku_tabungan.mimes'       => ':attribute harus bertipe :values',
            'lamp_kk.mimes'                  => ':attribute harus bertipe :values',
            'jenis_jaminan.required'         => ':attribute belum diisi',
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
                "errors"  => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
