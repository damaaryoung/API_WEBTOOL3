<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\Request;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PICRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $method = $request->method();

        $single = $request->segment(4);

        $check = PIC::where('id', $single)->first();

        switch ($method) {
            case 'POST':
                $user_id      = 'required|integer';
                $email        = 'email|unique:web.m_pic,email';
                $nama         = 'required';
                // $nama         = 'required|unique:web.m_pic,nama';
                $id_mk_area   = 'required|integer';
                $id_mk_cabang = 'required|integer';
                $id_mj_pic    = 'required|integer';
                break;

            case 'PUT':
                $user_id = ($check == null ? 'integer|unique:web.m_pic,user_id' : 'integer|unique:web.m_pic,user_id,' . $check->id);
                $email   = ($check == null ? 'email|unique:web.m_pic,email'     : 'email|unique:web.m_pic,email,' . $check->id);
                $nama    = ($check == null ? 'unique:web.m_pic,nama'            : 'unique:web.m_pic,nama,' . $check->id);
                $id_mk_area   = 'integer';
                $id_mk_cabang = 'integer';
                $id_mj_pic    = 'integer';
                break;
        }

        return [
            'user_id'      => $user_id,
            'email'        => $email,
            'id_mk_area'   => $id_mk_area,
            'id_mk_cabang' => $id_mk_cabang,
            'id_mj_pic'    => $id_mj_pic,
            'nama'         => $nama
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'      => ':attribute wajib diisi',
            'id_mk_area.required'   => ':attribute wajib diisi',
            'id_mk_cabang.required' => ':attribute wajib diisi',
            'id_mj_pic.required'    => ':attribute wajib diisi',
            'nama.required'         => ':attribute wajib diisi',
            'user_id.integer'       => ':attribute harus berupa angka',
            'id_mk_area.integer'    => ':attribute harus berupa angka',
            'id_mk_cabang.integer'  => ':attribute harus berupa angka',
            'id_mj_pic.integer'     => ':attribute harus berupa angka',
            'email.email'           => ':attribute harus berupa email',
            'user_id.unique'        => ':attribute telah ada yang menggunakan',
            'email.unique'          => ':attribute telah ada yang menggunakan',
            // 'nama.unique'           => ':attribute telah ada yang menggunakan'
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
