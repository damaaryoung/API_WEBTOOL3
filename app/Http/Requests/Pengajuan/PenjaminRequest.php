<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use App\Models\Pengajuan\SO\Penjamin;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PenjaminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(3);

        if (!empty($single)) {
            $check = Penjamin::where('id', $single)->first();

            if ($check->id_penjamin != null) {
                $rules['no_ktp_pen']  = 'digits:16|unique:web.penjamin_calon_debitur,no_ktp,' . $check->id;
                $rules['no_npwp_pen'] = 'digits:15|unique:web.penjamin_calon_debitur,no_npwp,' . $check->id;
                $rules['no_telp_pen'] = 'between:9,13|unique:web.penjamin_calon_debitur,no_telp,' . $check->id;
            }

            $rules = [
                // Penjamin
                'tgl_lahir_pen'            => 'date_format:d-m-Y',
                'jenis_kelamin_pen'        => 'in:L,P',
                'lamp_ktp_pen'             => 'mimes:jpg,jpeg,png,pdf',
                'lamp_ktp_pasangan_pen'    => 'mimes:jpg,jpeg,png,pdf',
                'lamp_kk_pen'              => 'mimes:jpg,jpeg,png,pdf',
                'lamp_buku_nikah_pen'      => 'mimes:jpg,jpeg,png,pdf',

                //'pekerjaan_pen'            => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pen'      => 'numeric',
                'rw_tempat_kerja_pen'      => 'numeric',
                // 'tgl_mulai_kerja_pen'      => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pen' => 'numeric'
            ];
        }else{
            $rules = [
                // Penjamin
                'no_ktp_pen.*'            => 'digits:16|unique:web.penjamin_calon_debitur,no_ktp',
                'no_npwp_pen.*'           => 'digits:15}unique:web.penjamin_calon_debitur,no_npwp',
                'tgl_lahir_pen.*'         => 'date_format:d-m-Y',
                'jenis_kelamin_pen.*'     => 'in:L,P',
                'no_telp_pen.*'           => 'between:9,13}unique:web.penjamin_calon_debitur,no_telp',
                'lamp_ktp_pen.*'          => 'mimes:jpg,jpeg,png,pdf',
                'lamp_ktp_pasangan_pen.*' => 'mimes:jpg,jpeg,png,pdf',
                'lamp_kk_pen.*'           => 'mimes:jpg,jpeg,png,pdf',
                'lamp_buku_nikah_pen.*'   => 'mimes:jpg,jpeg,png,pdf'
            ];
        }

        return $rules;
    }

    public function messages()
    {
            // Penjamin
            return [
                'no_ktp_pen.digits'                => ':attribute harus berupa angka dan berjumlah :digits digit',
                'no_ktp_pen.unique'                => ':attribute telah ada yang menggunakan',
                'no_npwp_pen.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
                'no_npwp_pen.unique'               => ':attribute telah ada yang menggunakan',
                'tgl_lahir_pen.date_format'        => ':attribute harus berupa angka dengan format :format',
                'jenis_kelamin_pen.in'             => ':attribute harus salah satu dari jenis berikut :values',
                'no_telp_pen.between'              => ':attribute harus berada diantara :min - :max.',
                'no_telp_pen.unique'               => ':attribute telah ada yang menggunakan',
                'lamp_ktp_pen.mimes'               => ':attribute harus bertipe :values',
                'lamp_ktp_pasangan_pen.mimes'      => ':attribute harus bertipe :values',
                'lamp_kk_pen.mimes'                => ':attribute harus bertipe :values',
                'lamp_buku_nikah_pen.mimes'        => ':attribute harus bertipe :values',
                // 'lamp_ktp_pen.max'                 => 'ukuran :attribute max :max kb',
                'lamp_ktp_pasangan_pen.max'        => 'ukuran :attribute max :max kb',
                'lamp_kk_pen.max'                  => 'ukuran :attribute max :max kb',
                // 'lamp_buku_nikah_pen.max'          => 'ukuran :attribute max :max kb',
                'pekerjaan_pen.in'                 => ':attribute harus salah satu dari jenis berikut :values',
                'rt_tempat_kerja_pen.numeric'      => ':attribute harus berupa angka',
                'rw_tempat_kerja_pen.numeric'      => ':attribute harus berupa angka',
                // 'tgl_mulai_kerja_pen.date_format'  => ':attribute harus berupa angka dengan format :format',
                'no_telp_tempat_kerja_pen.numeric' => ':attribute harus berupa angka',

                'no_ktp_pen.*.digits'                => ':attribute harus berupa angka dan berjumlah :digits digit',
                'no_ktp_pen.*.unique'                => ':attribute telah ada yang menggunakan',
                'no_npwp_pen.*.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
                'no_npwp_pen.*.unique'               => ':attribute telah ada yang menggunakan',
                'tgl_lahir_pen.*.date_format'        => ':attribute harus berupa angka dengan format :format',
                'jenis_kelamin_pen.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
                'no_telp_pen.*.between'              => ':attribute harus berada diantara :min - :max.',
                'no_telp_pen.*.unique'               => ':attribute telah ada yang menggunakan',
                'lamp_ktp_pen.*.mimes'               => ':attribute harus bertipe :values',
                'lamp_ktp_pasangan_pen.*.mimes'      => ':attribute harus bertipe :values',
                'lamp_kk_pen.*.mimes'                => ':attribute harus bertipe :values',
                'lamp_buku_nikah_pen.*.mimes'        => ':attribute harus bertipe :values',
                // 'lamp_ktp_pen.*.max'                 => 'ukuran :attribute max :max kb',
                // 'lamp_ktp_pasangan_pen.*.max'        => 'ukuran :attribute max :max kb',
                // 'lamp_kk_pen.*.max'                  => 'ukuran :attribute max :max kb',
                // 'lamp_buku_nikah_pen.*.max'          => 'ukuran :attribute max :max kb',
                'pekerjaan_pen.*.in'                 => ':attribute harus salah satu dari jenis berikut :values',
                'rt_tempat_kerja_pen.*.numeric'      => ':attribute harus berupa angka',
                'rw_tempat_kerja_pen.*.numeric'      => ':attribute harus berupa angka',
                // 'tgl_mulai_kerja_pen.*.date_format'  => ':attribute harus berupa angka dengan format :format',
                'no_telp_tempat_kerja_pen.*.numeric' => ':attribute harus berupa angka'
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
