<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

use App\Models\Jari\CollectResult;
use App\Models\Jari\TaskCollect;
use App\Models\Jari\Taskdraft;
use App\Models\Jari\Detailtaskdraft;
use App\Models\AreaKantor\ParameterAcc;
use App\Models\Jari\CollectActivity;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Jari_CollController extends BaseController
{
    public function getCollectResult(Request $request)
    {
        $user_id = $request->auth->user_id;
        //dd($user_id);
        // $pic = $request->header('Authorization');
        // $str = str_replace('Bearer ', '', $pic);
        // $str2 = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $str)[1]))));
        // $check = PIC::where('id', $id)->first();
        $param = ParameterAcc::where('id', 'USER_AKSES_COLLECTION')->first();
        $exp_param = explode(',', $param->value);
        $dd = in_array($user_id, $exp_param);
        // dd($dd);
        if ($dd != true) {
            return response()->json([
                'code'    => 401,
                'status'  => 'UnAuthorized',
                'message' => 'Level User anda bukan UNIT HEAD COLLECTION Anda Tidak Mempunyai Akses Untuk Melihat Menu Ini' . ' ' . 'Silahkan Hubungi IT'
            ], 401);
        }


        // $hasil = DB::connection('jari')->table('vw_collect_bayar')->orderBy('assigndate', 'ASC')->get();
        // // $count_kontrak = DB::connection('jari')->select("SELECT COUNT(*) FROM view_kretrans WHERE my_kode_trans=300 AND (pokok > 0 AND bunga > 0) 
        // // AND MONTH(tgl_trans)=MONTH(CURDATE()) AND YEAR(tgl_trans)=YEAR(CURDATE()) AND pelunasan='T'
        // // AND no_rekening 
        // // IN (SELECT accno FROM jari_collection.`taskdraft` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()))");


        // // $assign_kontrak = DB::connection('jari')->select("SELECT COUNT(DISTINCT accno) FROM jari_collection.`taskdraft` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $assign_kontrak = TaskDraft::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('accno')->count('accno');


        // // $assign_task = DB::connection('jari')->select("SELECT COUNT(id) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $assign_task = TaskDraft::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->count('id');



        // // $visit_task = DB::connection('jari')->select("SELECT COUNT(id) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $visit_task = TaskCollect::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->count('id');

        // // $visit_kontrak = DB::connection('jari')->select("SELECT COUNT(DISTINCT accno) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $visit_kontrak = TaskCollect::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('accno')->count('accno');



        // // $interaksi_task = DB::connection('jari')->select("SELECT COUNT(taskid) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='INTERAKSI'");

        // $interaksi_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'INTERAKSI')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->count('taskid');

        // //  dd(array($interaksi_task,  $interaksi_task_j));

        // // $interaksi_kontrak = DB::connection('jari')->select("SELECT COUNT(DISTINCT accno) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='INTERAKSI'");

        // $interaksi_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'INTERAKSI')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('accno')->count('accno');

        // // $JB_task = DB::connection('jari')->select("SELECT COUNT(taskid) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='JANJI BAYAR'");

        // $JB_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'JANJI BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->count('taskid');

        // // $JB_kontrak = DB::connection('jari')->select("SELECT COUNT(DISTINCT accno) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='JANJI BAYAR'");

        // $JB_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'JANJI BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('accno')->count('accno');

        // // $bayar_task = DB::connection('jari')->select("SELECT COUNT(taskid) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='BAYAR'");

        // $bayar_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->count('taskid');

        // // $bayar_kontrak = DB::connection('jari')->select("SELECT COUNT(DISTINCT accno) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='BAYAR'");

        // $bayar_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('accno')->count('accno');

        // // $assign_angsuran = DB::connection('jari')->select("SELECT SUM(pokok) FROM view_kretrans WHERE my_kode_trans=300 AND (pokok > 0 AND bunga > 0) 
        // // AND MONTH(tgl_trans)=MONTH(CURDATE()) AND YEAR(tgl_trans)=YEAR(CURDATE()) AND pelunasan='T'");

        // $assign_angsuran = DB::connection('jari')->table('view_kretrans')->where('my_kode_trans', 300)->where('pokok', '>', 0)->where('bunga', '>', 0)->whereMonth('tgl_trans', Carbon::now())->whereYear('tgl_trans', Carbon::now())->where('pelunasan', '=', 'T')->sum('pokok');

        // //     dd(array($assign_angsuran,  $assign_angsuran_j));


        // // $assign_ospokok = DB::connection('jari')->select("SELECT SUM(ospokok) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $assign_ospokok = TaskCollect::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->sum('ospokok');

        // // $visit_ospokok = DB::connection('jari')->select("SELECT SUM(DISTINCT ospokok) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='KUNJUNGAN'");

        // $visit_ospokok = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'KUNJUNGAN')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('ospokok')->sum('ospokok');

        // // $interaksi_ospokok = DB::connection('jari')->select("SELECT SUM(DISTINCT ospokok) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='INTERAKSI'");

        // $interaksi_ospokok = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'INTERAKSI')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('ospokok')->sum('ospokok');

        // // $JB_ospokok = DB::connection('jari')->select("SELECT SUM(DISTINCT ospokok) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='JANJI BAYAR'");

        // $JB_ospokok = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'JANJI BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('ospokok')->sum('ospokok');


        // // $bayar_ospokok = DB::connection('jari')->select("SELECT SUM(DISTINCT ospokok) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='BAYAR'");

        // $bayar_ospokok = Taskcollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->where('collectresult.code', 'BAYAR')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->distinct('ospokok')->sum('ospokok');

        // // $assign_ospokok = DB::connection('jari')->select("SELECT SUM(ospokok) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $sum_bayar_ospokok = TaskCollect::join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->where('collectresult.code', '=', 'BAYAR')->sum('ospokok');

        // $sum_assign_pokok = TaskCollect::whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->sum('ospokok');
        // //  dd($sum_assign_pokok);
        // //$current = $bayar_ospokok $assign_ospokok
        // //print_r($matches);
        // //  $dd =  str_replace('SUM(DISTINCT', '', $bayar_ospokok);
        // // dd($dd);

        // $angsuran_assignment = Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->whereMonth('assigndate', Carbon::now())->whereYear('assigndate', Carbon::now())->sum('angstung');

        // // $angsuran_kontrak = DB::connection('jari')->select("SELECT SUM(angstung) FROM detail_taskbulkdraft INNER JOIN taskdraft ON (taskdraft.`taskcode`=detail_taskbulkdraft.`taskcode`) 
        // // WHERE MONTH(taskdraft.`assigndate`)=MONTH(CURDATE()) AND YEAR(taskdraft.`assigndate`)=YEAR(CURDATE())
        // // AND taskdraft.`accno` IN (select DISTINCT accno FROM taskdraft)");

        // // $visit_angsuran = DB::connection('jari')->select("SELECT SUM(angstung) FROM detail_taskbulkdraft INNER JOIN taskdraft ON (taskdraft.`taskcode`=detail_taskbulkdraft.`taskcode`) INNER JOIN task_collect ON (detail_taskbulkdraft.`taskcode`=task_collect.`taskcode`) INNER JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`)
        // // WHERE MONTH(taskdraft.`assigndate`)=MONTH(CURDATE()) AND YEAR(taskdraft.`assigndate`)=YEAR(CURDATE())
        // // AND collectresult.`code`='KUNJUNGAN'
        // // ");

        // $visit_angsuran = Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->join('task_collect', 'detail_taskbulkdraft.taskcode', '=', 'task_collect.taskcode')->join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->where('collectresult.code', 'KUNJUNGAN')->whereMonth('taskdraft.assigndate', Carbon::now())->whereYear('taskdraft.assigndate', Carbon::now())->sum('angstung');

        // //         $interaksi_angsuran = DB::connection('jari')->select("SELECT SUM(angstung) FROM detail_taskbulkdraft INNER JOIN taskdraft ON (taskdraft.`taskcode`=detail_taskbulkdraft.`taskcode`) INNER JOIN task_collect ON (detail_taskbulkdraft.`taskcode`=task_collect.`taskcode`) INNER JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`)
        // // WHERE MONTH(taskdraft.`assigndate`)=MONTH(CURDATE()) AND YEAR(taskdraft.`assigndate`)=YEAR(CURDATE())
        // // AND collectresult.`code`='INTERAKSI'
        // // ");

        // $interaksi_angsuran = Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->join('task_collect', 'detail_taskbulkdraft.taskcode', '=', 'task_collect.taskcode')->join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->where('collectresult.code', 'INTERAKSI')->whereMonth('taskdraft.assigndate', Carbon::now())->whereYear('taskdraft.assigndate', Carbon::now())->sum('angstung');

        // //         $jb_angsuran = DB::connection('jari')->select("SELECT SUM(angstung) FROM detail_taskbulkdraft INNER JOIN taskdraft ON (taskdraft.`taskcode`=detail_taskbulkdraft.`taskcode`) INNER JOIN task_collect ON (detail_taskbulkdraft.`taskcode`=task_collect.`taskcode`) INNER JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`)
        // // WHERE MONTH(taskdraft.`assigndate`)=MONTH(CURDATE()) AND YEAR(taskdraft.`assigndate`)=YEAR(CURDATE())
        // // AND collectresult.`code`='JANJI BAYAR'
        // // ");

        // $jb_angsuran = Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->join('task_collect', 'detail_taskbulkdraft.taskcode', '=', 'task_collect.taskcode')->join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->where('collectresult.code', 'JANJI BAYAR')->whereMonth('taskdraft.assigndate', Carbon::now())->whereYear('taskdraft.assigndate', Carbon::now())->sum('angstung');

        // //         $bayar_angsuran = DB::connection('jari')->select("SELECT SUM(angstung) FROM detail_taskbulkdraft INNER JOIN taskdraft ON (taskdraft.`taskcode`=detail_taskbulkdraft.`taskcode`) INNER JOIN task_collect ON (detail_taskbulkdraft.`taskcode`=task_collect.`taskcode`) INNER JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`)
        // // WHERE MONTH(taskdraft.`assigndate`)=MONTH(CURDATE()) AND YEAR(taskdraft.`assigndate`)=YEAR(CURDATE())
        // // AND collectresult.`code`='BAYAR'
        // // ");

        // $bayar_angsuran = Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->join('task_collect', 'detail_taskbulkdraft.taskcode', '=', 'task_collect.taskcode')->join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->where('collectresult.code', 'BAYAR')->whereMonth('taskdraft.assigndate', Carbon::now())->whereYear('taskdraft.assigndate', Carbon::now())->sum('angstung');


        // //    dd($bayar_angsuran);


        // $percent = $sum_bayar_ospokok / $sum_assign_pokok;
        // $percent_current = number_format($percent * 100, 2) . '%';

        // $percent_coll = $bayar_angsuran / $angsuran_assignment;
        // $percent_collrasio = number_format($percent_coll * 100, 2) . '%';

     //   dd($percent_current);


        $collect_activity = CollectActivity::get();

        // $count_task = DB::connection('jari')->select("SELECT count(*) FROM jari_collection.`task_collect` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");
        // $assign = DB::connection('jari')->select("SELECT COUNT(*) FROM jari_collection.`taskdraft` WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE())");

        // $visit = DB::connection('jari')->select("SELECT COUNT(*) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='KUNJUNGAN'");

        // $bayar = DB::connection('jari')->select("SELECT COUNT(*) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='BAYAR'");

        // $janji_bayar = DB::connection('jari')->select("SELECT COUNT(*) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='JANJI BAYAR'");

        // $interaksi = DB::connection('jari')->select("SELECT COUNT(*) FROM jari_collection.`task_collect`  JOIN collectresult ON (task_collect.`id`=collectresult.`taskid`) WHERE MONTH(assigndate)=MONTH(CURDATE()) AND YEAR(assigndate)=YEAR(CURDATE()) AND collectresult.`code`='INTERAKSI'");

        //   dd($count);
        if (empty($collect_activity)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Collect Result Kosong'
            ], 404);
        }

        // if (empty($real)) {
        //     return response()->json([
        //         'code'    => 402,
        //         'status'  => 'not found',
        //         'message' => 'Data Hasil LPDK id transaksi ' . $id . 'Belum Melalui Proses REALISASI'
        //     ], 402);
        // }
        //  $data = array();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data' => $collect_activity
                // array(
                //     'assignment' =>
                //     [
                //         [
                //             'nama' => 'Assign Task',
                //             'jumlah' => $assign_task
                //         ],
                //         [
                //             'nama' => 'Assign Kontrak',
                //             'jumlah' => $assign_kontrak
                //         ],
                //         [
                //             'nama' => 'Angsuran Assignment',
                //             'jumlah' => $angsuran_assignment
                //         ],
                //         [
                //             'nama' => 'Ospokok Assignment',
                //             'jumlah' => $assign_ospokok
                //         ],
                //     ],
                //     'visit' =>
                //     [
                //         [
                //             'nama' => 'Visit Task',
                //             'jumlah' => $visit_task
                //         ],
                //         [
                //             'nama' => 'Visit Kontrak',
                //             'jumlah'  =>   $visit_kontrak
                //         ],
                //         [
                //             'nama' => 'Angsuran Visit',
                //             'jumlah' => $visit_angsuran
                //         ],
                //         [
                //             'nama' => 'Ospokok Visit',
                //             'jumlah' => $visit_ospokok
                //         ],
                //     ],
                //     'interaksi' =>
                //     [
                //         [
                //             'nama' => 'Interaksi Task',
                //             'jumlah' => $interaksi_task
                //         ],
                //         [
                //             'nama' => 'Interaksi Kontrak',
                //             'jumlah' => $interaksi_kontrak
                //         ],
                //         [
                //             'nama' => 'Angsuran Interaksi',
                //             'jumlah' => $interaksi_angsuran
                //         ],
                //         [
                //             'nama' => 'Ospokok Interaksi',
                //             'jumlah' => $interaksi_ospokok
                //         ],
                //     ],
                //     'janji_bayar' =>
                //     [
                //         [
                //             'nama' => 'Janji Bayar Task',
                //             'jumlah' => $JB_task
                //         ],
                //         [
                //             'nama' => 'Janji Bayar Kontrak',
                //             'jumlah' => $JB_kontrak
                //         ],

                //         [
                //             'nama' => 'Angsuran Janji Bayar',
                //             'jumlah' => $jb_angsuran
                //         ],
                //         [
                //             'nama' => 'Ospokok Janji Bayar',
                //             'jumlah' => $JB_ospokok
                //         ],
                //     ],
                //     'bayar' =>
                //     [
                //         [
                //             'nama' => 'Bayar Task',
                //             'jumlah' => $bayar_task
                //         ],
                //         [
                //             'nama_' => 'Bayar Kontrak',
                //             'jumlah_' => $bayar_kontrak
                //         ],
                //         [
                //             'nama' => 'Angsuran Bayar',
                //             'jumlah' => $bayar_angsuran
                //         ],
                //         [
                //             'nama' => 'Ospokok Bayar',
                //             'jumlah' => $bayar_ospokok
                //         ],
                //     ],


                //     // ],
                //     'current' =>
                //     [
                //         'nama' => '%current',
                //         'jumlah' => $percent_current
                //     ],
                //     'collection_rasio' =>
                //     [
                //         'nama' => '%collection_rasio',
                //         'jumlah' => $percent_collrasio
                //     ]

                // )

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function filterCollectResult(Request $req)
    {
        $user_id = $req->auth->user_id;
       $tgl_awal = $req->input('tgl_transaksi_awal');
       $tgl_akhir = $req->input('tgl_transaksi_akhir');
       $area = $req->input('area');
       $cabang = $req->input('cabang');
       $pic = $req->input('kolektor');

       if (empty($tgl_awal) || empty($tgl_akhir)) {
           return response()->json([
               'code' => 403,
               'status' => 'bad request',
               'message' => 'silahkan input data mandatory'
           ],403);
       }

//ASSIGNMENT

//assign task
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
       $assign_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
       ->count('task_collect.id');
} elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
    $assign_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
   ->count('task_collect.id');
} elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) {
    $assign_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
     ->where('collectresult.collector_area',$area)
     ->where('collectresult.collector_cabang',$cabang)
    ->where('collector_collectorcode',$pic)
   ->count('task_collect.id');
} else {
    $assign_task = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->count('task_collect.id');
}

//assign_kontrak
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
      $assign_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
      ->where('collectresult.collector_area',$area)
      ->distinct('task_collect.accno')
      ->count('task_collect.accno');

} elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
    $assign_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->distinct('task_collect.accno')
    ->count('task_collect.accno');
} elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
    $assign_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collector_collectorcode',$pic)
    ->distinct('task_collect.accno')->count('task_collect.accno');
} else { 
    $assign_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->distinct('task_collect.accno')->count('task_collect.accno');
}

//assignment angsuran
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $assign_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->where('collectresult.collector_area',$area)
    ->distinct('angstung')
    ->sum('angstung');    
} elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
    $assign_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->distinct('angstung')
    ->sum('angstung');

} elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
     $assign_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->where('collectresult.collector_area',$area)
     ->where('collectresult.collector_cabang',$cabang)
     ->where('collectorid',$pic)
     ->distinct('angstung')
       ->sum('angstung');
    
}  else { 
    $assign_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->distinct('angstung')
    ->sum('angstung');
}


//assign ospokok
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $assign_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->distinct('ospokok')
    ->sum('ospokok');
} elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
    $assign_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->distinct('ospokok')
    ->sum('ospokok');
} elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
    $assign_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectorid',$pic)
    ->distinct('ospokok')
    ->sum('ospokok');
} else { 

    $assign_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->distinct('ospokok')
    ->sum('ospokok');
}


//VISIT

//visit task 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
$visit_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
->where('collectresult.collector_area',$area)
->where('collectresult.code','KUNJUNGAN')
->count('task_collect.id');

} elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
    $visit_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.code','KUNJUNGAN')
    ->count('task_collect.id');
} elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
    $visit_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.collector_collectorcode',$pic)
    ->where('collectresult.code','KUNJUNGAN')
    ->count('task_collect.id');
} else { 
    $visit_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.code','KUNJUNGAN')
    ->count('task_collect.id');
}

//visit kontrak 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $visit_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','KUNJUNGAN')
    ->distinct('task_collect.accno')
    ->count('task_collect.accno');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $visit_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('task_collect.accno')
        ->count('task_collect.accno');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $visit_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('task_collect.accno')
        ->count('task_collect.accno');
    } else { 
        $visit_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('task_collect.accno')
        ->count('task_collect.accno');
    }
 
//visit_angsuran 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $visit_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('angstung','>','0')
    ->where('collectresult.code','KUNJUNGAN')
    ->distinct('angstung')
       ->sum('angstung');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $visit_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('angstung','>','0')
    ->where('collectresult.code','KUNJUNGAN')
    ->distinct('angstung')
       ->sum('angstung');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $visit_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('angstung','>','0')
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('angstung')
           ->sum('angstung');
    } else { 
        $visit_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('angstung','>','0')
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('angstung')
           ->sum('angstung');
    }

    //visit_ospokok 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $visit_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','KUNJUNGAN')
    ->distinct('ospokok')
    ->sum('ospokok');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $visit_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('ospokok')
        ->sum('ospokok');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $visit_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('ospokok')
        ->sum('ospokok');
    } else { 
        $visit_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.id')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','KUNJUNGAN')
        ->distinct('ospokok')
        ->sum('ospokok');
    }

//INTERAKSI
      //interaksi_task 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $interaksi_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','INTERAKSI')
    ->count('task_collect.id');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $interaksi_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.code','INTERAKSI')
    ->count('task_collect.id');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $interaksi_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectorid',$pic)
    ->where('collectresult.code','INTERAKSI')
    ->count('task_collect.id');
    } else { 
        $interaksi_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','INTERAKSI')
        ->count('task_collect.id');
    }

      //interaksi_kontrak 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $interaksi_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','INTERAKSI')
        ->distinct('accno')
        ->count('accno');
        
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $interaksi_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','INTERAKSI')
            ->distinct('accno')
            ->count('accno');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $interaksi_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('collectresult.code','INTERAKSI')
            ->distinct('accno')
            ->count('accno');
        } else { 
            $interaksi_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','INTERAKSI')
            ->distinct('accno')
            ->count('accno');
        }


      //interaksi_angsuran 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $interaksi_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('angstung','>','0')
        ->where('collectresult.code','INTERAKSI')
        ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $interaksi_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','INTERAKSI')
            ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $interaksi_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('angstung','>','0')
            ->where('collectresult.code','INTERAKSI')
            ->sum('angstung');
        } else { 
            $interaksi_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','INTERAKSI')
            ->sum('angstung');
        }

           //interaksi_ospokok 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $interaksi_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','INTERAKSI')
        ->distinct('ospokok')
        ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $interaksi_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','INTERAKSI')
            ->distinct('ospokok')
            ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $interaksi_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','INTERAKSI')
       ->distinct('ospokok')
        ->sum('ospokok');
        } else { 
            $interaksi_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','INTERAKSI')
            ->distinct('ospokok')
            ->sum('ospokok');
        }


        //JANJI BAYAR
      //jb_task 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $jb_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','JANJI BAYAR')
    ->count('task_collect.id');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $jb_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.code','JANJI BAYAR')
    ->count('task_collect.id');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $jb_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectorid',$pic)
    ->where('collectresult.code','JANJI BAYAR')
    ->count('task_collect.id');
    } else { 
        $jb_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','JANJI BAYAR')
        ->count('task_collect.id');
    }

      //jb_kontrak 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $jb_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','JANJI BAYAR')
        ->distinct('accno')
        ->count('accno');
        
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $jb_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','JANJI BAYAR')
            ->distinct('accno')
            ->count('accno');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $jb_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('collectresult.code','JANJI BAYAR')
            ->distinct('accno')
            ->count('accno');
        } else { 
            $jb_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','JANJI BAYAR')
            ->distinct('accno')
            ->count('accno');
        }


      //jb_angsuran 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $jb_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('angstung','>','0')
        ->where('collectresult.code','JANJI BAYAR')
        ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $jb_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','JANJI BAYAR')
            ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $jb_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('angstung','>','0')
            ->where('collectresult.code','JANJI BAYAR')
            ->sum('angstung');
        } else { 
            $jb_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','JANJI BAYAR')
            ->sum('angstung');
        }

           //jb_ospokok 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $jb_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','JANJI BAYAR')
        ->distinct('ospokok')
        ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $jb_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','JANJI BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $jb_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','JANJI BAYAR')
       ->distinct('ospokok')
        ->sum('ospokok');
        } else { 
            $jb_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','JANJI BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        }


        //BAYAR JARI
      //bayarjari_task 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $bayarjari_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $bayarjari_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $bayarjari_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectorid',$pic)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    } else { 
        $bayarjari_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','BAYAR')
        ->count('task_collect.id');
    }

      //bayarjari_kontrak 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayarjari_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','BAYAR')
        ->distinct('accno')
        ->count('accno');
        
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayarjari_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayarjari_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        } else { 
            $bayarjari_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        }


      //bayarjari_angsuran 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayarjari_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('angstung','>','0')
        ->where('collectresult.code','BAYAR')
        ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayarjari_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayarjari_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        } else { 
            $bayarjari_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        }

           //bayarjari_ospokok 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayarjari_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','BAYAR')
        ->distinct('ospokok')
        ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayarjari_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayarjari_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','BAYAR')
       ->distinct('ospokok')
        ->sum('ospokok');
        } else { 
            $bayarjari_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        }

        
     //BAYAR
      //bayar_task 
if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
    $bayar_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    
    } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
        $bayar_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
        $bayar_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
    ->where('collectresult.collector_area',$area)
    ->where('collectresult.collector_cabang',$cabang)
    ->where('collectorid',$pic)
    ->where('collectresult.code','BAYAR')
    ->count('task_collect.id');
    } else { 
        $bayar_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.code','BAYAR')
        ->count('task_collect.id');
    }

//       //bayar_kontrak 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayar_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','BAYAR')
        ->distinct('accno')
        ->count('accno');
        
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayar_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayar_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        } else { 
            $bayar_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','BAYAR')
            ->distinct('accno')
            ->count('accno');
        }


      //bayar_angsuran 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayar_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('angstung','>','0')
        ->where('collectresult.code','BAYAR')
        ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayar_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayar_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectorid',$pic)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        } else { 
            $bayar_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('angstung','>','0')
            ->where('collectresult.code','BAYAR')
            ->sum('angstung');
        }

           //bayar_ospokok 
      if ($req->has('area') && empty($req->input('cabang')) && empty($req->input('kolektor')) ) {
        $bayar_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.code','BAYAR')
        ->distinct('ospokok')
        ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && empty($req->input('kolektor'))) {
            $bayar_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.collector_area',$area)
            ->where('collectresult.collector_cabang',$cabang)
            ->where('collectresult.code','BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        } elseif ($req->has('area') && $req->has('cabang') && $req->has('kolektor')) { 
            $bayar_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
        ->where('collectresult.collector_area',$area)
        ->where('collectresult.collector_cabang',$cabang)
        ->where('collectorid',$pic)
        ->where('collectresult.code','BAYAR')
       ->distinct('ospokok')
        ->sum('ospokok');
        } else { 
            $bayar_ospokok = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->join('view_kretrans','view_kretrans.NO_REKENING','task_collect.accno')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])
            ->where('collectresult.code','BAYAR')
            ->distinct('ospokok')
            ->sum('ospokok');
        }
    
    //    $assign_kontrak = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->distinct('task_collect.accno')
    //    ->count('task_collect.accno');

    //    $assign_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->distinct('task_collect.accno')
    //    ->sum('detail_taskdraft.angstung');

    //    $assign_pokok = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereBetween('task_collect.assigndate',[$tgl_awal,$tgl_akhir])->orWhere('collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->distinct('task_collect.id')->sum('task_collect.ospokok');

//        $visit_ospokok = TaskCollect::join('collectresult'.'task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)
// // ->where('collectresult.code','KUNJUNGAN')
// ->distinct('task_collect.id')->sum('task_collect.ospokok');

       

       //VISIT
// $visit_task = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('collectresult.code','KUNJUNGAN')->count('task_collect.id');


// $visit_kontrak = TaskCollect::join('collectresult','task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('collectresult.code','KUNJUNGAN')->distinct('task_collect.id')->count('task_collect.id');


// SELECT  SUM(angstung) FROM detail_taskbulkdraft JOIN task_collect ON (task_collect.`taskcode`=detail_taskbulkdraft.`taskcode`) JOIN collectresult ON (collectresult.`taskid` =task_collect.`id`)  
//          WHERE MONTH(task_collect.`assigndate`) = '06' AND YEAR(task_collect.`assigndate`)='2020' 
//          AND (detail_taskbulkdraft.`angstung` > 0) 
//          AND collectresult.`collector_area`='BGR' 
//         AND collectresult.`collector_cabang`='35' 
//         AND collectresult.`collector_collectorcode` ='393' 
//          AND task_collect.`accno` IN (SELECT DISTINCT(task_collect.accno) FROM task_collect)
//          AND collectresult.`code`='KUNJUNGAN'; 
// $visit_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('detail_taskbulkdraft.angstung','>','0')->where('collectresult.code','KUNJUNGAN')->whereIn('accno','SELECT DISTINCT(task_collect.accno) FROM task_collect')->sum('detail_taskbulkdraft.angstung');


// $visit_ospokok = TaskCollect::join('collectresult', 'task_collect.id', 'collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)
//         ->where('collectresult.code','KUNJUNGAN')
//        ->distinct('task_collect.id')->sum('task_collect.ospokok');
       
// ->where('collectresult.code','KUNJUNGAN')
//->distinct('task_collect.id')->sum('task_collect.ospokok');


// $interaksi_task = TaskCollect::join('collectresult'.'task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('collectresult.code','INTERAKSI')->count('task_collect.id');


// $interaksi_kontrak = TaskCollect::join('collectresult'.'task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('collectresult.code','INTERAKSI')->distinct('task_collect.id')->count('task_collect.id');

// $interaksi_angsuran = Detailtaskdraft::join('task_collect','task_collect.taskcode','detail_taskbulkdraft.taskcode')->join('collectresult','task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('detail_taskbulkdraft.angstung','>','0')->where('collectresult.code','INTERAKSI')->sum('detail_taskbulkdraft.angstung');

// $interaksi_ospokok = TaskCollect::join('collectresult'.'task_collect.id','collectresult.taskid')->whereMonth('task_collect.assigndate',$month)->whereYear('task_collect.assigndate',$year)->orWhere('collectresult.collector_area',$area)->orWhere('collectresult.collector_cabang',$cabang)->orWhere('collectorid',$pic)->where('collectresult.code','INTERAKSI')->distinct('task_collect.id')->sum('task_collect.ospokok');

//dd($bayarjari_ospokok);

// if ($bayarjari_ospokok === 0 && $assign_ospokok === 0) {
//     $bayarjari_ospokok = 0;
//     $assign_ospokok = 0;
// } else {
//     $bayarjari_ospokok = $bayarjari_ospokok;
//     $assign_ospokok = $assign_ospokok;
// }

if ($bayarjari_ospokok === 0 && $assign_ospokok === 0) {
    $percent_current = "0 %";
}else {
$percent = $bayarjari_ospokok / $assign_ospokok;
$percent_current = number_format($percent * 100, 2) . '%';
}

if ($bayarjari_angsuran === 0 && $assign_angsuran === 0) {
    $percent_collrasio = "0 %";
} else {
    $percent_coll = $bayarjari_angsuran / $assign_angsuran;
    $percent_collrasio = number_format($percent_coll * 100, 2) . '%';

}


      // try {
       return response()->json([
        'code'   => 200,
        'status' => 'success',
        'data' =>  array(
            'tanggal_collect' => 
                   [
                       'nama' => 'Tanggal Collect',
                       'percent' => Carbon::now(),
                   ],
                'assignment' =>
                [
                    [
                        'nama' => 'Assign Task',
                        'jumlah' => $assign_task
                    ],
                    [
                        'nama' => 'Assign Kontrak',
                        'jumlah' => $assign_kontrak
                    ],
                    [
                        'nama' => 'Angsuran Assignment',
                        'jumlah' =>  $assign_angsuran
                    ],
                    [
                        'nama' => 'Ospokok Assignment',
                        'jumlah' => $assign_ospokok
                    ],
                ],

                'visit' =>
                [
                    [
                        'nama' => 'Visit Task',
                        'jumlah' => $visit_task
                    ],
                    [
                        'nama' => 'Visit Kontrak',
                        'jumlah' => $visit_kontrak
                    ],
                    [
                        'nama' => 'Angsuran Visit',
                        'jumlah' =>  $visit_angsuran
                    ],
                    [
                        'nama' => 'Ospokok Visit',
                        'jumlah' => $visit_ospokok
                    ],
                 ],

                'interaksi' =>
                [
                    [
                        'nama' => 'Interaksi Task',
                        'jumlah' => $interaksi_task
                    ],
                    [
                        'nama' => 'Interaksi Kontrak',
                        'jumlah' => $interaksi_kontrak
                    ],
                    [
                        'nama' => 'Angsuran Interaksi',
                        'jumlah' =>  $interaksi_angsuran
                    ],
                    [
                        'nama' => 'Ospokok Interaksi',
                        'jumlah' => $interaksi_ospokok
                    ],
                 ],

                 'janji_bayar' =>
                 [
                     [
                         'nama' => 'Janji Bayar Task',
                         'jumlah' => $jb_task
                     ],
                     [
                         'nama' => 'Janji Bayar Kontrak',
                         'jumlah' => $jb_kontrak
                     ],
                     [
                         'nama' => 'Angsuran Janji Bayar',
                         'jumlah' =>  $jb_angsuran
                     ],
                     [
                         'nama' => 'Ospokok Janji Bayar',
                         'jumlah' => $jb_ospokok
                     ],
                  ],

                  
                 'bayar' =>
                 [
                     [
                         'nama' => 'Bayar Task',
                         'jumlah' => $bayar_task
                     ],
                     [
                         'nama' => 'Bayar Kontrak',
                         'jumlah' => $bayar_kontrak
                     ],
                     [
                         'nama' => 'Angsuran Bayar',
                         'jumlah' =>  $bayar_angsuran
                     ],
                     [
                         'nama' => 'Ospokok Bayar',
                         'jumlah' => $bayar_ospokok
                     ],
                  ],

                  'bayar_via_jari' =>
                  [
                      [
                          'nama' => 'Bayar Jari Task',
                          'jumlah' => $bayarjari_task
                      ],
                      [
                          'nama' => 'Bayar Jari Kontrak',
                          'jumlah' => $bayarjari_kontrak
                      ],
                      [
                          'nama' => 'Angsuran Bayar Jari',
                          'jumlah' =>  $bayarjari_angsuran
                      ],
                      [
                          'nama' => 'Ospokok Bayar Jari',
                          'jumlah' => $bayarjari_ospokok
                      ],
                   ],
                   'current' => 
                   [
                       'nama' => 'current',
                       'percent' => $percent_current,
                   ],
                   'collection_rasio' => 
                   [
                       'nama' => 'collection ratio',
                       'percent' => $percent_collrasio
                   ]
        )
       ],200);
    // } catch (\Exception $e) {
    //     // $err = DB::connection('web')->rollback();
    //     return response()->json([
    //         'code'    => 501,
    //         'status'  => 'error',
    //         'message' => $e
    //     ], 501);
    // }
    }
}
    