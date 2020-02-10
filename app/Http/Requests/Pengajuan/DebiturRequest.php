<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class DebiturRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(3);


        $check = Debitur::where('id', $single)->first();

        if ($check != null) {
            if ($check->id_calon_debt != null) {
                $rules['no_ktp']    = 'digits:16|unique:web.calon_debitur,no_ktp,'.$check->id_calon_debt;
                $rules['no_ktp_kk'] = 'digits:16|unique:web.calon_debitur,no_ktp_kk,'.$check->id_calon_debt;
                $rules['no_kk']     = 'digits:16|unique:web.calon_debitur,no_kk,'.$check->id_calon_debt;
                $rules['no_npwp']   = 'digits:15|unique:web.calon_debitur,no_npwp,'.$check->id_calon_debt;
                $rules['no_telp']   = 'between:11,13|unique:web.calon_debitur,no_telp,'.$check->id_calon_debt;
                $rules['no_hp']     = 'between:11,13|unique:web.calon_debitur,no_hp,'.$check->id_calon_debt;
            }
        }

        $rules = [
            // Debitur
            'jenis_kelamin'         => 'in:L,P',
            'status_nikah'          => 'in:SINGLE,NIKAH,CERAI',
            'tgl_lahir'             => 'date_format:d-m-Y',
            // 'agama'                 => 'in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
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
            'tgl_lahir_anak.*'      => 'date_format:d-m-Y',
            'tinggi_badan'          => 'numeric',
            'berat_badan'           => 'numeric',
            'pekerjaan'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
            'id_prov_tempat_kerja'  => 'numeric',
            'id_kab_tempat_kerja'   => 'numeric',
            'id_kec_tempat_kerja'   => 'numeric',
            'id_kel_tempat_kerja'   => 'numeric',
            'rt_tempat_kerja'       => 'numeric',
            'rw_tempat_kerja'       => 'numeric',
            // 'tgl_mulai_kerja'       => 'date_format:d-m-Y',
            'no_telp_tempat_kerja'  => 'numeric',

            'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_sttp_pbb'         => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_sertifikat'       => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_imb'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_buku_tabungan.*'  => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_sku.*'            => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_slip_gaji'        => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_foto_usaha.*'     => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'lamp_skk'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_pembukuan_usaha.*'=> 'mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        return $rules;
    }

    public function messages(){
        return [
            // Debitur
            'jenis_kelamin.required'          => ':attribute wajib diisi',
            'status_nikah.required'           => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp.required'                 => ':attribute wajib diisi',
            'no_ktp.required'                 => ':attribute wajib diisi',
            'no_ktp_kk.required'              => ':attribute wajib diisi',
            'no_ktp_kk.required'              => ':attribute wajib diisi',
            'no_kk.required'                  => ':attribute wajib diisi',
            'no_kk.required'                  => ':attribute wajib diisi',
            'no_npwp.required'                => ':attribute wajib diisi',
            'no_npwp.required'                => ':attribute wajib diisi',
            'tgl_lahir.required'              => ':attribute wajib diisi',
            'agama.required'                  => ':attribute wajib diisi',
            'rt_ktp.required'                 => ':attribute wajib diisi',
            'rw_ktp.required'                 => ':attribute wajib diisi',
            'id_provinsi_ktp.required'        => ':attribute wajib diisi',
            'id_kabupaten_ktp.required'       => ':attribute wajib diisi',
            'id_kecamatan_ktp.required'       => ':attribute wajib diisi',
            'id_kelurahan_ktp.required'       => ':attribute wajib diisi',
            'rt_domisili.required'            => ':attribute wajib diisi',
            'rw_domisili.required'            => ':attribute wajib diisi',
            'id_provinsi_domisili.required'   => ':attribute wajib diisi',
            'id_kabupaten_domisili.required'  => ':attribute wajib diisi',
            'id_kecamatan_domisili.required'  => ':attribute wajib diisi',
            'id_kelurahan_domisili.required'  => ':attribute wajib diisi',

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
            // 'agama.in'                       => ':attribute harus salah satu dari jenis berikut :values',
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

            'tgl_lahir_anak.*.date_format'   => ':attribute harus berupa angka dengan format :format',
            'tinggi_badan.numeric'           => ':attribute harus berupa angka',
            'berat_badan.numeric'            => ':attribute harus berupa angka',
            'pekerjaan.in'                   => ':attribute harus salah satu dari jenis berikut :values',
            'id_prov_tempat_kerja'           => ':attribute harus berupa angka',
            'id_kab_tempat_kerja'            => ':attribute harus berupa angka',
            'id_kec_tempat_kerja'            => ':attribute harus berupa angka',
            'id_kel_tempat_kerja'            => ':attribute harus berupa angka',
            'rt_tempat_kerja.numeric'        => ':attribute harus berupa angka',
            'rw_tempat_kerja.numeric'        => ':attribute harus berupa angka',
            // 'tgl_mulai_kerja.date_format'    => ':attribute harus berupa angka dengan format :format',
            'no_telp_tempat_kerja.numeric'   => ':attribute harus berupa angka',
            'lamp_surat_cerai.mimes'         => ':attribute harus bertipe :values',
            'lamp_buku_tabungan.mimes'       => ':attribute harus bertipe :values',
            'lamp_ktp.mimes'                 => ':attribute harus bertipe :values',
            'lamp_kk.mimes'                  => ':attribute harus bertipe :values',
            'lamp_sku.mimes'                 => ':attribute harus bertipe :values',
            'lamp_slip_gaji.mimes'           => ':attribute harus bertipe :values',
            'lamp_foto_usaha.mimes'          => ':attribute harus bertipe :values',
            'lamp_surat_cerai.max'           => 'ukuran :attribute max :values',
            'lamp_buku_tabungan.max'         => 'ukuran :attribute max :max kb',
            'lamp_ktp.max'                   => 'ukuran :attribute max :max kb',
            'lamp_kk.max'                    => 'ukuran :attribute max :max kb',
            'lamp_sku.max'                   => 'ukuran :attribute max :max kb',
            'lamp_slip_gaji.max'             => 'ukuran :attribute max :max kb',
            'lamp_foto_usaha.max'            => 'ukuran :attribute max :max kb'
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
