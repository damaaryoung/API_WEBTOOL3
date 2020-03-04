<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use App\Models\Pengajuan\SO\Pasangan;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PasanganRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(3);

        if (!empty($single)) {
            $check = Pasangan::where('id', $single)->first();

            if ($check != null) {
                if ($check->id_pasangan != null) {
                    $rules['no_ktp_pas']    = 'digits:16|unique:web.pasangan_calon_debitur,no_ktp,'.$check->id_pasangan;
                    $rules['no_ktp_kk_pas'] = 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk,'.$check->id_pasangan;
                    $rules['no_kk_pas']     = 'digits:16|unique:web.pasangan_calon_debitur,no_kk,'.$check->id_pasangan;
                    $rules['no_npwp_pas']   = 'digits:15|unique:web.pasangan_calon_debitur,no_npwp,'.$check->id_pasangan;
                    $rules['tgl_lahir_pas'] = 'date_format:d-m-Y';
                    $rules['no_telp_pas']   = 'between:11,13|unique:web.pasangan_calon_debitur,no_telp,'.$check->id_pasangan;
                }
            }

            $rules = [
                // Pasangan
                'jenis_kelamin_pas'         => 'in:L,P',
                'lamp_ktp_pas'              => 'mimes:jpg,jpeg,png,pdf',
                'lamp_kk_pas'               => 'mimes:jpg,jpeg,png,pdf',
                'pekerjaan_pas'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pas'       => 'numeric',
                'rw_tempat_kerja_pas'       => 'numeric',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'numeric',
                'rt_tempat_kerja_pas'       => 'numeric',
                'rw_tempat_kerja_pas'       => 'numeric',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'numeric'
            ];
        }else{
            $rules = [
                // Pasangan
                'jenis_kelamin_pas'     => 'in:L,P',
                'no_ktp_pas'            => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp',
                'no_ktp_kk_pas'         => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk',
                'no_npwp_pas'           => 'digits:15|unique:web.pasangan_calon_debitur,no_npwp',
                'tgl_lahir_pas'         => 'date_format:d-m-Y',
                'no_telp_pas'           => 'between:11,13|unique:web.pasangan_calon_debitur,no_telp',
                'lamp_ktp_pas'          => 'mimes:jpg,jpeg,png,pdf',
                'lamp_kk_pas'           => 'mimes:jpg,jpeg,png,pdf'
            ];
        }

        return $rules;
    }

    public function messages(){
        return [
            // Pasangan
            'jenis_kelamin_pas.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp_pas.digits'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_pas.unique'                => ':attribute telah ada yang menggunakan',
            'no_ktp_kk_pas.digits'             => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk_pas.unique'             => ':attribute telah ada yang menggunakan',
            'no_kk_pas.digits'                 => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk_pas.unique'                 => ':attribute telah ada yang menggunakan',
            'no_npwp_pas.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp_pas.unique'               => ':attribute telah ada yang menggunakan',
            'id_provinsi'                      => ':attribute harus berupa angka',
            'id_kabupaten'                     => ':attribute harus berupa angka',
            'id_kecamatan'                     => ':attribute harus berupa angka',
            'id_kelurahan'                     => ':attribute harus berupa angka',
            'rt'                               => ':attribute harus berupa angka',
            'rw'                               => ':attribute harus berupa angka',
            'tgl_lahir_pas.date_format'        => ':attribute harus berupa angka dengan format :format',
            'no_telp_pas.between'              => ':attribute harus berada diantara :min - :max.',
            'no_telp_pas.unique'               => ':attribute telah ada yang menggunakan',
            'lamp_ktp_pas.mimes'               => ':attribute harus bertipe :values',
            'lamp_kk_pas.mimes'                => ':attribute harus bertipe :values',
            // 'lamp_ktp_pas.max'                 => 'ukuran :attribute max :max kb',
            // 'lamp_kk_pas.max'                  => 'ukuran :attribute max :max kb',
            'pekerjaan_pas.in'                 => ':attribute harus salah satu dari jenis berikut :values',
            'rt_tempat_kerja_pas.numeric'      => ':attribute harus berupa angka',
            'rw_tempat_kerja_pas.numeric'      => ':attribute harus berupa angka',
            // 'tgl_mulai_kerja_pas.date_format'  => ':attribute harus berupa angka dengan format :format',
            'no_telp_tempat_kerja_pas.numeric' => ':attribute harus berupa angka / bilangan bulat'
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
