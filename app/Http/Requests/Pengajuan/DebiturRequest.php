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
            if ($check->id != null) {
                $rules['no_ktp']    = 'digits:16|unique:web.calon_debitur,no_ktp,'.$check->id;
                $rules['no_ktp_kk'] = 'digits:16|unique:web.calon_debitur,no_ktp_kk,'.$check->id;
                $rules['no_kk']     = 'digits:16|unique:web.calon_debitur,no_kk,'.$check->id;
                $rules['no_npwp']   = 'digits:15|unique:web.calon_debitur,no_npwp,'.$check->id;
                $rules['no_telp']   = 'between:9,13|unique:web.calon_debitur,no_telp,'.$check->id;
                $rules['no_hp']     = 'between:9,13|unique:web.calon_debitur,no_hp,'.$check->id;
            }else{
                $rules['no_ktp']    = 'digits:16|unique:web.calon_debitur,no_ktp';
                $rules['no_ktp_kk'] = 'digits:16|unique:web.calon_debitur,no_ktp_kk';
                $rules['no_kk']     = 'digits:16|unique:web.calon_debitur,no_kk';
                $rules['no_npwp']   = 'digits:15|unique:web.calon_debitur,no_npwp';
                $rules['no_telp']   = 'between:9,13|unique:web.calon_debitur,no_telp';
                $rules['no_hp']     = 'between:9,13|unique:web.calon_debitur,no_hp';
            }
        }else{
            $rules['no_ktp']    = 'digits:16';
            $rules['no_ktp_kk'] = 'digits:16';
            $rules['no_kk']     = 'digits:16';
            $rules['no_npwp']   = 'digits:15';
            $rules['no_telp']   = 'between:9,13';
            $rules['no_hp']     = 'between:9,13';
        }

        $rules = [
            // Debitur
            'jenis_kelamin'         => 'in:L,P',
            'status_nikah'          => 'in:Single,Menikah,Janda/Duda',
            // 'tgl_lahir'             => 'date_format:d-m-Y',
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
         //   'pekerjaan'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
            'id_prov_tempat_kerja'  => 'numeric',
            'id_kab_tempat_kerja'   => 'numeric',
            'id_kec_tempat_kerja'   => 'numeric',
            'id_kel_tempat_kerja'   => 'numeric',
            'rt_tempat_kerja'       => 'numeric',
            'rw_tempat_kerja'       => 'numeric',
            // 'tgl_mulai_kerja'       => 'date_format:d-m-Y',
            'no_telp_tempat_kerja'  => 'numeric',

            'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf',
            'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf',
            'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf',
            'lamp_sttp_pbb'         => 'mimes:jpg,jpeg,png,pdf',
            'lamp_sertifikat'       => 'mimes:jpg,jpeg,png,pdf',
            'lamp_imb'              => 'mimes:jpg,jpeg,png,pdf',
            'lamp_pbb'              => 'image|mimes:jpg,jpeg,png,pdf',
            'lamp_buku_tabungan.*'  => 'mimes:jpg,jpeg,png,pdf',
            'lamp_sku.*'            => 'mimes:jpg,jpeg,png,pdf',
            'lamp_slip_gaji'        => 'mimes:jpg,jpeg,png,pdf',
            'lamp_foto_usaha.*'     => 'mimes:jpg,jpeg,png,pdf',
            'lamp_skk'              => 'mimes:jpg,jpeg,png,pdf',
            'lamp_pembukuan_usaha.*'=> 'mimes:jpg,jpeg,png,pdf',
            'foto_agunan_rumah'     => 'mimes:jpg,jpeg,png,pdf'
        ];

        return $rules;
    }

    public function messages(){
        return [
            // Debitur
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
         //   'tgl_lahir.date_format'          => ':attribute harus berupa angka dengan format :format',
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
            'lamp_buku_tabungan.*.mimes'     => ':attribute harus bertipe :values',
            'lamp_ktp.mimes'                 => ':attribute harus bertipe :values',
            'lamp_kk.mimes'                  => ':attribute harus bertipe :values',
            'lamp_slip_gaji.mimes'           => ':attribute harus bertipe :values',
            'lamp_foto_usaha.*.mimes'        => ':attribute harus bertipe :values',
            
            'lamp_sertifikat.mimes'          => ':attribute harus bertipe :values',
            'lamp_pbb.mimes'                 => ':attribute harus bertipe :values',
            'lamp_imb.mimes'                 => ':attribute harus bertipe :values',
            'lamp_sku.*.mimes'               => ':attribute harus bertipe :values',
            'lamp_skk.mimes'                 => ':attribute harus bertipe :values',
            'lamp_slip_gaji.mimes'           => ':attribute harus bertipe :values',
            'lamp_pembukuan_usaha.*.mimes'   => ':attribute harus bertipe :values',
            'foto_agunan_rumah.mimes'        => ':attribute harus bertipe :values'
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
