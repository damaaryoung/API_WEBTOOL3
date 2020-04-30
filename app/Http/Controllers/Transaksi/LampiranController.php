<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Transaksi\TransAO;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LampiranController extends BaseController
{
    public function form_ideb($id_transaksi, Request $req)
    {
        $check_so = TransSO::where('id', $id_transaksi)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_transaksi . ' tidak ditemukan'
            ], 404);
        }

        $lamp_dir = 'public/' . $check_so->debt['no_ktp'];

        if ($file = $req->file('form_persetujuan_ideb')) {
            $formP = $file->getClientOriginalExtension();

            if (
                $formP != 'png' &&
                $formP != 'jpg' &&
                $formP != 'jpeg' &&
                $formP != 'PNG' &&
                $formP != 'JPG' &&
                $formP != 'JPEG' &&
                $formP != 'pdf' &&
                $formP != 'PDF'
            ) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => "Lampiran Form Persetujuan Ideb harus berformat: png, jpg, jpeg, pdf"
                ], 422);
            }

            $path = $lamp_dir . '/ideb';
            $name = 'form_persetujuan_ideb';

            $check_file = $check_so->form_persetujuan_ideb;
            $form_persetujuan_ideb = Helper::uploadImg($check_file, $file, $path, $name);
            //  dd($form_persetujuan_ideb);
        } else {
            $form_persetujuan_ideb = $check_so->form_persetujuan_ideb;
        }

        DB::connection('web')->beginTransaction();
        try {
            TransSO::where('id', $id_transaksi)->update([
                'form_persetujuan_ideb' => $form_persetujuan_ideb
            ]);
            $dd =   TransAO::where('id_trans_so', $id_transaksi)->update([
                'form_persetujuan_ideb' => $form_persetujuan_ideb
            ]);
            //  dd($dd);

            DB::connection('web')->commit();
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'lampiran berhasil di unggah',
                'data'    => $form_persetujuan_ideb
            ], 200);
        } catch (\Exception $e) {
            DB::connection('web')->rollback();
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
