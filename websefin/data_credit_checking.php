<link href="<?php echo base_url('assets/dist/css/datepicker.min.css')?>" rel="stylesheet" type="text/css">
<script src="<?php echo base_url('assets/dist/js/datepicker.js')?>"></script>
<div id="lihat_data_credit" class="content-wrapper" style="padding-left: 15px; padding-right: 15px;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Account Officer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Account Officer</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="box-body table-responsive no-padding">
                            <table id="example2" class="table table-bordered table-hover table-sm" style="white-space: nowrap;">
                                <thead style="font-size: 14px" class="bg-danger">
                                    <tr>
                                        <th>
                                            No
                                        </th>
                                        <th>
                                            Tanggal Transaksi
                                        </th>
                                        <th>
                                            No SO
                                        </th>
                                        <th>
                                            Asal Data
                                        </th>
                                        <th>
                                            Nama Marketing
                                        </th>
                                        <th>
                                            Nama Debitur
                                        </th>
                                        <th>
                                            Status DAS
                                        </th>
                                        <th>
                                            Status DS SPV
                                        </th>
                                        <th>
                                            Status AO
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="data_creditchecking" style="font-size: 13px">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </section> 
</div>


<div id="lihat_detail" class="content-wrapper" style="padding-left: 15px; padding-right: 15px;">
<!--     <section class="content-header"> -->
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Account Officer</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Data Account Officer</li>
            </ol>
          </div>
        </div>
    </div>
    <div id="form_detail" method="GET">
        <div class="col-md-12">
            <div class="box box-primary" style="background-color: #ffffff1f">
                <div class="box-header with-border">
                   <h3 class="box-title brand-text font-weight-light" style="font-size: 20px; height: 9px;">Data Pengajuan</h3>
                </div>
                <div class="box-body">
                    <div class="card mb-3" id="table">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_1" role="button" aria-expanded="false" aria-controls="collapse_1">
                                <b>DATA AO</b>
                            </a>
                        </div>
                        <form id="form_fasilitas">
                        <input type="hidden" name="id_fasilitas_pinjaman" value="">
                        <div class="card-body collapse" id="collapse_1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInput1">NO SO</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="nomor_so" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Nama Sales Officer</label>
                                        <input type="text" class="form-control" name="nama_so" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Asal Data<span class="required_notification">*</span></label>
                                        <select name="asal_data" id="select_asal_data" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;" readonly>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Nama Marketing 1/CGC/EGC/Tele Sales<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="nama_marketing" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Plafon<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control" name="plafon" aria-describedby="" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Tenor<span class="required_notification">*</span></label>
                                        <select name="tenor" id="tenor" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;">            
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Jenis Pinjaman</label>
                                        <select name="jenis_pinjaman_credit" id="jenis_pinjaman_credit" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Tujuan Pinjaman</label>
                                        <textarea id="tujuan_pinjaman_credit" name="tujuan_pinjaman_credit" class="form-control " onkeyup="this.value = this.value.toUpperCase()" rows="9" cols="40"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div style="float: right;">
                                <button type="submit" class="btn btn-success far fa-save submit">Update</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="card mb-3" id="table">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_2" role="button" aria-expanded="false" aria-controls="collapse_2">
                                <b>DATA CALON DEBITUR</b>
                            </a>
                        </div>
                        <div class="card-body collapse" id="collapse_2">
                            <form id="form_debitur">
                                <input type="hidden" id="id_debitur" name="id_debitur" value="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Nama Lengkap <small><i>(Sesuai KTP Tanpa Gelar)</i></small><span class="required_notification">*</span></label>
                                            <input type="text" name="nama_debitur" onkeyup="this.value = this.value.toUpperCase()" class="form-control ">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >Gelar Keagamaan</label>
                                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="gelar_keagamaan" >
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >Gelar Pendidikan</label>
                                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="gelar_pendidikan" >
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Jenis Kelamin<span class="required_notification">*</span></label>
                                                <select name="jenis_kelamin" id="jenis_kelamin1" class="form-control" onchange="showDiv()">
                                                    <option value="">-- Pilih Status Kelamin --</option>
                                                    <option id="L" value="L">LAKI-LAKI</option>
                                                    <option id="P" value="P">PEREMPUAN</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Status Pernikahan<span class="required_notification">*</span></label>
                                                <select name="status_nikah" id="status_nikah" class="form-control" onchange="showDiv()">
                                                    <option value="">-- Pilih Status Pernikahan --</option>
                                                    <option id="nikah" value="NIKAH">MENIKAH</option>
                                                    <option id="single" value="SINGLE">BELUM MENIKAH</option>
                                                    <option id="cerai" value="CERAI">JANDA / DUDA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >Tinggi Badan (cm)<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="tinggi_badan" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >Berat Badan (kg)<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="berat_badan" 3 onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Nama Ibu Kandung<span class="required_notification">*</span></label>
                                            <input type="text" name="ibu_kandung" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                  
                                        <div class="form-group">
                                            <label >No KTP<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="no_ktp" maxlength="16" onkeypress="return hanyaAngka(event)">
                                        </div>
                                        <div class="form-group">
                                            <label >No KTP KK<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="no_ktp_kk" maxlength="15" onkeypress="return hanyaAngka(event)">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >No KK<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="no_kk" maxlength="16" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >No NPWP</label>
                                                <input type="text" class="form-control" name="no_npwp" maxlength="15" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >Tempat Lahir</label>
                                                <input type="text" class="form-control" name="tempat_lahir" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Lahir<span class="required_notification">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                          <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="tgl_lahir_deb" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                                </div>      
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Agama<span class="required_notification">*</span></label>
                                            <select id="agama" name="agama" class="form-control" >
                                                <option value="">--Pilih--</option>
                                                <option id="agama_deb1" value="ISLAM">ISLAM</option>
                                                <option id="agama_deb2" value="KATHOLIK ">KATHOLIK</option>
                                                <option id="agama_deb3" value="KRISTEN">KRISTEN</option>
                                                <option id="agama_deb4" value="HINDU">HINDU</option>
                                                <option id="agama_deb5" value="BUDHA">BUDHA</option>
                                                <option id="agama_deb6" value="LAIN2 KEPERCAYAAN">LAIN2 KEPERCAYAAN</option>
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label >Alamat<small><i>(Sesuai KTP)</i></small></label>
                                                <input type="text" class="form-control" name="alamat_ktp" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RT</label>
                                                <input type="text" class="form-control"  name="rt_ktp" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RW</label>
                                                <input type="text" class="form-control" name="rw_ktp" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-group" id="select_provinsi_ktp">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_ktp" id="provinsi_ktp" class="form-control" >
                                            </select>
                                        </div>
                                        <div class="form-group" id="select_provinsi_ktp_dup">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_ktp" id="provinsi_ktp_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6" id="select_kabupaten_ktp">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_ktp" id="kabupaten_ktp" class="form-control" >
                                                </select>
                                            </div>    
                                            <div class="form-group col-md-6" id="select_kabupaten_ktp_dup">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_ktp" id="kabupaten_ktp_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>           
                                            <div class="form-group col-md-6" id="select_kecamatan_ktp">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_ktp" id="kecamatan_ktp" class="form-control" >
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-6" id="select_kecamatan_ktp_dup">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_ktp" id="kecamatan_ktp_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>   
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6" id="select_kelurahan_ktp">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_ktp" id="kelurahan_ktp" class="form-control" >
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-6" id="select_kelurahan_ktp_dup">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_ktp" id="kelurahan_ktp_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>    
                                            <div class="form-group col-md-6">
                                                <label>Kode POS</label>
                                                <input type="text" name="kode_pos_ktp" id="kode_pos_ktp" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group" style="margin-left: 10px;">
                                                <label for="exampleInput1" class="bmd-label-floating">Anak</label>
                                                <div class="form-group form-file-upload form-file-multiple">
                                                    <button type="button" class="btn btn-success add-row-pefindo" ><i class="fa fa-plus"></i>&nbsp; Tambah </button>&nbsp;
                                                    <button type="button" class="btn btn-danger delete-row-pefindo" ><i class="fa fa-trash"></i>&nbsp; Delete </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="table2" class="table table-hover table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th width="5">Pilih</th>
                                                        <th>Nama Anak</th>
                                                        <th>Tanggal Lahir Anak</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="checkbox" name="record_pefindo" width="5" onkeyup="javascript:this.value=this.value.toUpperCase()"></td>
                                                        <td><input type="text" class="form-control" name="nama_anak[]" onkeyup="this.value = this.value.toUpperCase()"></td>
                                                        <td><input type="text" name="tgl_lahir_anak[]" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
<!--                                             <div class="form-group col-md-6">
                                            <label >Nama Anak</label>
                                            <input type="text" class="form-control" name="nama_anak[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Tanggal Lahir Anak<span class="required_notification">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                      <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="tgl_lahir_anak[]" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                            </div>      
                                        </div> -->
                                    </div>
                                               
                                    <div class="col-md-6">
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label >Alamat<small><i>(Domisili)</i></small></label>
                                                <input type="text" class="form-control" name="alamat_domisili" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RT</label>
                                                <input type="text" class="form-control" name="rt_domisili" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RW</label>
                                                <input type="text" class="form-control"  name="rw_domisili" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-group" id="select_provinsi_domisili">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_domisili" id="provinsi_domisili" class="form-control" >
                                            </select>
                                        </div>
                                        <div class="form-group" id="select_provinsi_domisili_dup">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_domisili" id="provinsi_domisili_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6" id="select_kabupaten_domisili">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_domisili" id="kabupaten_domisili" class="form-control" >
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6" id="select_kabupaten_domisili_dup">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_domisili" id="kabupaten_domisili_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6" id="select_kecamatan_domisili">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_domisili" id="kecamatan_domisili" class="form-control" >
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6" id="select_kecamatan_domisili_dup">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_domisili" id="kecamatan_domisili_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6" id="select_kelurahan_domisili">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_domisili" id="kelurahan_domisili" class="form-control" >
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6" id="select_kelurahan_domisili_dup">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_domisili" id="kelurahan_domisili_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >Kode POS</label>
                                                <input type="text" class="form-control" name="kode_pos_domisili" maxlength="5" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Pendidikan Terakhir<span class="required_notification">*</span></label>
                                                <select id="select_pendidikan_terakhir" name="pendidikan_terakhir" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >Jumlah Tanggungan</label>
                                                <input type="text" class="form-control"  name="jumlah_tanggungan" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >No Telpon</label>
                                                <input type="text" class="form-control" name="no_telp"  maxlength="13" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >No Handphone</label>
                                                <input type="text" class="form-control" name="no_hp" maxlength="13" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInput1" >Alamat Korespondensi</label>
                                                <select id="alamat_surat" name="alamat_surat" class="form-control ">
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInput1" >Pekerjaan<span class="required_notification">*</span></label>
                                                <select name="pekerjaan_deb" class="form-control ">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label >Nama Perusahaan<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="nama_perusahaan" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >Posisi<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="posisi" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >Jenis Usaha<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="jenis_usaha" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label >Alamat Usaha/Kantor<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="alamat_usaha_kantor" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RT<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control"  name="rt_usaha_kantor" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label >RW<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control"  name="rw_usaha_kantor" maxlength="3" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>

                                        <div class="form-group" id="select_provinsi_kantor">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_kantor" id="provinsi_kantor" class="form-control" >
                                            </select>
                                        </div>
                                        <div class="form-group" id="select_provinsi_kantor_dup">
                                            <label>Provinsi<span class="required_notification">*</span></label>
                                            <select name="provinsi_kantor" id="provinsi_kantor_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6" id="select_kabupaten_kantor">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_kantor" id="kabupaten_kantor" class="form-control" >
                                                </select>
                                            </div>    
                                            <div class="form-group col-md-6" id="select_kabupaten_kantor_dup">
                                                <label>Kabupaten<span class="required_notification">*</span></label>
                                                <select name="kabupaten_kantor" id="kabupaten_kantor_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>           
                                            <div class="form-group col-md-6" id="select_kecamatan_kantor">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_kantor" id="kecamatan_kantor" class="form-control" >
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-6" id="select_kecamatan_kantor_dup">
                                                <label>Kecamatan<span class="required_notification">*</span></label>
                                                <select name="kecamatan_kantor" id="kecamatan_kantor_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>   
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6" id="select_kelurahan_kantor">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_kantor" id="kelurahan_kantor" class="form-control" >
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-6" id="select_kelurahan_kantor_dup">
                                                <label>Kelurahan<span class="required_notification">*</span></label>
                                                <select name="kelurahan_kantor" id="kelurahan_kantor_dup" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                </select>
                                            </div>    
                                            <div class="form-group col-md-6">
                                                <label>Kode POS</label>
                                                <input type="text" name="kode_pos_kantor" id="kode_pos_kantor" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Mulai Bekerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                          <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="masa_kerja_usaha" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                                </div>      
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >No Telpon Kantor/Usaha<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="no_telp_kantor_usaha" maxlength="13" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>   

                                    </div>

                                </div>
                                <div style="float: right;">
                                    <button type="submit" class="btn btn-success far fa-save submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card mb-3" id="form_pasangan_debitur">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_3" role="button" aria-expanded="false" aria-controls="collapse_3">
                                <b>DATA PASANGAN</b>
                            </a>
                        </div>
                        <div class="card-body collapse" id="collapse_3">
                            <form id="form_pasangan">
                                <input type="hidden" id="id_pasangan" name="id_pasangan" value="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput1" >Nama Lengkap <small><i>(Sesuai KTP)</i></small></label>
                                            <input type="text" name="nama_lengkap_pas" class="form-control " onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Nama Ibu Kandung</label>
                                            <input type="text" name="nama_ibu_kandung_pas" class="form-control " onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Jenis Kelamin</label>
                                            <select name="jenis_kelamin_pas" class="form-control ">
                                                <option value="">Pilih</option>
                                                <option id="L_pas" value="L">Laki-Laki</option>
                                                <option id="P_pas" value="P">Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Alamat<small><i>(Sesuai KTP)</i></small></label>
                                            <textarea name="alamat_ktp_pas" class="form-control " rows="5" cols="40" onkeyup="this.value = this.value.toUpperCase()"></textarea>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >No KTP</label>
                                                <input type="text" name="no_ktp_pas" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label >NIK KTP di KK</label>
                                                <input type="text" name="no_ktp_kk_pas" class="form-control" maxlength="16" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInput1" >NO NPWP</label>
                                                <input type="text" name="no_npwp_pas" class="form-control " maxlength="15" onkeypress="return hanyaAngka(event)">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInput1" >No Telpon</label>
                                                <input type="text" name="no_telp_pas" class="form-control " maxlength="13" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label >Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir_pas" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Lahir<span class="required_notification">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                          <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="tgl_lahir_pas" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                                </div>      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput1" class="bmd-label-floating">Pekerjaan</label>
                                            <select name="pekerjaan_pas" class="form-control ">
                                                <option value="">-- Pilih Pekerjaan --</option>
                                                <option value="KARYAWAN">Karyawan</option>
                                                <option value="PNS">PNS</option>
                                                <option value="WIRASWASTA">Wiraswasta</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" class="bmd-label-floating">Nama Perusahaan/Usaha</label>
                                            <input type="text" name="nama_perusahaan_pas" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                  <label class="bmd-label-floating">Posisi</label>
                                                  <input type="text" class="form-control" name="posisi_pekerjaan_pas" onkeyup="this.value = this.value.toUpperCase()">
                                                </div>
                                                <div class="form-group col-md-6">
                                                  <label class="bmd-label-floating">Jenis Usaha</label>
                                                  <input type="text" class="form-control" name="jenis_usaha_pas" onkeyup="this.value = this.value.toUpperCase()">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label>Alamat Usaha/Kantor</label>
                                                <input type="text" class="form-control" name="alamat_usaha_kantor_pas" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>RT</label>
                                                <input type="text" class="form-control" name="rt_kantor_usaha_pas" maxlength="3" onkeypress="return hanyaAngka(event)" >
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>RW</label>
                                                <input type="text" class="form-control" name="rw_kantor_usaha_pas"  maxlength="3" onkeypress="return hanyaAngka(event)" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Provinsi</label>
                                            <select name="provinsi_kantor_usaha_pas" id="select_provinsi_kantor_usaha_pas" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                 <option value="">--Pilih--</option>
                                             </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Kabupaten/Kota</label>
                                                <select id="select_kab_kantor_usaha_pas" name="id_kabupaten_kantor_usaha_pas" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>               
                                            <div class="form-group col-md-6">
                                                <label>Kecamatan</label>
                                                <select name="kecamatan_kantor_usaha_pas" id="select_kecamatan_kantor_usaha_pas" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                    <option value="">--Pilih--</option>
                                                 </select>
                                            </div>    
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Kelurahan</label>
                                                <select name="kelurahan_kantor_usaha_pas" id="select_kelurahan_kantor_usaha_pas" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>    
                                            <div class="form-group col-md-6">
                                                <label>Kode POS</label>
                                                <input type="text" id="kode_pos_kantor_usaha_pas" class="form-control" maxlength="5" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Mulai Bekerja</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                          <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="masa_kerja_lama_usaha_pas" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                                </div>      
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="bmd-label-floating">No Telpon</label>
                                                <input type="text" class="form-control" name="no_telp_kantor_usaha_pas" maxlength="13" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div style="float: right;">
                                            <button type="submit" class="btn btn-success far fa-save submit">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-3" id="formku">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_4" role="button" aria-expanded="false" aria-controls="collapse_3">
                                <b>DATA PENJAMIN</b>
                            </a>
                        </div>
                        <div class="card-body collapse" id="collapse_4">
                            <div class="box-body table-responsive no-padding">
                                <table id="example2" class="table table-bordered table-hover" style="min-width: 3300px">
                                    <thead style="font-size: 14px">
                                        <tr>
                                            <th>
                                                Nama KTP
                                            </th>
                                            <th>
                                                Nama Ibu Kandung
                                            </th>
                                            <th>
                                                No KTP
                                            </th>
                                            <th>
                                                No NPWP
                                            </th>
                                            <th>
                                               Tempat Lahir
                                            </th>
                                            <th>
                                                Tanggal Lahir
                                            </th>
                                            <th>
                                                Jenis Kelamin
                                            </th>
                                            <th>
                                                Alamat KTP
                                            </th>
                                            <th>
                                                No Telpon
                                            </th>
                                            <th>
                                                Hubungan Debitur
                                            </th>
                                            <th>
                                                Lampiran KTP
                                            </th>
                                            <th>
                                                Lampiran KTP Pasangan
                                            </th>
                                            <th>
                                                Lampiran KK
                                            </th>
                                            <th>
                                                Lampiran Buku Nikah
                                            </th>
<!--                                             <th>Pekerjaan
                                            </th>
                                            <th>
                                                Nama Perusahaan / Usaha
                                            </th>
                                            <th>
                                                Posisi
                                            </th>
                                            <th>
                                                Jenis Usaha
                                            </th>
                                            <th>
                                                Alamat Kantor / Usaha
                                            </th>
                                            <th>
                                                Provinsi Kantor/ Usaha
                                            </th>
                                            <th>
                                                Kabupaten/Kota Usaha
                                            </th>
                                            <th>
                                                Kecamatan Kantor / Usaha
                                            </th>
                                            <th>
                                                Kelurahan Kantor / Usaha
                                            </th>
                                            <th>
                                                Kode Pos
                                            </th>
                                            <th>
                                                Tanggal Bekerja
                                            </th>
                                            <th>
                                                No Telpon Kantor / Usaha
                                            </th> -->
<!--                                             <th>
                                                Jenis Penjamin
                                            </th> -->
                                            <th>
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="data_penjamin" style="font-size: 13px">
                                    </tbody>
                                </table>
                            </div>


<!--                                 <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Lengkap <small><i>(Sesuai KTP)</i></small></label>
                                        <input type="text" name="nama_ktp_pen[]" class="form-control" >
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Ibu Kandung</label>
                                        <input type="text" name="ibu_kandung_pen[]" class="form-control" >
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Tempat Lahir</label>
                                            <input type="text" name="tempat_lahir_pen[]" class="form-control" >
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Tanggal Lahir</label>
                                            <input type="text" name="tgl_lahir_pen[]" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <input type="text" name="jenis_kelamin_pen[]" class="form-control">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>No KTP</label>
                                            <input type="text" name="no_ktp_pen[]" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>No NPWP</label>
                                            <input type="text" name="no_npwp_pen[]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 15px;">
                                        <label>Alamat<small><i>(Sesuai KTP)</i></small></label>
                                        <textarea name="alamat_ktp_pen[]" class="form-control " rows="5" cols="40"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>No Telpon</label>
                                        <input type="text" name="no_telp_pen[]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Hubungan</label>
                                        <input type="text" name="hubungan_debitur_pen[]" class="form-control ">
                                    </div>
                                </div>
                                <div class="col-md-6">  
                                    <div class="form-group" style="margin-top: 36px;" id="ktp_penjamin">
                                        <label>Lihat KTP Penjamin<small><i>(Optional)</i></small></label>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_ktp_penjamin">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="ktp_pas_penjamin">
                                        <label>KTP Pasangan Penjamin<small><i>(Optional)</i></small></label>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_ktp_pas_penjamin">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="kk_penjamin">
                                        <label>Kartu Keluarga Penjamin</label>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_kk_penjamin">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="buku_nikah_penjamin">
                                        <label>Buku Nikah Penjamin</label>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_buku_nikah_penjamin">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="card mb-3" id="table">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_5" role="button" aria-expanded="false" aria-controls="collapse_5">
                                <b>LAMPIRAN</b>
                            </a>
                        </div>
                        <div class="card-body collapse" id="collapse_5">
                            <div class="row">
                                <div class="col-md-4" id="ktp">
                                    <div class="form-group">
                                        <label class="bmd-label-floating">KTP</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_ktp" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_ktp">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="kk">
                                    <div class="form-group">
                                        <label class="bmd-label-floating">Kartu Keluarga</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_kk" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_kk">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="sertifikat">
                                    <div class="form-group">
                                        <label>Sertifikat</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_sertifikat" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_sertifikat">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="pbb">
                                    <div class="form-group">
                                        <label for="exampleInput1" class="bmd-label-floating">Lampiran PBB</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_pbb " data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_pbb">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="imb">
                                    <div class="form-group">
                                        <label for="exampleInput1" class="bmd-label-floating">IMB</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_imb " data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_imb">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="form_ktp_pasangan">
                                    <div class="form-group">
                                        <label for="exampleInput1" class="bmd-label-floating">Lampiran KTP Pasangan</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_ktp" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_ktp_pasangan">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="form_buku_nikah">
                                    <div class="form-group">
                                        <label for="exampleInput1" class="bmd-label-floating">Lampiran Buku Nikah</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_ktp" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_buku_nikah">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="buku_tabungan">
                                    <div class="form-group">
                                        <label for="exampleInput1" class="bmd-label-floating">Lampiran Buku Tabungan</label>
                                        <button class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_edit_buku_tabungan" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                                        <div class="form-group form-file-upload form-file-multiple">
                                            <div class="col-md-6">
                                                <div class="well" id="gambar_buku_tabungan">
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="lampiran_ideb">
                                    <div class="form-group">
                                        <label>Lampiran IDEB</label>
                                        <div id="dataideb">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="lampiran_pefindo">
                                    <div class="form-group">
                                        <label>Lampiran PEFINDO</label>
                                        <div id="datapefindo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <form id="form_input_ao">
                <input type="hidden" name="id" value="">
                <!-- AREA CHART -->
                <div class="box box-primary" style="background-color: #ffffff1f">
                    <div class="box-header with-border">
                        <h3 class="box-title font-weight-light ao" style="font-size: 20px; height: 9px;">Input Memorandum AO</h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group" id="status_ao">
                            <label>Status<span class="required_notification">*</span></label>
                            <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="radio" id="radioPrimary2" value="1" name="status_ao">
                                <label for="radioPrimary2">Recommend
                                </label>
                              </div>
                              <div class="icheck-danger d-inline">
                                <input type="radio" id="radioPrimary3" value="2" name="status_ao">
                                <label for="radioPrimary3">Not Recommend
                                </label>
                              </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_6" role="button" aria-expanded="false" aria-controls="collapse_6">
                                    <b>VERIFIKASI DOKUMEN</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>KTP Calon Debitur<span class="required_notification">*</span></label>
                                            <select name="ver_ktp_calon_debitur" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>KTP Pasangan<span class="required_notification">*</span></label>
                                            <select name="ver_ktp_pasangan" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Kartu Keluarga<span class="required_notification">*</span></label>
                                            <select name="ver_kk" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Surat Akta Nikah<span class="required_notification">*</span></label>
                                            <select name="ver_akta_nikah" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Surat Cerai<span class="required_notification">*</span></label>
                                            <select name="ver_surat_cerai" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Surat Akta Kematian<span class="required_notification">*</span></label>
                                            <select name="ver_akta_kematian" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>SPPT PBB<span class="required_notification">*</span></label>
                                            <select name="ver_sttp_pbb" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sertifikat<span class="required_notification">*</span></label>
                                            <select name="ver_sertifikat" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>IMB<span class="required_notification">*</span></label>
                                            <select name="ver_imb" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Slip Gaji/Pembukuan Usaha<span class="required_notification">*</span></label>
                                            <select name="ver_slip_gaji" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Surat Keterangan Kerja/ Usaha<span class="required_notification">*</span></label>
                                            <select name="ver_keterangan_kerja_usaha" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Rekening Tabungan<span class="required_notification">*</span></label>
                                            <select name="ver_rekening_tabungan" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Data Penjamin<span class="required_notification">*</span></label>
                                            <select name="ver_data_penjamin" class="form-control" >
                                                <option value="">-- Pilih Verifikasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Catatan dan Analisa Sederhana<span class="required_notification">*</span></label>
                                            <textarea name="catatan_verifikasi" class="form-control " rows="3" cols="40" onkeyup="this.value = this.value.toUpperCase()"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_7" role="button" aria-expanded="false" aria-controls="collapse_7">
                                    <b>VALIDASI SAAT SURVEI</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Calon Debitur<span class="required_notification">*</span></label>
                                            <select name="val_calon_debitur" class="form-control" >
                                                <option value="">-- Pilih Validasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Pasangan Calon Debitur<span class="required_notification">*</span></label>
                                            <select name="val_pas_calon_debitur" class="form-control" >
                                                <option value="">-- Pilih Validasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Penjamin<span class="required_notification">*</span></label>
                                            <select name="val_penjamin" class="form-control" >
                                                <option value="">-- Pilih Validasi --</option>
                                                <option value="1">ADA</option>
                                                <option value="0">TIDAK ADA</option>
                                                <option value="2">ADA KEJANGGALAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Domisili Tempat Tinggal<span class="required_notification">*</span></label>
                                            <select name="val_domisili_tinggal" class="form-control" >
                                                <option value="">-- Pilih Validasi --</option>
                                                <option value="1">SESUAI</option>
                                                <option value="0">TIDAK SESUAI</option>
                                            </select>
                                        </div>
                                    </div>                    
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Agunan Tanah<span class="required_notification">*</span></label>
                                                <select name="val_agunan_tanah" class="form-control" >
                                                    <option value="">-- Pilih Validasi --</option>
                                                    <option value="1">SESUAI</option>
                                                    <option value="0">TIDAK SESUAI</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Agunan Kendaraan<span class="required_notification">*</span></label>
                                                <select name="val_agunan_kendaraan" class="form-control" >
                                                    <option value="">-- Pilih Validasi --</option>
                                                    <option value="1">SESUAI</option>
                                                    <option value="0">TIDAK SESUAI</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Pekerjaan<span class="required_notification">*</span></label>
                                                <select name="val_pekerjaan" class="form-control" >
                                                    <option value="">-- Pilih Validasi --</option>
                                                    <option value="1">SESUAI</option>
                                                    <option value="0">TIDAK SESUAI</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Usaha<span class="required_notification">*</span></label>
                                                <select name="val_usaha" class="form-control" >
                                                    <option value="">-- Pilih Validasi --</option>
                                                    <option value="1">SESUAI</option>
                                                    <option value="0">TIDAK SESUAI</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Cek Lingkungan<span class="required_notification">*</span></label>
                                            <select name="val_cek_lingkungan" class="form-control" >
                                                <option value="">-- Pilih Validasi --</option>
                                                <option value="1">SESUAI</option>
                                                <option value="0">TIDAK SESUAI</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Catatan Hasil Cek dan Analisa Sederhana<span class="required_notification">*</span></label>
                                            <textarea name="catatan_val" class="form-control " rows="5" cols="40" onkeyup="this.value = this.value.toUpperCase()"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_8" role="button" aria-expanded="false" aria-controls="collapse_8">
                                    <b>AGUNAN JAMINAN SERTIFIKAT</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_8">
                                <div class="col-md-12" id="">
                                    <div class="row" >
                                        <div class="form-group">
                                            <div class="form-group form-file-upload form-file-multiple">
                                                <button type="button" class="btn btn-success add-row" ><i class="fa fa-plus"></i>&nbsp; Tambah </button>&nbsp;
                                                <button type="button" class="btn btn-danger delete-row" ><i class="fa fa-trash"></i>&nbsp; Delete </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="table" class="table table-hover table-striped table-bordered nowrap">
                                            <thead>
                                                <tr>
                                                    <th width="5">#</th>
                                                    <th>Input Agunan Jaminan Sertifikat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="record" width="5" onkeyup="javascript:this.value=this.value.toUpperCase()">
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInput1" >Lokasi Agunan<span class="required_notification">*</span></label>
                                                                    <select name="tipe_lokasi_agunan[]" class="form-control ">
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="PERUM">PERUMAHAN</option>
                                                                        <option value="BIASA">NON PERUMAHAN</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-8">
                                                                        <label >Alamat Sesuai KTP<span class="required_notification">*</span></label>
                                                                        <input type="text" name="alamat_agunan[]" class="form-control" id="inputEmail4" onkeyup="this.value = this.value.toUpperCase()">
                                                                    </div>
                                                                    <div class="form-group col-md-2">
                                                                        <label >RT<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control" name="rt_agunan[]" maxlength="3" onkeypress="return hanyaAngka(event)">
                                                                    </div>
                                                                    <div class="form-group col-md-2">
                                                                        <label >RW<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control" name="rw_agunan[]" maxlength="3" onkeypress="return hanyaAngka(event)">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Provinsi<span class="required_notification">*</span></label>
                                                                    <select name="id_prov_agunan[]" id="select_provinsi_agunan" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                            
                                                                    </select>
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label>Kabupaten/Kota<span class="required_notification">*</span></label>
                                                                        <select id="select_kabupaten_agunan" name="id_kab_agunan[]" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                                            <option value="">--Pilih--</option>
                                                                        </select>
                                                                    </div>               
                                                                    <div class="form-group col-md-6">
                                                                        <label>Kecamatan<span class="required_notification">*</span></label>
                                                                        <select name="id_kec_agunan[]" id="select_kecamatan_agunan" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                                            <option value="">--Pilih--</option>
                                                                         </select>
                                                                    </div>    
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label>Kelurahan<span class="required_notification">*</span></label>
                                                                        <select name="id_kel_agunan[]" id="select_kelurahan_agunan" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" >
                                                                            <option value="">--Pilih--</option>
                                                                        </select>
                                                                    </div>    
                                                                    <div class="form-group col-md-6">
                                                                        <label>Kode POS<span class="required_notification">*</span></label>
                                                                        <input type="text" name="kode_pos_agunan" class="form-control" maxlength="5" onkeypress="return hanyaAngka(event)">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label >Luas Tanah (m2)<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control" name="luas_tanah[]" onkeypress="return hanyaAngka(event)">
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label >Luas Bangunan (m2)<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control"  name="luas_bangunan[]" onkeypress="return hanyaAngka(event)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInput1" >Nama Pemilik Sertifikat<span class="required_notification">*</span></label>
                                                                    <input type="text" name="nama_pemilik_sertifikat[]" class="form-control " onkeyup="this.value = this.value.toUpperCase()" >
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInput1" >Jenis Sertifikat</label>
                                                                    <select id="jenis_sertifikat" name="jenis_sertifikat[]" class="form-control " onchange="showshgb()">
                                                                        <option value="">-- Pilih --</option>
                                                                       <option value="SHM">SHM</option>
                                                                        <option value="SHGB">SHGB</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1" >Nomor Sertifikat<span class="required_notification">*</span></label>
                                                                    <input type="text" class="form-control" name="no_sertifikat[]" aria-describedby="">
                                                                </div>
                                                                <div class="form-group"> 
                                                                        <label for="exampleInputEmail1" >Tanggal & Nomor Ukur sertifikat</label>
                                                                        <input type="text" class="form-control" name="no_ukur_sertifikat[]">
                                                                        <!-- <input type="text" class="form-control" name="tgl_ukur_sertifikat[]"> -->
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label>Tanggal Berlaku SHGB<span id="wajib_shgb" class="required_notification">*</span></label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">
                                                                                  <i class="far fa-calendar-alt"></i>
                                                                                </span>
                                                                            </div>
                                                                            <input type="text" name="tgl_berlaku_shgb[]" class="datepicker-here form-control" data-language="en"  data-date-format="dd-mm-yyyy"/>
                                                                        </div>      
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label for="exampleInputEmail1" >Nomor IMB<small><i>(Jika Ada)</i></small></label>
                                                                        <input type="text" class="form-control" name="no_imb[]">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="exampleInputEmail1" >NJOP<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control uang" name="njop[]">
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label for="exampleInputEmail1" >NOP<span class="required_notification">*</span></label>
                                                                        <input type="text" class="form-control" name="nop[]">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                          <label>LAMPIRAN<span class="required_notification">*</span></label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputFile">Foto Agunan Tampak Depan<span class="required_notification">*</span></label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="agunan_bag_depan[]" class="custom-file-input" id="exampleInputFile">
                                                                            <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputFile">Foto Agunan Tampak Jalan<span class="required_notification">*</span></label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="agunan_bag_jalan[]" class="custom-file-input" id="exampleInputFile">
                                                                            <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputFile">Foto Agunan Tampak Ruang Tamu<span class="required_notification">*</span></label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="agunan_bag_ruangtamu[]" class="custom-file-input" id="exampleInputFile">
                                                                            <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputFile">Foto Agunan Tampak Dapur<span class="required_notification">*</span></label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="agunan_bag_dapur[]" class="custom-file-input" id="exampleInputFile">
                                                                            <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputFile">Foto Agunan Tampak Kamar Mandi<span class="required_notification">*</span></label>
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" name="agunan_bag_kamarmandi[]" class="custom-file-input" id="exampleInputFile">
                                                                            <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                 
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_10" role="button" aria-expanded="false" aria-controls="collapse_10">
                                    <b>PEMERIKSAAN TANAH DAN BANGUNAN</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_10">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Nama Penghuni<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="nama_penghuni_agunan[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label >Status Penghuni<span class="required_notification">*</span></label>
                                            <select name="status_penghuni_agunan[]" class="form-control ">
                                                <option value="">--Pilih Status Penghuni--</option>
                                                <option value="PEMILIK">PEMILIK</option>
                                                <option value="PENYEWA">PENYEWA</option>
                                                <option value="TIDAK DIHUNI">TIDAK DIHUNI</option>
                                                <option value="KELUARGA">KELUARGA</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Bentuk Agunan<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="bentuk_bangunan_agunan[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>   
                                        <div class="form-group">
                                            <label for="exampleInput1" >Kondisi Agunan<span class="required_notification">*</span></label>
                                            <select name="kondisi_bangunan_agunan[]" class="form-control ">
                                                <option value="">--Pilih--</option>
                                                <option value="SANGAT TERAWAT">SANGAT TERAWAT</option>
                                                <option value="CUKUP TERAWAT">CUKUP TERAWAT</option>
                                                <option value="KURANG TERAWAT">KURANG TERAWAT</option>
                                                <option value="TIDAK TERAWAT">TIDAK TERAWAT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Fasilitas<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="fasilitas_agunan[]" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Listrik (Kwh)<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="listrik_agunan[]" onkeypress="return hanyaAngka(event)">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Nilai Taksasi Bangunan<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" name="nilai_taksasi_bangunan[]" aria-describedby="" placeholder="">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Nilai Taksasi Agunan<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" name="nilai_taksasi_agunan[]" aria-describedby="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Taksasi Agunan<span class="required_notification">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                          <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="tgl_taksasi_agunan[]" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                                </div>      
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Nilai Likuidasi<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" name="nilai_likuidasi_agunan[]" aria-describedby="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Perusahaan Penililai Independen</label>
                                                <input type="text" class="form-control" name="perusahaan_penilai_independen[]" onkeyup="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Nilai Agunan Independen</label>
                                                <input type="text" class="form-control uang" name="nilai_agunan_independen[]" aria-describedby="" placeholder="">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">    
                                <a class="text-light" data-toggle="collapse" href="#collapse_9" role="button" aria-expanded="false" aria-controls="collapse_9">
                                    <b>AGUNAN KENDARAAN</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No BPKB</label>
                                            <input type="text" class="form-control" name="no_bpkb" aria-describedby="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Nama Pemilik</label>
                                            <input type="text" class="form-control" name="nama_pemilik_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Alamat Pemilik</label>
                                            <input type="text" class="form-control" name="alamat_pemilik_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Merk/Type</label>
                                            <input type="text" class="form-control" name="merk/type_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Jenis/Silinder</label>
                                            <input type="text" class="form-control" name="jenis/silinder_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No Rangka</label>
                                            <input type="text" class="form-control" name="no_rangka_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No Mesin</label>
                                            <input type="text" class="form-control" name="no_mesin_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Warna</label>
                                            <input type="text" class="form-control" name="warna_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Tahun</label>
                                            <input type="text" class="form-control" name="tahun_ken" onkeypress="return hanyaAngka(event)">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No POlisi</label>
                                            <input type="text" class="form-control" name="no_polisi_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No STNK</label>
                                            <input type="text" class="form-control" name="no_stnk_ken" onkeyup="this.value = this.value.toUpperCase()" >
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Expired Pajak</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                      <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="tgl_expired_pajak_ken" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                            </div>      
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Expired STNK</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                      <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="tgl_expired_stnk_ken" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                                            </div>      
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">No Faktur</label>
                                            <input type="text" class="form-control" name="no_faktur_ken"  onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                      <label>LAMPIRAN</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">Foto Agunan Kendaraan Tampak Depan</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="lamp_agunan_depan_ken[]" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Foto Agunan Kendaraan Tampak Kanan</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="lamp_agunan_kanan_ken[]" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Foto Agunan Kendaraan Tampak Kiri</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="lamp_agunan_kiri_ken[]" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">Foto Agunan Kendaraan Tampak Belakang</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="lamp_agunan_belakang_ken[]" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label for="exampleInputFile">Foto Agunan Kendaraan Tampak Dalam</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="lamp_agunan_dalam_ken[]" class="custom-file-input" id="exampleInputFile">
                                                    <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_11" role="button" aria-expanded="false" aria-controls="collapse_11">
                                    <b>PEMERIKSAAN KENDARAAN</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_11">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Nama Pengguna</label>
                                            <input type="text" class="form-control" name="nama_pengguna_ken[]" aria-describedby="" placeholder="" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" >Status Pengguna</label>
                                            <select name="status_pengguna_ken[]" class="form-control " style="margin-top: -11px;">
                                                <option value="">-- Pilih --</option>
                                                <option value="PEMILIK">PEMILIK</option>
                                                <option value="PENYEWA">PENYEWA</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Roda Kendaraan</label>
                                            <input type="text" class="form-control" name="jml_roda_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                         <div class="form-group">
                                            <label for="exampleInputEmail1">Kondisi Kendaraan</label>
                                            <input type="text" class="form-control" name="kondisi_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>   
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Keberadaan Kendaraan</label>
                                            <input type="text" class="form-control" name="keberadaan_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Body</label>
                                            <input type="text" class="form-control" name="body_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Interior</label>
                                            <input type="text" class="form-control" name="interior_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">KM</label>
                                            <input type="text" class="form-control" name="km_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Modifikasi</label>
                                            <input type="text" class="form-control" style="margin-bottom: 7px;" name="modifikasi_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Kelengkapan Aksesoris</label>
                                            <input type="text" class="form-control" name="aksesoris_ken[]" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_12" role="button" aria-expanded="false" aria-controls="collapse_12">
                                    <b>KAPASITAS BULANAN</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card-header bg-gradient-danger">
                                            <a class="text-light" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse_1">
                                            <b>Pemasukan</b>
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"  >Calon Debitur<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="pemasukan_debitur" name="pemasukan_debitur" onkeyup="total_pemasukan_kapasitas_bulanan();">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"  >Pasangan</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="pemasukan_pasangan" id="pemasukan_pasangan" onkeyup="total_pemasukan_kapasitas_bulanan();">
                                        </div>
                                         <div class="form-group">
                                            <label for="exampleInputEmail1"  >Penjamin</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="pemasukan_penjamin" id="pemasukan_penjamin" onkeyup="total_pemasukan_kapasitas_bulanan();">
                                        </div>   
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"  >Total Pemasukan</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="total_pemasukan" name="total_pemasukan" style="color: #000; font-weight: 500;" readonly>
                                        </div>  
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-header bg-gradient-danger">
                                            <a class="text-light" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse_1">
                                            <b>Pengeluaran</b>
                                            </a>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1"  >Rumah Tangga<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="biaya_rumah_tangga" name="biaya_rumah_tangga" aria-describedby="" placeholder="" onkeyup="total_pengeluaran_kapasitas_bulanan();">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1"  >Transportasi<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="biaya_transportasi" name="biaya_transportasi" aria-describedby="" placeholder="" onkeyup="total_pengeluaran_kapasitas_bulanan();">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Pendidikan<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="biaya_pendidikan" name="biaya_pendidikan" aria-describedby="" placeholder="" onkeyup="total_pengeluaran_kapasitas_bulanan();">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Telpon, Listrik dan Air<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="biaya_telp_listr_air" name="biaya_telp_listr_air" aria-describedby="" placeholder="" onkeyup="total_pengeluaran_kapasitas_bulanan();">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Lain-Lain<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="biaya_lain" name="biaya_lain" aria-describedby="" placeholder="" onkeyup="total_pengeluaran_kapasitas_bulanan();">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Total Pengeluaran</label>
                                            <input type="text"class="form-control uang" data-a-sep="." data-a-dec="," id="total_pengeluaran" name="total_pengeluaran" placeholder="" style="color: #000; font-weight: 500;" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_13" role="button" aria-expanded="false" aria-controls="collapse_13">
                                    <b>PENDAPATAN & PENGELUARAN USAHA(JIKA PENGUSAHA)</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_13">
                                <label style="font-size: 1.5em;font-weight: 300; margin-top: 23px">Pendapatan Usaha</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Tunai</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="pemasukan_tunai" name="pemasukan_tunai" value="0"   onkeyup="total_pendapatan_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Kredit</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="pemasukan_kredit" name="pemasukan_kredit" value="0" onkeyup="total_pendapatan_usaha();">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <label style="font-size: 1.5em;font-weight: 300; margin-top: 23px">Pengeluaran Usaha</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Sewa/Kontrak</label>
                                            <input type="text" class="form-control uang" id="biaya_sewa" name="biaya_sewa" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Gaji Pegawai</label>
                                            <input type="text" class="form-control uang" id="biaya_gaji_pegawai" name="biaya_gaji_pegawai" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Belanja Barang</label>
                                            <input type="text" class="form-control uang" id="biaya_belanja_brg" name="biaya_belanja_brg" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Telpon, Listrik dan Air</label>
                                            <input type="text" class="form-control uang" id="biaya_telp_listr_air_usaha" name="biaya_telp_listr_air_usaha" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Sampah & Keamanan</label>
                                            <input type="text" class="form-control uang" id="biaya_sampah_keamanan" name="biaya_sampah_keamanan" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Biaya Kirim Barang</label>
                                            <input type="text" class="form-control uang" id="biaya_kirim_barang" name="biaya_kirim_barang" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Pembayaran Hutang Dagang</label>
                                            <input type="text" class="form-control uang" id="biaya_hutang_dagang" name="biaya_hutang_dagang" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label >Angsuran Lain</label>
                                            <input type="text" class="form-control uang" id="biaya_angsuran" name="biaya_angsuran" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Lainnya</label>
                                            <input type="text" class="form-control uang" id="biaya_lain_lain" name="biaya_lain_lain" aria-describedby="" value="0" onkeyup="total_pengeluaran_usaha();">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <label style="font-size: 1.5em;font-weight: 300; margin-top: 23px">Total</label>
                                <div class="row">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Pendapatan Usaha</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="pendapatan_usaha" name="pendapatan_usaha" aria-describedby="" placeholder="" style="color: #000; font-weight: 500;" readonly>
                                            <input type="hidden" value="0" id="pendapatan_usaha_hide">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Pengeluaran Usaha</label>
                                            <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," id="pengeluaran_usaha" name="pengeluaran_usaha" aria-describedby="" placeholder="" style="color: #000; font-weight: 500;"  readonly>
                                            <input type="hidden" value="0" id="pengeluaran_usaha_hide">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" >Keuntungan Usaha</label>
                                            <input type="text" class="form-control auto" data-a-sep="." data-a-dec="," id="keuntungan_usaha" name="keuntungan_usaha" aria-describedby="" placeholder="" style="color: #000; font-weight: 500;" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3 ao" id="table">
                            <div class="card-header bg-gradient-danger">
                                <a class="text-light" data-toggle="collapse" href="#collapse_14" role="button" aria-expanded="false" aria-controls="collapse_14">
                                    <b>REKOMENDASI AO</b>
                                </a>
                            </div>
                            <div class="card-body collapse" id="collapse_14">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput1" >Tujuan Pinjaman<span class="required_notification">*</span></label>
                                            <textarea name="tujuan_pinjaman" class="form-control " rows="5" cols="40" onkeyup="this.value = this.value.toUpperCase()" style="height: 126px;"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="bmd-label-floating">Jenis Pinjaman<span class="required_notification">*</span></label>
                                            <select id="jenis_pinjaman" name="jenis_pinjaman" class="form-control" >
                                                <option value="">--Pilih--</option>
                                                <option value="KONSUMTIF">KONSUMTIF</option>
                                                <option value="MODAL">MODAL KERJA</option>
                                                <option value="INVESTASI">INVESTASI</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Produk<span class="required_notification">*</span></label>
                                            <select id="produk" name="produk" class="form-control" >
                                            </select>
                                        </div> 
                        <!--                 <div class="form-group">
                                            <label for="exampleInputEmail1" >Produk<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control" name="produk" onkeyup="this.value = this.value.toUpperCase()">
                                        </div> -->
                                         <div class="form-group">
                                            <label for="exampleInputEmail1" >Plafon Kredit<span class="required_notification">*</span></label>
                                            <input type="text" class="form-control uang" name="plafon_kredit" aria-describedby="" placeholder="">
                                        </div> 
                                       
                                        <div class="form-group">
                                            <label>Jangka Waktu<span class="required_notification">*</span></label>
                                            <select name="jangka_waktu" class="form-control" >
                                                <option value="">-- Pilih --</option>
                                                <option value="12">12</option>
                                                <option value="18">18</option>
                                                <option value="24">24</option>
                                                <option value="30">30</option>
                                                <option value="36">36</option>
                                                <option value="48">48</option>
                                                <option value="60">60</option>
                                            </select>
                                        </div> 
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Suku Bunga<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control" name="suku_bunga" aria-describedby="" placeholder="">
                                            </div>   
                                             <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1" >Angsuran / Bln<span class="required_notification">*</span></label>
                                                <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="pembayaran_bunga" aria-describedby="" placeholder="">
                                            </div>    
                                        </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Akad Kredit<span class="required_notification">*</span></label>
                                        <select name="akad_kredit" class="form-control" >
                                            <option value="">-- Pilih --</option>
                                            <option value="ADENDUM">ADENDUM</option>
                                            <option value="NOTARIS">NOTARIS</option>
                                            <option value="INTERNAL">INTERNAL</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Ikatan Agunan<span class="required_notification">*</span></label>
                                        <select name="ikatan_agunan" class="form-control" >
                                            <option value="">-- Pilih --</option>
                                            <option value="APHT">APHT</option>
                                            <option value="SKMHT">SKMHT</option>
                                            <option value="FIDUSIA">FIDUSIA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" >Analisa AO<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control" name="analisa_ao" onkeyup="this.value = this.value.toUpperCase()">
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" >Biaya Provisi<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="biaya_provisi" aria-describedby="" placeholder="">
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" >Biaya Administrasi<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="biaya_administrasi" aria-describedby="" placeholder="">
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" >Biaya Kredit Checking<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="biaya_credit_checking" aria-describedby="" placeholder="">
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" >Biaya Tabungan<span class="required_notification">*</span></label>
                                        <input type="text" class="form-control uang" data-a-sep="." data-a-dec="," name="biaya_tabungan" aria-describedby="" placeholder="">
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3 ao" id="table">
                        <div class="card-header bg-gradient-danger">
                            <a class="text-light" data-toggle="collapse" href="#collapse_65" role="button" aria-expanded="false" aria-controls="collapse_65">
                                <b>LAMPIRAN</b>
                            </a>
                        </div>
                        <div class="card-body collapse" id="collapse_65">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Surat Keterangan Kerja<span class="required_notification">*</span></label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="lamp_skk" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Slip Gaji</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="lamp_slip_gaji" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Form Persetujuan IDEB</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="form_persetujuan_ideb" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3"> 
                                    <div class="form-group">
                                        <label for="exampleInputFile">Buku Tabungan</label>
                                    </div>
                                    <a href="javascript:void(0)" class="btn btn-warning text-white" onclick="addFile_tabungan();" style="margin-top: -31px;"><i class="fa fa-paperclip"></i> Tambah Lampiran</a>
                                    <div id="set-tabungan"></div>
                                    <hr>
                                </div> 
                                <div class="col-md-3"> 
                                    <div class="form-group">
                                        <label for="exampleInputFile">Surat Keterangan Usaha</label>
                                    </div>
                                    <a href="javascript:void(0)" class="btn btn-warning text-white" onclick="addFile_surat_keterangan_usaha();" style="margin-top: -31px;"><i class="fa fa-paperclip"></i> Tambah Lampiran</a>
                                    <div id="set-surat-keterangan-usaha"></div>
                                    <hr>
                                </div> 
                                <div class="col-md-3"> 
                                    <div class="form-group">
                                        <label for="exampleInputFile">Pembukuan Usaha</label>
                                    </div>
                                    <a href="javascript:void(0)" class="btn btn-warning text-white" onclick="addFile_pembukuan_usaha();" style="margin-top: -31px;"><i class="fa fa-paperclip"></i> Tambah Lampiran</a>
                                    <div id="set-pembukuan_usaha"></div>
                                    <hr>
                                </div>
                                <div class="col-md-3"> 
                                    <div class="form-group">
                                        <label for="exampleInputFile">Foto Usaha</label>
                                    </div>
                                    <a href="javascript:void(0)" class="btn btn-warning text-white" onclick="addFile_foto_usaha();" style="margin-top: -31px;"><i class="fa fa-paperclip"></i> Tambah Lampiran</a>
                                    <div id="set-foto-usaha"></div>
                                    <hr>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-style: italic; color: #383a3a;">Catatan</label>
                        <textarea name="catatan_ao" style="width: 100%;margin-bottom: 34px;" rows="5" onkeyup="this.value = this.value.toUpperCase()"></textarea>
                        <button type="submit" id="submit_ao" class="btn btn-primary submit" style="float: right; margin-right: 7px;margin-top: -25px;">Simpan</button>
                    </div> 
                </div>
            </form>
        </div>
    </div>
</div>
<form id="form_edit_ktp_deb">
<input type="hidden" id="id_debitur_ktp" name="id_debitur_ktp">
<div class="modal fade in" id="modal_edit_ktp" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran KTP Debitur</label>
                    <div class="input-group">
                        <input type="file" name="lamp_ktp_deb" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_kk_deb">
<input type="hidden" id="id_debitur_kk" name="id_debitur_kk">
<div class="modal fade in" id="modal_edit_kk" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran KK Debitur</label>
                    <div class="input-group">
                        <input type="file" name="lamp_kk_deb" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_sertifikat_deb">
<input type="hidden" id="id_debitur_sertifikat" name="id_debitur_sertifikat">
<div class="modal fade in" id="modal_edit_sertifikat" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran Sertifikat</label>
                    <div class="input-group">
                        <input type="file" name="lamp_sertifikat_deb" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_pbb_deb">
<input type="hidden" id="id_debitur_pbb" name="id_debitur_pbb">
<div class="modal fade in" id="modal_edit_pbb" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran PBB</label>
                    <div class="input-group">
                        <input type="file" name="lamp_pbb_deb" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_imb_deb">
<input type="hidden" id="id_debitur_imb" name="id_debitur_imb">
<div class="modal fade in" id="modal_edit_imb" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran IMB</label>
                    <div class="input-group">
                        <input type="file" name="lamp_imb_deb" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_buku_tabungan_deb">
<input type="hidden" id="id_debitur_imb" name="id_debitur_buku_tabungan">
<div class="modal fade in" id="modal_edit_buku_tabungan" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Lampiran Buku Tabungan</label>
                    <div class="input-group">
                        <input type="file" name="lamp_buku_tabungan_deb[]" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_penjamin">
<div class="modal fade in" id="modal_penjamin" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Penjamin</h5>
                <button type="button" class="close close_deb" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:500px; overflow-y:scroll">
                <div class="row">
                    <input type="hidden" id="edit_id_penjamin" name="edit_id_penjamin">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap <small><i>(Sesuai KTP)</i></small><span class="required_notification">*</span></label>
                            <input type="text" name="nama_pen" onkeyup="this.value = this.value.toUpperCase()" class="form-control ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Ibu Kandung<span class="required_notification">*</span></label>
                            <input type="text" name="nama_ibu_kandung_pen" onkeyup="this.value = this.value.toUpperCase()" class="form-control ">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No KTP<span class="required_notification">*</span></label>
                            <input type="text" name="no_ktp_pen" onkeyup="this.value = this.value.toUpperCase()" class="form-control " maxlength="16">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No NPWP</label>
                            <input type="text" name="no_npwp_pen" onkeyup="this.value = this.value.toUpperCase()" class="form-control " maxlength="15">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tempat Lahir<span class="required_notification">*</span></label>
                            <input type="text" name="tempat_lahir_pen" onkeyup="this.value = this.value.toUpperCase()" class="form-control ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Lahir<span class="required_notification">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="tgl_lahir_pen" id="tgl_lahir_pen" class="datepicker-here form-control" data-language='en'  data-date-format="dd-mm-yyyy"/>
                            </div>      
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Kelamin<span class="required_notification">*</span></label>
                            <select name="jenis_kelamin_pen" id="select_jenis_kel_pen" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No Telpon<span class="required_notification">*</span></label>
                            <input type="text" name="notelp_pen" class="form-control" maxlength="13" onkeypress="return hanyaAngka(event)">
                        </div>
                    </div>
                </div>
                    <div class="form-group">
                        <label>Alamat<small><i>(Sesuai KTP)</i></small><span class="required_notification">*</span></label>
                        <textarea id="alamat_ktp_pas" name="alamat_ktp_pen" class="form-control" onkeyup="this.value = this.value.toUpperCase()" style="height: 125px;"></textarea>
                    </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-4" id="ktp_pen">
                            <div class="form-group">
                                <label for="exampleInput1" class="bmd-label-floating">KTP Penjamin</label>
                                <button type="button" id="lamp-ktp-pen" class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_ktp_pen" data-id="65"><i class="fa fa-paperclip"></i></button>
    <!--                             <div class="form-group form-file-upload form-file-multiple">
                                    <div class="col-md-6">
                                        <div class="well" id="gambar_ktp_penjamin">
                                        </div>  
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-4" id="kk_pen">
                            <div class="form-group">
                                <label for="exampleInput1" class="bmd-label-floating">KK Penjamin</label>
                                <button type="button" id="" class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_kk_pen" data-id="65"><i class="fa fa-paperclip"></i></button>
<!--                                 <div class="form-group form-file-upload form-file-multiple">
                                    <div class="col-md-6">
                                        <div class="well" id="gambar_kk_penjamin">
                                        </div>  
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-4" id="ktp_pas_pen">
                            <div class="form-group">
                                <label for="exampleInput1" class="bmd-label-floating">KTP Pasangan Penjamin</label>
                                <button type="button" class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_ktp_pasangan_pen" data-id="65"><i class="fa fa-paperclip"></i></button>
<!--                                 <div class="form-group form-file-upload form-file-multiple">
                                    <div class="col-md-6">
                                        <div class="well" id="gambar_ktp_pas_penjamin">
                                        </div>  
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-4" id="bukunikah_pen">
                            <div class="form-group">
                                <label for="exampleInput1" class="bmd-label-floating">Buku Nikah Penjamin</label>
                                <button type="button" class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_buku_nikah_pen" data-id="65"><i class="fa fa-paperclip"></i></button>
<!--                                 <div class="form-group form-file-upload form-file-multiple">
                                    <div class="col-md-6">
                                        <div class="well" id="gambar_bukunikah_penjamin">
                                        </div>  
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
<!--                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputFile">Lampiran KTP<span class="required_notification">*</span></label>
                            <button class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_sertifikat" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputFile">Lampiran KTP Pasangan<span class="required_notification">*</span></label>
                            <button class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_sertifikat" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputFile">Lampiran KK<span class="required_notification">*</span></label>
                            <button class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_sertifikat" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputFile">Lampiran Buku Nikah<span class="required_notification">*</span></label>
                            <button class="btn btn-info btn-sm edit" data-toggle="modal" data-target="#modal_edit_sertifikat" data-id="65"><i class="fa fa-pencil-alt"></i></button>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit">Save Changes</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_ktp_penjamin">
<div class="modal fade in" id="modal_edit_ktp_pen" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <label for="exampleInputFile">Ubah Lampiran KTP Penjamin</label>
                    <input type="hidden" id="id_ktp_pen" name="id_ktp_pen">
                    <div class="input-group">
                        <input type="file" name="lamp_ktp_pen" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_ktp_pas_penjamin">
<div class="modal fade in" id="modal_edit_ktp_pasangan_pen" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <input type="hidden" id="id_ktp_pasangan_pen" name="id_ktp_pasangan_pen">
                    <label for="exampleInputFile">Ubah KTP Pasangan Penjamin</label>
                    <div class="input-group">
                        <input type="file" name="lamp_ktp_pasangan_pen" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_buku_nikah_penjamin">
<div class="modal fade in" id="modal_edit_buku_nikah_pen" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <input type="hidden" id="id_buku_nikah_pen" name="id_buku_nikah_pen">
                    <label for="exampleInputFile">Ubah Lampiran Buku Nikah Penjamin</label>
                    <div class="input-group">
                        <input type="file" name="lamp_buku_nikah_pen" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<form id="form_edit_kk_penjamin">
<div class="modal fade in" id="modal_edit_kk_pen" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class='modal-body text-center'>
                <div class="form-group">
                    <input type="hidden" id="id_kk_pen" name="id_kk_pen">
                    <label for="exampleInputFile">Ubah Lampiran KK Penjamin</label>
                    <div class="input-group">
                        <input type="file" name="lamp_kk_pen" class="form-control" style="height: 45px">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger close_deb" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>

<div class="modal fade in" id="modal_load_data" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="load_data"></div>
    </div>
</div>

<!-- <script src="<?php echo base_url('assets/dist/js/datepicker.js')?>"></script> -->
<script src="<?php echo base_url('assets/dist/js/datepicker.en.js')?>"></script>
<script src="https://cdn.rawgit.com/igorescobar/jQuery-Mask-Plugin/1ef022ab/dist/jquery.mask.min.js"></script>
<script src="<?php echo base_url('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js')?>"></script>

<script type="text/javascript">
    //INPUT FILE
    $(document).ready(function () {
      bsCustomFileInput.init();
    });
    // =============================================================

    $(function(){
        var np = 0;
        $(".add-row-pefindo").click(function(){
        var datepicker = 'datepicker'+ np++;
        console.log(datepicker);

            var markup = '<tr><td><input type="checkbox" name="record_pefindo" width="5" onkeyup="javascript:this.value=this.value.toUpperCase()"></td><td><input type="text" class="form-control" name="nama_anak[]" onkeyup="this.value = this.value.toUpperCase()"></td><td><input type="text" name="tgl_lahir_anak[]" class="datepicker-here form-control" id="'+datepicker+'" data-language="en"  data-date-format="dd-mm-yyyy"/></td></tr>';
            $("#table2 ").append(markup);

            $("#datepicker0").datepicker();
            $("#datepicker1").datepicker();
            $("#datepicker2").datepicker();
            $("#datepicker3").datepicker();
            $("#datepicker4").datepicker();
            $("#datepicker5").datepicker();
            $("#datepicker6").datepicker();
            $("#datepicker7").datepicker();
            $("#datepicker8").datepicker();
            $("#datepicker9").datepicker();
            $("#datepicker10").datepicker();
            $("#datepicker11").datepicker();
        });
    
    

        $(".delete-row-pefindo").click(function(){
            $("table tbody").find('input[name="record_pefindo"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });

    })


    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })
    });
    function rubah(angka){
       var reverse = angka.toString().split('').reverse().join(''),
       ribuan = reverse.match(/\d{1,3}/g);
       ribuan = ribuan.join('.').split('').reverse().join('');
       return ribuan;
     }

    //HIDE
    hide_all = function(){
        $('#lihat_data_credit').hide();
        $('#lihat_detail').hide();
        $('#lihat_debitur_ao').hide();  
    }

    hide_all();
    $('#lihat_data_credit').show();
    $('#wajib_shgb').hide();
    $('.ao').hide();
    // =============================================================

    $('#radioPrimary2').click(function(e) {
        if( $('#radioPrimary2').prop('checked') ){
            $('.ao').show();
        }
    })
    $('#radioPrimary3').click(function(e) {
        if( $('#radioPrimary3').prop('checked') ){
            $('.ao').hide();
        }
    })



    //LOAD DATA PENGAJUAN
    get_credit_checking = function(opts, id){
        var url = '<?php echo config_item('api_url') ?>api/master/mao/';

        if(id != undefined){
                url+=id;
        }

        if(opts != undefined){
        var data = opts;
        }

        return $.ajax({
            // type : 'GET',
            url: url,
            data: data,
            headers: {
                'Authorization': 'Bearer '+localStorage.getItem('token')
            }
        });
    }

    load_data= function(){     
        get_credit_checking()
        .done(function(response){
            var data = response.data;
            var html = [];
            var no   = 0;
                if(data.length === 0 ){
                    var tr =[
                        '<tr valign="midle">',
                            '<td colspan="4">No Data</td>',
                        '</tr>'
                    ].join('\n');
                    $('#data_creditchecking').html(tr);

                    return;
                }
                $.each(data,function(index,item){
                    no++;

                    var status = item.ao.status;
                    if(status =='recommend'){
                        var disabled ="disabled";
                    }else{
                        var disabled ="";
                    }
                    
                    var tr = [
                        '<tr>',
                            '<td>'+ no+'</td>',
                            '<td>'+ item.tgl_transaksi +'</td>',
                            '<td>'+ item.nomor_so +'</td>',
                            '<td>'+ item.asal_data +'</td>',
                            '<td>'+ item.nama_marketing +'</td>',
                            '<td>'+ item.nama_debitur +'</td>',
                            '<td>'+ item.das.status +'</td>',
                            '<td>'+ item.hm.status +'</td>',
                            '<td>'+ item.ao.status +'</td>',
                            '<td style="width: 70px;">',
                                '<form method="post" target="_blank" action="<?php echo base_url().'index.php/report/Memo_ao' ?>"> <button type="button" '+disabled+' class="btn btn-info btn-sm edit"   data-target="#update" data="'+item.id+'"><i class="fas fa-pencil-alt"></i></button>',
                                '<button type="button" class="btn btn-warning btn-sm edit" onclick="click_detail()" data-target="#update" data="'+item.id+'"><i style="color: #fff;" class="fas fa-eye"></i></button>',
                                '<input type="hidden" name ="id" value="'+item.id+'"><button type="submit" class="btn btn-success btn-sm" ><i class="far fa-file-pdf"></i></a></form>',
                            '</td>',
                        '</tr>'
                    ].join('\n');
                    html.push(tr);
                });
                $('#data_creditchecking').html(html);
                $('#example2').DataTable({
                  "paging": true,
                  "retrieve": true,
                  "lengthChange": true,
                  "searching": true,
                  "ordering": true,
                  "info": true,
                  "autoWidth": false,
                });
        })
        .fail(function(response){
            $('#data_creditchecking').html('<tr><td colspan="4">Tidak ada data</td></tr>');
        });
    }
    load_data();
    $('#lihat_data_credit').show();
    // =============================================================



    //RUBAH RIBUAN
    function rubah(angka){
       var reverse = angka.toString().split('').reverse().join(''),
       ribuan = reverse.match(/\d{1,3}/g);
       ribuan = ribuan.join('.').split('').reverse().join('');
       return ribuan;
    }
    // =============================================================

    //TOTAL PEMASUKAN KAPASITAS BULANAN
    function total_pemasukan_kapasitas_bulanan() {

        var pemasukan_debitur   = (document.getElementById('pemasukan_debitur').value);
        pemasukan_debitur       = pemasukan_debitur.replace(/[^\d]/g,"");

        var pemasukan_pasangan  = (document.getElementById('pemasukan_pasangan').value);
        pemasukan_pasangan      = pemasukan_pasangan.replace(/[^\d]/g,"");

        var pemasukan_penjamin  = (document.getElementById('pemasukan_penjamin').value);
        pemasukan_penjamin      = pemasukan_penjamin.replace(/[^\d]/g,"");

        var formatter = new Intl.NumberFormat('id-ID', {
        //style: 'decimal', //tanpa decimal, tanpa Rp
        style: 'currency', //dengan 2 decimal, dengan Rp
        currency: 'IDR',

        });

        var cadeb               = Math.floor(pemasukan_debitur);
        var pasangan            = Math.floor(pemasukan_pasangan);
        var penjamin            = Math.floor(pemasukan_penjamin);

        var total               = cadeb + pasangan + penjamin;
        var total_pemasukan     = formatter.format(Math.abs(total));
        
        document.getElementById('total_pemasukan').value = total_pemasukan;
    }
    // =============================================================

    //TOTAL PENGELUARAN KAPASITAS BULANAN
    function total_pengeluaran_kapasitas_bulanan() {

        var pengeluaran_rumah_tangga        = (document.getElementById('biaya_rumah_tangga').value);
        pengeluaran_rumah_tangga            = pengeluaran_rumah_tangga.replace(/[^\d]/g,"");

        var pengeluaran_transportasi        = (document.getElementById('biaya_transportasi').value);
        pengeluaran_transportasi            = pengeluaran_transportasi.replace(/[^\d]/g,"");

        var pengeluaran_pendidikan          = (document.getElementById('biaya_pendidikan').value);
        pengeluaran_pendidikan              = pengeluaran_pendidikan.replace(/[^\d]/g,"");

        var pengeluaran_telpon_listrik_air  = (document.getElementById('biaya_telp_listr_air').value);
        pengeluaran_telpon_listrik_air      = pengeluaran_telpon_listrik_air.replace(/[^\d]/g,"");

        var pengeluaran_lain_lain           = (document.getElementById('biaya_lain').value);
        pengeluaran_lain_lain               = pengeluaran_lain_lain.replace(/[^\d]/g,"");

        var formatter = new Intl.NumberFormat('id-ID', {
        //style: 'decimal', //tanpa decimal, tanpa Rp
        style: 'currency', //dengan 2 decimal, dengan Rp
        currency: 'IDR',

        });

        var rumah_tangga                    = Math.floor(pengeluaran_rumah_tangga);
        var transportasi                    = Math.floor(pengeluaran_transportasi);
        var pendidikan                      = Math.floor(pengeluaran_pendidikan);
        var telpon_listrik_air              = Math.floor(pengeluaran_telpon_listrik_air);
        var lain_lain                       = Math.floor(pengeluaran_lain_lain);

        var total                           = rumah_tangga + transportasi + pendidikan + telpon_listrik_air + lain_lain;
        var total_pengeluaran               = formatter.format(Math.abs(total));
        
        document.getElementById('total_pengeluaran').value = total_pengeluaran;
    }
    // =============================================================

    //TOTAL PENDAPATAN USAHA
    function total_pendapatan_usaha() {

        var pemasukan_tunai   = (document.getElementById('pemasukan_tunai').value);
        pemasukan_tunai       = pemasukan_tunai.replace(/[^\d]/g,"");

        var pemasukan_kredit  = (document.getElementById('pemasukan_kredit').value);
        pemasukan_kredit      = pemasukan_kredit.replace(/[^\d]/g,"");

   
        var formatter = new Intl.NumberFormat('id-ID', {
        //style: 'decimal', //tanpa decimal, tanpa Rp
        style: 'currency', //dengan 2 decimal, dengan Rp
        currency: 'IDR',

        });

        var tunai             = Math.floor(pemasukan_tunai);
        var kredit            = Math.floor(pemasukan_kredit);


        var total             = tunai + kredit;
        var total_pemasukan   = formatter.format(Math.abs(total));
        
        document.getElementById('pendapatan_usaha').value = total_pemasukan;
        document.getElementById('pendapatan_usaha_hide').value = total;

        var pengeluaran_usaha  = (document.getElementById('pengeluaran_usaha_hide').value);
        pengeluaran_usaha      = pengeluaran_usaha.replace(/[^\d]/g,"");

        var pengeluaran            = Math.floor(pengeluaran_usaha);

        var total_keuntungan  =  total - pengeluaran;
        total_keuntungan      = formatter.format(Math.abs(total_keuntungan));
        document.getElementById('keuntungan_usaha').value = total_keuntungan;
    }
    // =============================================================

    //TOTAL PENGELUARAN USAHA
    function total_pengeluaran_usaha() {

        var pengeluaran_sewa                        = (document.getElementById('biaya_sewa').value);
        pengeluaran_sewa                            = pengeluaran_sewa.replace(/[^\d]/g,"");

        var pengeluaran_gaji_pegawai                = (document.getElementById('biaya_gaji_pegawai').value);
        pengeluaran_gaji_pegawai                    = pengeluaran_gaji_pegawai.replace(/[^\d]/g,"");

        var pengeluaran_belanja_barang              = (document.getElementById('biaya_belanja_brg').value);
        pengeluaran_belanja_barang                  = pengeluaran_belanja_barang.replace(/[^\d]/g,"");

        var pengeluaran_tlp_listrik_air_usaha       = (document.getElementById('biaya_telp_listr_air_usaha').value);
        pengeluaran_tlp_listrik_air_usaha           = pengeluaran_tlp_listrik_air_usaha.replace(/[^\d]/g,"");

        var pengeluaran_sampah_keamanan             = (document.getElementById('biaya_sampah_keamanan').value);
        pengeluaran_sampah_keamanan                 = pengeluaran_sampah_keamanan.replace(/[^\d]/g,"");

        var pengeluaran_biaya_kirim_barang          = (document.getElementById('biaya_kirim_barang').value);
        pengeluaran_biaya_kirim_barang              = pengeluaran_biaya_kirim_barang.replace(/[^\d]/g,"");

        var pengeluaran_pembayaran_hutang_dagang    = (document.getElementById('biaya_hutang_dagang').value);
        pengeluaran_pembayaran_hutang_dagang        = pengeluaran_pembayaran_hutang_dagang.replace(/[^\d]/g,"");

        var pengeluaran_angsuran_lain               = (document.getElementById('biaya_angsuran').value);
        pengeluaran_angsuran_lain                   = pengeluaran_angsuran_lain.replace(/[^\d]/g,"");

        var pengeluaran_lainnya                     = (document.getElementById('biaya_lain_lain').value);
        pengeluaran_lainnya                         = pengeluaran_lainnya.replace(/[^\d]/g,"");

        var pendapatan_usaha                        = (document.getElementById('pendapatan_usaha').value);
        pendapatan_usaha                            = pendapatan_usaha.replace(/[^\d]/g,"");
  
        var formatter = new Intl.NumberFormat('id-ID', {
        //style: 'decimal', //tanpa decimal, tanpa Rp
        style: 'currency', //dengan 2 decimal, dengan Rp
        currency: 'IDR',

        });

        var sewa                     = parseInt(pengeluaran_sewa);
        var gaji_pegawai             = parseInt(pengeluaran_gaji_pegawai);
        var belanja_barang           = Math.floor(pengeluaran_belanja_barang);
        var tlp_listrik_air          = Math.floor(pengeluaran_tlp_listrik_air_usaha);
        var sampah_keamanan          = Math.floor(pengeluaran_sampah_keamanan);
        var biaya_kirim_barang       = Math.floor(pengeluaran_biaya_kirim_barang);
        var pembayaran_hutang_dagang = Math.floor(pengeluaran_pembayaran_hutang_dagang);
        var angsuran_lain            = Math.floor(pengeluaran_angsuran_lain);
        var lainnya                  = Math.floor(pengeluaran_lainnya);

        var total                    = sewa + gaji_pegawai + belanja_barang + tlp_listrik_air + sampah_keamanan + biaya_kirim_barang + pembayaran_hutang_dagang + angsuran_lain + lainnya;

        var total_pengeluaran        = formatter.format(Math.abs(total));

        document.getElementById('pengeluaran_usaha').value = total_pengeluaran;
        document.getElementById('pengeluaran_usaha_hide').value = total;

        var pendapatan_usaha  = (document.getElementById('pendapatan_usaha_hide').value);
        pendapatan_usaha      = pendapatan_usaha.replace(/[^\d]/g,"");

        var pendapatan        = Math.floor(pendapatan_usaha);

        var total_keuntungan  =  pendapatan- total;
        total_keuntungan      = formatter.format(Math.abs(total_keuntungan));
        document.getElementById('keuntungan_usaha').value = total_keuntungan;
    }
    // =============================================================

    // BUTTON TAMBAH DAH HAPUS UNTUK UPLOAD BUKU TABUNGAN
    function addElement_tabungan(parent_tabungan_id, element_tabungan_tag, element_tabungan_id, html_tabungan) {
        var p = document.getElementById(parent_tabungan_id);
        var new_tabungan_element = document.createElement(element_tabungan_tag);
        new_tabungan_element.setAttribute('id', element_tabungan_id);
        new_tabungan_element.innerHTML = html_tabungan;
        p.appendChild(new_tabungan_element);
    }

    function removeElement_tabungan(element_tabungan_id) {
        var tabungan_element = document.getElementById(element_tabungan_id);
        tabungan_element.parentNode.removeChild(tabungan_element);
    }

    var tabungan_id = 0;
    function addFile_tabungan() {
        tabungan_id++;
        var html_tabungan =  '<input id="file_tabungan" type="file" name="lamp_buku_tabungan[]" accept="" style="width: 206px;"/>'+
                    ' <a href="javascript:void(0)" onclick="javascript:removeElement_tabungan(\'tabungan-' + tabungan_id + '\'); return false;">'+
                    '<i class="far fa-window-close fa-lg text-danger"></i></a>';
        addElement_tabungan('set-tabungan', 'p', 'tabungan-' + tabungan_id, html_tabungan);
    }
    // =============================================================

    // BUTTON TAMBAH DAH HAPUS UNTUK UPLOAD PEMBUKUAN USAHA
    function addElement_pembukuan_usaha(parent_pembukuan_usaha_id, element_pembukuan_usaha_tag, element_pembukuan_usaha_id, html_pembukuan_usaha) {
        var p = document.getElementById(parent_pembukuan_usaha_id);
        var new_pembukuan_usaha_element = document.createElement(element_pembukuan_usaha_tag);
        new_pembukuan_usaha_element.setAttribute('id', element_pembukuan_usaha_id);
        new_pembukuan_usaha_element.innerHTML = html_pembukuan_usaha;
        p.appendChild(new_pembukuan_usaha_element);
    }

    function removeElement_pembukuan_usaha(element_pembukuan_usaha_id) {
        var pembukuan_usaha_element = document.getElementById(element_pembukuan_usaha_id);
        pembukuan_usaha_element.parentNode.removeChild(pembukuan_usaha_element);
    }

    var pembukuan_usaha_id = 0;
    function addFile_pembukuan_usaha() {
        pembukuan_usaha_id++;
        var html_pembukuan_usaha ='<input id="file_pembukuan_usaha" type="file" name="foto_pembukuan_usaha[]" accept="" style="width: 206px;"/>'+
                    ' <a href="javascript:void(0)" onclick="javascript:removeElement_pembukuan_usaha(\'pembukuan_usaha-' + pembukuan_usaha_id + '\'); return false;">'+
                    '<i class="far fa-window-close fa-lg text-danger"></i></a>';
        addElement_pembukuan_usaha('set-pembukuan_usaha', 'p', 'pembukuan_usaha-' + pembukuan_usaha_id, html_pembukuan_usaha);
    }
    // =============================================================

    // BUTTON TAMBAH DAH HAPUS UNTUK UPLOAD SURAT KETERANGAN USAHA
    function addElement_surat_keterangan_usaha(parent_surat_keterangan_usaha_id, element_surat_keterangan_usaha_tag, element_surat_keterangan_usaha_id, html_surat_keterangan_usaha) {
        var p = document.getElementById(parent_surat_keterangan_usaha_id);
        var new_surat_keterangan_usaha_element = document.createElement(element_surat_keterangan_usaha_tag);
        new_surat_keterangan_usaha_element.setAttribute('id', element_surat_keterangan_usaha_id);
        new_surat_keterangan_usaha_element.innerHTML = html_surat_keterangan_usaha;
        p.appendChild(new_surat_keterangan_usaha_element);
    }

    function removeElement_surat_keterangan_usaha(element_surat_keterangan_usaha_id) {
        var surat_keterangan_usaha_element = document.getElementById(element_surat_keterangan_usaha_id);
        surat_keterangan_usaha_element.parentNode.removeChild(surat_keterangan_usaha_element);
    }

    var surat_keterangan_usaha_id = 0;
    function addFile_surat_keterangan_usaha() {
        surat_keterangan_usaha_id++;
        var html_surat_keterangan_usaha =  '<input id="file_surat_keterangan_usaha" type="file" name="lamp_sku[]" accept="" style="width: 206px;"/>'+
                    ' <a href="javascript:void(0)" onclick="javascript:removeElement_surat_keterangan_usaha(\'surat_keterangan_usaha-' + surat_keterangan_usaha_id + '\'); return false;">'+
                    '<i class="far fa-window-close fa-lg text-danger"></i></a>';
        addElement_surat_keterangan_usaha('set-surat-keterangan-usaha', 'p', 'surat_keterangan_usaha-' + surat_keterangan_usaha_id, html_surat_keterangan_usaha);
    }
    // =============================================================

    // BUTTON TAMBAH DAH HAPUS UNTUK UPLOAD FOTO USAHA
    function addElement_foto_usaha(parent_foto_usaha_id, element_foto_usaha_tag, element_foto_usaha_id, html_foto_usaha) {
        var p = document.getElementById(parent_foto_usaha_id);
        var new_foto_usaha_element = document.createElement(element_foto_usaha_tag);
        new_foto_usaha_element.setAttribute('id', element_foto_usaha_id);
        new_foto_usaha_element.innerHTML = html_foto_usaha;
        p.appendChild(new_foto_usaha_element);
    }

    function removeElement_foto_usaha(element_foto_usaha_id) {
        var foto_usaha_element = document.getElementById(element_foto_usaha_id);
        foto_usaha_element.parentNode.removeChild(foto_usaha_element);
    }

    var foto_usaha_id = 0;
    function addFile_foto_usaha() {
        foto_usaha_id++;
        var html_foto_usaha =  '<input id="file_foto_usaha" type="file" name="lamp_foto_usaha[]" accept="" style="width: 206px;"/>'+
                    ' <a href="javascript:void(0)" onclick="javascript:removeElement_foto_usaha(\'foto_usaha-' + foto_usaha_id + '\'); return false;">'+
                    '<i class="far fa-window-close fa-lg text-danger"></i></a>';
        addElement_foto_usaha('set-foto-usaha', 'p', 'foto_usaha-' + foto_usaha_id, html_foto_usaha);
    }
    // =============================================================

    // JIKA SERIFIKAT SHGB MAKA REQUIRED SHOW
    function showshgb(select){
        var select = document.getElementById("jenis_sertifikat");
        if(select.value == 'SHGB') {
            $('#wajib_shgb').show();   
        }
        else {
            $('#wajib_shgb').hide(); 
        }
    }   
    // =============================================================


    //HANYA ANGKA
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))

        return false;
        return true;
    }
    // =============================================================

    //RIBUAN
    $( '.uang' ).mask('0.000.000.000', {reverse: true});
    // =============================================================

    //TAMBAH AGUNAN
    $(".delete-row").click(function(){
        $("table tbody").find('input[name="record"]').each(function(){
            if($(this).is(":checked")){
                $(this).parents("tr").remove();
            }
        });
    });

    var np = 0;
    var nb = 0;
    var nc = 0;
    var nl = 0;
    var ns = 0;
    $(".add-row").click(function(){
    var iddd = 'select_provinsi_agunan'+ np++;
    var idkabb= 'select_kabupaten_agunan'+ nb++;
    var idkecc= 'select_kecamatan_agunan'+ nc++;
    var idkell= 'select_kelurahan_agunan'+ nl++;
    var idkodepos= 'select_kelurahan_agunan'+ ns++;

    var markup = '<tr><td><input type="checkbox" name="record" width="5" onkeyup="javascript:this.value=this.value.toUpperCase()"></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label for="exampleInput1">Lokasi Agunan<span class="required_notification">*</span></label><select name="tipe_lokasi_agunan[]" class="form-control "><option value="">-- Pilih --</option><option value="PERUM">PERUMAHAN</option><option value="BIASA">NON PERUMAHAN</option></select></div><div class="form-row"><div class="form-group col-md-8"><label >Alamat Sesuai KTP<span class="required_notification">*</span></label><input type="text" name="alamat_agunan[]" class="form-control" id="inputEmail4" ></div><div class="form-group col-md-2"><label >RT<span class="required_notification">*</span></label><input type="text" class="form-control" name="rt_agunan[]" maxlength="3" onkeypress="return hanyaAngka(event)"></div><div class="form-group col-md-2"><label >RW<span class="required_notification">*</span></label><input type="text" class="form-control" name="rw_agunan[]" maxlength="3" onkeypress="return hanyaAngka(event)"></div></div><div class="form-group"><label>Provinsi<span class="required_notification">*</span></label><select name="id_prov_agunan[]" id="'+iddd+'" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" ><option value="">--Pilih--</option></select></div><div class="form-row"><div class="form-group col-md-6"><label>Kabupaten/Kota<span class="required_notification">*</span></label><select id="'+idkabb+'" name="id_kab_agunan[]" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" ><option value="">--Pilih--</option></select></div><div class="form-group col-md-6"><label>Kecamatan<span class="required_notification">*</span></label><select name="id_kec_agunan[]" id="'+idkecc+'" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" ><option value="">--Pilih--</option></select></div></div><div class="form-row"><div class="form-group col-md-6"><label>Kelurahan<span class="required_notification">*</span></label><select name="id_kel_agunan[]" id="'+idkell+'" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" ><option value="">--Pilih--</option></select></div><div class="form-group col-md-6"><label>Kode POS<span class="required_notification">*</span></label><input type="text" name="kode_pos_agunan[]" id="'+idkodepos+'" class="form-control" maxlength="5" onkeypress="return hanyaAngka(event)"></div></div><div class="form-row"><div class="form-group col-md-6"><label >Luas Tanah<span class="required_notification">*</span></label><input type="text" class="form-control" name="luas_tanah[]" ></div><div class="form-group col-md-6"><label >Luas Bangunan<span class="required_notification">*</span></label><input type="text" class="form-control"  name="luas_bangunan[]" ></div></div></div><div class="col-md-6"><div class="form-group"><label for="exampleInput1" >Nama Pemilik Sertifikat<span class="required_notification">*</span></label><input type="text" name="nama_pemilik_sertifikat[]" class="form-control "></div><div class="form-group"><label for="exampleInput1" >Jenis Sertifikat</label><select name="jenis_sertifikat[]" class="form-control "><option value="">-- Pilih --</option><option value="SHM">SHM</option><option value="SHGB">SHGB</option></select></div><div class="form-group"><label for="exampleInputEmail1" >Nomor Sertifikat<span class="required_notification">*</span></label><input type="text" class="form-control" name="no_sertifikat[]" aria-describedby=""></div><div class="form-group"> <label for="exampleInputEmail1" >Tanggal & Nomor Ukur sertifikat</label><input type="text" class="form-control" name="no_ukur_sertifikat[]"></div><div class="form-row"><div class="form-group col-md-6"><label>Tanggal Berlaku SHGB<span class="required_notification">*</span></label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="text" name="tgl_berlaku_shgb[]" class="datepicker-here form-control" data-language="en"  data-date-format="dd-mm-yyyy"/></div></div><div class="form-group col-md-6"><label for="exampleInputEmail1" >Nomor IMB<small><i>(Jika Ada)</i></small></label><input type="text" class="form-control" name="no_imb[]"></div></div><div class="form-row"><div class="form-group col-md-6"><label for="exampleInputEmail1" >NJOP<span class="required_notification">*</span></label><input type="text" class="form-control uang" name="njop[]"></div><div class="form-group col-md-6"><label for="exampleInputEmail1" >NOP<span class="required_notification">*</span></label><input type="text" class="form-control" name="nop[]"></div></div></div></div><div class="form-group"><label>LAMPIRAN<span class="required_notification">*</span></label></div><div class="row"><div class="col-md-6"><div class="form-group"><label for="exampleInputFile">Foto Agunan Tampak Depan<span class="required_notification">*</span></label><div class="input-group"><div class="custom-file"><input type="file" name="agunan_bag_depan[]" class="custom-file-input" id="exampleInputFile"><label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label></div></div></div><div class="form-group"><label for="exampleInputFile">Foto Agunan Tampak Jalan<span class="required_notification">*</span></label><div class="input-group"><div class="custom-file"><input type="file" name="agunan_bag_jalan[]" class="custom-file-input" id="exampleInputFile"><label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label></div></div></div><div class="form-group"><label for="exampleInputFile">Foto Agunan Tampak Ruang Tamu<span class="required_notification">*</span></label><div class="input-group"><div class="custom-file"><input type="file" name="agunan_bag_ruangtamu[]" class="custom-file-input" id="exampleInputFile"><label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label></div></div></div></div><div class="col-md-6"><div class="form-group"><label for="exampleInputFile">Foto Agunan Tampak Dapur<span class="required_notification">*</span></label><div class="input-group"><div class="custom-file"><input type="file" name="agunan_bag_dapur[]" class="custom-file-input" id="exampleInputFile"><label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label></div></div></div> <div class="form-group"><label for="exampleInputFile">Foto Agunan Tampak Kamar Mandi<span class="required_notification">*</span></label><div class="input-group"><div class="custom-file"><input type="file" name="agunan_bag_kamarmandi[]" class="custom-file-input" id="exampleInputFile"><label class="custom-file-label" style="font-size: 11px" for="exampleInputFile">Choose file</label></div></div></div> </div></div> </td></tr>';
    $("#table tbody").append(markup);

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })
    });

    //RIBUAN
    $( '.uang' ).mask('0.000.000.000', {reverse: true});
    // =============================================================

    get_provinsi()
    .done(function(res){
        var select = [];
        $.each(res.data, function(i,e){
            var option = [
                '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id='+iddd+']').html(select);
    })

    $('#'+iddd).change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id='+idkabb+']').html(select);      
            }
        });
    }); 

    $('#'+idkabb).change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id='+idkecc+']').html(select);      
            }
        });
    }); 

    $('#'+idkecc).change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id='+idkell+']').html(select);      
            }
        });
    }); 

    $('#'+idkell).change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data;    
                $('#form_detail input[id='+idkodepos+']').val(data.kode_pos);   
            }
        });
    }); 
    $(document).ready(function () {
      bsCustomFileInput.init();
    });
});
// =============================================================

$(function(){
    var provinsi_ktp = [];
    $('#form_debitur select[id=provinsi_ktp ]').on('click', function(e){
        $('#select_provinsi_ktp').remove();
        $('#select_provinsi_ktp_dup').show();
        $('#select_kabupaten_ktp').remove();
        $('#select_kabupaten_ktp_dup').show();
        $('#select_kecamatan_ktp').remove();
        $('#select_kecamatan_ktp_dup').show();
        $('#select_kelurahan_ktp').remove();
        $('#select_kelurahan_ktp_dup').show();
        $('#kode_pos_ktp').val('');
    }) 

    $('#form_detail select[id=provinsi_domisili ]').on('click', function(e){
        $('#select_provinsi_domisili').remove();
        $('#select_provinsi_domisili_dup').show();
        $('#select_kabupaten_domisili').remove();
        $('#select_kabupaten_domisili_dup').show();
        $('#select_kecamatan_domisili').remove();
        $('#select_kecamatan_domisili_dup').show();
        $('#select_kelurahan_domisili').remove();
        $('#select_kelurahan_domisili_dup').show();
        $('#kode_pos_domisili').val('');
    })

    $('#form_detail select[id=provinsi_kantor ]').on('click', function(e){
        $('#select_provinsi_kantor').remove();
        $('#select_provinsi_kantor_dup').show();
        $('#select_kabupaten_kantor').remove();
        $('#select_kabupaten_kantor_dup').show();
        $('#select_kecamatan_kantor').remove();
        $('#select_kecamatan_kantor_dup').show();
        $('#select_kelurahan_kantor').remove();
        $('#select_kelurahan_kantor_dup').show();
        $('#kode_pos_kantor').val('');
    })


    get_produk = function(opts){
        var url = '<?php echo $this->config->item('api_url');?>produk';
        return $.ajax({
            type: 'GET',
            url : url
        });
    }

    get_produk()
    .done(function(res){
        var select = [];
        $.each(res.data, function(i,e){
            var option = [
            '<option value="'+e.nama_produk+'">'+e.nama_produk+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=produk]').html(select);
    })



    $('#provinsi_ktp_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail  select[id=kabupaten_ktp_dup]').html(select);      
            }
        });
    });

    $('#kabupaten_ktp_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kecamatan_ktp_dup]').html(select);      
            }
        });
    });

    $('#kecamatan_ktp_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kelurahan_ktp_dup]').html(select);      
            }
        });
    });

    $('#kelurahan_ktp_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data; 

                $('#form_detail input[id=kode_pos_ktp]').val(data.kode_pos);   
            }
        });
    });


    $('#provinsi_domisili_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail  select[id=kabupaten_domisili_dup]').html(select);      
            }
        });
    });

    $('#kabupaten_domisili_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kecamatan_domisili_dup]').html(select);      
            }
        });
    });

    $('#kecamatan_domisili_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kelurahan_domisili_dup]').html(select);      
            }
        });
    });

    $('#kelurahan_domisili_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data; 

                $('#form_detail input[name=kode_pos_domisili]').val(data.kode_pos);   
            }
        });
    });


    $('#provinsi_kantor_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                var select1 = '<option value="">--Pilih--</option>';
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail  select[id=kabupaten_kantor_dup]').html(select1+select);      
            }
        });
    });

    $('#kabupaten_kantor_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                var select1 = '<option value="">--Pilih--</option>';
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kecamatan_kantor_dup]').html(select1+select);      
            }
        });
    });

    $('#kecamatan_kantor_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
                var select = [];
                var select1 = '<option value="">--Pilih--</option>';
                $.each(res.data, function(i,e){
                    var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=kelurahan_kantor_dup]').html(select1+select);      
            }
        });
    });

    $('#kelurahan_kantor_dup').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data; 

                $('#form_detail input[name=kode_pos_kantor]').val(data.kode_pos);   
            }
        });
    });


    get_data_debitur = function(opts, id_debitur){
        var url = '<?php echo config_item('api_url') ?>api/debitur/';

        if(id_debitur != undefined){
                url+=id_debitur;
        }

        if(opts != undefined){
        var data = opts;
        }

        return $.ajax({
            // type : 'GET',
            url: url,
            data: data,
            headers: {
                'Authorization': 'Bearer '+localStorage.getItem('token')
            }
        });
    }

    get_data_pasangan = function(opts, id_pasangan){
        var url = '<?php echo config_item('api_url') ?>api/pasangan/';

        if(id_pasangan != undefined){
                url+=id_pasangan;
        }

        if(opts != undefined){
        var data = opts;
        }

        return $.ajax({
            // type : 'GET',
            url: url,
            data: data,
            headers: {
                'Authorization': 'Bearer '+localStorage.getItem('token')
            }
        });
    }

    get_data_fasilitas = function(opts, id_fasilitas){
        var url = '<?php echo config_item('api_url') ?>api/faspin/';

        if(id_fasilitas != undefined){
                url+=id_fasilitas;
        }

        if(opts != undefined){
        var data = opts;
        }

        return $.ajax({
            // type : 'GET',
            url: url,
            data: data,
            headers: {
                'Authorization': 'Bearer '+localStorage.getItem('token')
           }
        });
    }

    update_ao = function(opts,id){
        var data= opts;
        var url = '<?php echo $this->config->item('api_url');?>api/master/mao/'+id;
        return $.ajax({
            url: url,
            data: data,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function(){
                let html = 
                            "<div width='100%' class='text-center'>"+
                                "<i class='fa fa-spinner fa-spin fa-4x text-danger'></i><br><br>"+
                                "<a id='batal' href='javascript:void(0)' class='text-primary' data-dismiss='modal'>Batal</a>"+
                            "</div>";
                
                $('#load_data').html(html);
                $('#modal_load_data').modal('show');   
            },
            headers : {
                    'Authorization': 'Bearer '+localStorage.getItem('token')
                }
        });
    }


//UPDATE FASILITAS
$(function(){

    update_fasilitas = function(opts,id){
        var data= opts;
        var url = '<?php echo $this->config->item('api_url');?>api/master/mcc/'+id;
        return $.ajax({
            url: url,
            data: data,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function(){
            let html = 
                        "<div width='100%' class='text-center'>"+
                            "<i class='fa fa-spinner fa-spin fa-4x text-danger'></i><br><br>"+
                            "<a id='batal' href='javascript:void(0)' class='text-primary batal' data-dismiss='modal'>Batal</a>"+
                        "</div>";
            
            $('#load_data').html(html);
            $('#modal_load_data').modal('show');   
            },
            headers : {
                    'Authorization': 'Bearer '+localStorage.getItem('token')
                }
        });
    }



    //SUBMIT FASILITAS PINJAMAN
    $('#form_fasilitas').on('submit',function(e){
        var id = $('input[name=id_fasilitas_pinjaman]', this).val();
        e.preventDefault();
        var formData = new FormData();
    //     //Data Pasangan
        formData.append('id_asal_data',$('select[name=asal_data]',this).val());
        formData.append('nama_marketing',$('input[name=nama_marketing]',this).val());
        var plafon = $('input[name=plafon]',this).val();
        plafon = plafon.replace(/[^\d]/g,"");
        formData.append('plafon_pinjaman',plafon);
        formData.append('tenor_pinjaman',$('select[name=tenor]',this).val());
        formData.append('jenis_pinjaman',$('select[name=jenis_pinjaman_credit]',this).val());
        formData.append('tujuan_pinjaman',$('textarea[name=tujuan_pinjaman_credit]',this).val());

        update_fasilitas(formData, id)
        .done(function(res){
            var data = res.data;
                bootbox.alert('Data berhasil diubah',function(){
                $("#batal").click();
                load_data();
                load_fasilitas();
                // hide_all();

                // $('#lihat_detail').show();
            });
        })
        .fail(function(jqXHR){
            var data = jqXHR.responseJSON;
            var error = "";

            if(typeof data == 'string') {
                error = '<p>'+ data +'</p>';
            } else {
                $.each(data, function(index, item){
                    error += '<p>'+ item +'</p>'+"\n";
                });
            }
            bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                $("#batal").click();
            });
        });
    }); 

});
//=========================================================================================================
    update_debitur = function(opts,id){
        var data= opts;
        var url = '<?php echo $this->config->item('api_url');?>api/debitur/'+id;
        return $.ajax({
            url: url,
            data: data,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            headers : {
                    'Authorization': 'Bearer '+localStorage.getItem('token')
            },
            beforeSend: function(){
            let html = 
                        "<div width='100%' class='text-center'>"+
                            "<i class='fa fa-spinner fa-spin fa-4x text-danger'></i><br><br>"+
                            "<a id='batal' href='javascript:void(0)' class='text-primary' data-dismiss='modal'>Batal</a>"+
                        "</div>";
            
            $('#load_data').html(html);
            $('#modal_load_data').modal('show');   
        }
        });
    }

    update_pasangan = function(opts,id){
        var data= opts;
        var url = '<?php echo $this->config->item('api_url');?>/api/pasangan/'+id;
        return $.ajax({
            url: url,
            data: data,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            headers : {
                    'Authorization': 'Bearer '+localStorage.getItem('token')
            },
            beforeSend: function(){
            let html = 
                        "<div width='100%' class='text-center'>"+
                            "<i class='fa fa-spinner fa-spin fa-4x text-danger'></i><br><br>"+
                            "<a id='batal' href='javascript:void(0)' class='text-primary' data-dismiss='modal'>Batal</a>"+
                        "</div>";
            
            $('#load_data').html(html);
            $('#modal_load_data').modal('show');   
            }
        });
    }

        update_penjamin = function(opts,id){
            var data= opts;
            var url = '<?php echo $this->config->item('api_url');?>api/penjamin/'+id;
            return $.ajax({
                url: url,
                data: data,
                type: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function(){
                let html = 
                            "<div width='100%' class='text-center'>"+
                                "<i class='fa fa-spinner fa-spin fa-4x text-danger'></i><br><br>"+
                                "<a id='batal' href='javascript:void(0)' class='text-primary' data-dismiss='modal'>Batal</a>"+
                            "</div>";
                
                $('#load_data').html(html);
                $('#modal_load_data').modal('show');   
                },
                headers : {
                        'Authorization': 'Bearer '+localStorage.getItem('token')
                    }
            });
        }


    get_provinsi = function(opts){
        var url = '<?php echo $this->config->item('api_url');?>wilayah/provinsi';
        return $.ajax({
            type: 'GET',
            url : url,
            headers : {
                'Authorization': 'Bearer '+localStorage.getItem('token')
            }
        });
    }


    get_provinsi()
    .done(function(res){
        var select = [];
        $.each(res.data, function(i,e){
            var option = [
                '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=select_provinsi_agunan]').html(select);
    })

    get_provinsi()
    .done(function(res){
        var select = [];
        $.each(res.data, function(i,e){
            var option = [
                '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=select_provinsi_kantor_usaha_pas]').html(select);
    })

    get_provinsi()
    .done(function(res){
        var select = [];
        var select1 = '<option value="">--Pilih--</option>';
        $.each(res.data, function(i,e){
            var option = [
            '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=provinsi_ktp_dup]').html(select1+select);
    })

    get_provinsi()
    .done(function(res){
        var select = [];
        var select1 = '<option value="">--Pilih--</option>';
        $.each(res.data, function(i,e){
            var option = [
            '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=provinsi_kantor_dup]').html(select1+select);
    })

    get_provinsi()
    .done(function(res){
        var select = [];
        var select1 = '<option value="">--Pilih--</option>';
        $.each(res.data, function(i,e){
            var option = [
            '<option value="'+e.id+'">'+e.nama+'</option>'
            ].join('\n');
            select.push(option);
        });
        $('#form_detail select[id=provinsi_domisili_dup]').html(select1+select);
    })

    $('#select_provinsi_kantor_usaha_pas').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kab_kantor_usaha_pas]').html(select1+select);      
            }
        });
    }); 


    $('#select_provinsi_agunan').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/provinsi/"+id+"/kabupaten",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kabupaten_agunan]').html(select1+select);      
            }
        });
    });    


    $('#select_kab_kantor_usaha_pas').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kecamatan_kantor_usaha_pas').html(select1+select);      
            }
        });
    });  

    $('#select_kabupaten_agunan').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kabupaten/"+id+"/kecamatan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kecamatan_agunan]').html(select1+select);      
            }
        });
    });
 

    $('#select_kecamatan_kantor_usaha_pas').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kelurahan_kantor_usaha_pas]').html(select1+select);      
            }
        });
    }); 

    $('#select_kecamatan_agunan').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kecamatan/"+id+"/kelurahan",
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(res){
            var select = [];
            var select1 = '<option value="">--Pilih--</option>';
            $.each(res.data, function(i,e){
                var option = [
                    '<option value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_kelurahan_agunan]').html(select1+select);      
            }
        });
    }); 


    $('#select_kelurahan_kantor_usaha_pas').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data;    

                $('#form_detail input[id=kode_pos_kantor_usaha_pas]').val(data.kode_pos);   
            }
        });
    }); 

    $('#select_kelurahan_agunan').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo $this->config->item('api_url');?>wilayah/kelurahan/"+id,
            method : "GET",
            data : {id: id},
            async : false,
            dataType : 'json',

            success: function(response){
            var data = response.data;    
                $('#form_detail input[name=kode_pos_agunan]').val(data.kode_pos);   
            }
        });
    }); 

   get_asaldata = function(opts){
        var url = '<?php echo $this->config->item('api_url');?>/api/master/asal_data';
        return $.ajax({
            type: 'GET',
            url : url,
            headers : {
                'Authorization': 'Bearer '+localStorage.getItem('token')
            }
        });
    }

    // Click ubah
    $('#data_creditchecking').on('click', '.edit', function(e){
        e.preventDefault();

        var id = $(this).attr('data');
        var htmldata= [];
        var html = [];
        var html1 = [];
        var html2 = [];
        var html3 = [];
        var html4 = [];
        var html5 = [];
        var html6 = [];
        var html7 = [];
        var htmlideb = [];
        var htmlpefindo = [];
        
        get_credit_checking({}, id)
        .done(function(response){
            var data = response.data;

            console.log(data);

            // id = data.id;
            var id_debitur = data.data_debitur.id;
            var id_pasangan = data.data_pasangan.id;
            var id_credit = data.id;
            var id_fasilitas = data.fasilitas_pinjaman.id;

            if (id_pasangan = '0') {
                $('#form_pasangan_debitur').hide();
                $('#form_ktp_pasangan').hide();
                $('#form_buku_nikah').hide();
            }else{
                $('#form_pasangan_debitur').show();
                $('#form_ktp_pasangan').show();
                $('#form_buku_nikah').show();
            }

            $('#form_detail input[type=hidden][name=id]').val(data.id);
            $('#form_detail input[type=hidden][name=id_debitur]').val(data.data_debitur.id);
            $('#form_edit_ktp_deb input[type=hidden][name=id_debitur_ktp]').val(data.data_debitur.id);
            $('#form_edit_kk_deb input[type=hidden][name=id_debitur_kk]').val(data.data_debitur.id);
            $('#form_edit_sertifikat_deb input[type=hidden][name=id_debitur_sertifikat]').val(data.data_debitur.id);
            $('#form_edit_imb_deb input[type=hidden][name=id_debitur_imb]').val(data.data_debitur.id);
            $('#form_edit_pbb_deb input[type=hidden][name=id_debitur_pbb]').val(data.data_debitur.id);
            $('#form_edit_buku_tabungan_deb input[type=hidden][name=id_debitur_buku_tabungan]').val(data.data_debitur.id);
            
            // var id_penjamin = data.data_penjamin.id;
            $('#form_detail input[type=hidden][name=id_pasangan]').val(data.data_pasangan.id);

            $('#form_detail input[name=nomor_so]').val(data.nomor_so);
            $('#form_detail input[name=nama_so]').val(data.nama_so);

            get_asaldata()
            .done(function(res){
                var select = [];
                $.each(res.data, function(i,e){
                    var option = [
                        '<option id="'+e.id+'" value="'+e.id+'">'+e.nama+'</option>'
                    ].join('\n');
                    select.push(option);
                });
                $('#form_detail select[id=select_asal_data]').html(select);
            if (data.asaldata.id == ''+data.asaldata.id+'') {
             document.getElementById(''+data.asaldata.id+'').selected = "true";
            }
            })

            // $('#form_detail input[name=asal_data]').val(data.asaldata.nama);
            $('#form_detail input[name=nama_marketing]').val(data.nama_marketing);

            load_fasilitas= function(){  
                get_data_fasilitas({}, id_fasilitas)
                .done(function(response){
                var data_fasilitas = response.data;
                //calon debitur
                var plafon = (rubah(data_fasilitas.plafon));
                $('#form_detail input[name=plafon]').val(plafon);

                var select_tenor = [];
                    var option_tenor= [
                        '<option id="tenor12" value="12">12</option>',
                        '<option id="tenor18" value="18">18</option>',
                        '<option id="tenor24" value="24">24</option>',
                        '<option id="tenor30" value="30">30</option>',
                        '<option id="tenor36" value="36">36</option>',
                        '<option id="tenor48" value="48">48</option>',
                        '<option id="tenor60" value="60">60</option>'
                ].join('\n');
                select_tenor.push(option_tenor);
                $('#form_detail  select[name=tenor]').html(select_tenor);

                if (data_fasilitas.tenor == "12") {
                document.getElementById("tenor12").selected = "true"; 
                }else
                if (data_fasilitas.tenor == "18") {
                document.getElementById("tenor18").selected = "true";  
                }else
                if (data_fasilitas.tenor == "24") {
                document.getElementById("tenor24").selected = "true"; 
                }else
                if (data_fasilitas.tenor == "30") {
                document.getElementById("tenor30").selected = "true";  
                }else
                if (data_fasilitas.tenor == "36") {
                document.getElementById("tenor36").selected = "true";  
                }else
                if (data_fasilitas.tenor == "48") {
                document.getElementById("tenor48").selected = "true";  
                }
                if (data_fasilitas.tenor == "60") {
                document.getElementById("tenor60").selected = "true";  
                }


                var select_jenis_pinjaman = [];
                    var option_jenis_pinjaman = [
                        '<option value="">--Pilih--</option>',
                        '<option value="KONSUMTIF">KONSUMTIF</option>',
                        '<option value="MODAL">MODAL KERJA</option>',
                        '<option value="INVESTASI">INVESTASI</option>'                
                    ].join('\n');
                    select_jenis_pinjaman.push(option_jenis_pinjaman);
                $('#form_detail select[id=jenis_pinjaman]').html(select_jenis_pinjaman);

                var select_jenis_pinjaman1 = [];
                        var option_jenis_pinjaman1= [
                            '<option value="">--Pilih--</option>',
                            '<option id="konsumtif" value="KONSUMTIF">KONSUMTIF</option>',
                            '<option id="modal_kerja" value="MODAL">MODAL KERJA</option>',
                            '<option id="investasi" value="INVESTASI">INVESTASI</option>'
                    ].join('\n');
                    select_jenis_pinjaman1.push(option_jenis_pinjaman1);
                $('#form_detail  select[id=jenis_pinjaman_credit]').html(select_jenis_pinjaman1);

                if (data_fasilitas.jenis_pinjaman == "KONSUMTIF") {
                document.getElementById("konsumtif").selected = "true"; 
                }else
                if (data_fasilitas.jenis_pinjaman == "MODAL") {
                document.getElementById("modal_kerja").selected = "true";  
                }else
                if (data_fasilitas.jenis_pinjaman = "INVESTASI") {
                document.getElementById("investasi").selected = "true"; 
                }

                $('#form_detail input[name=jenis_pinjaman_credit]').val(data_fasilitas.jenis_pinjaman);
                $('#form_detail textarea[name=tujuan_pinjaman]').val(data_fasilitas.tujuan_pinjaman);
                $('#form_detail textarea[name=tujuan_pinjaman_credit]').val(data_fasilitas.tujuan_pinjaman);

                })
            }

            load_debitur= function(){  
            get_data_debitur({}, id_debitur)
            .done(function(response){
            var data_debitur = response.data;
            console.log(data_debitur);
            //calon debitur
            $('#select_provinsi_ktp_dup').hide();
            $('#select_kabupaten_ktp_dup').hide();
            $('#select_kecamatan_ktp_dup').hide();
            $('#select_kelurahan_ktp_dup').hide();

            $('#select_provinsi_domisili_dup').hide();
            $('#select_kabupaten_domisili_dup').hide();
            $('#select_kecamatan_domisili_dup').hide();
            $('#select_kelurahan_domisili_dup').hide();

            $('#select_provinsi_kantor_dup').hide();
            $('#select_kabupaten_kantor_dup').hide();
            $('#select_kecamatan_kantor_dup').hide();
            $('#select_kelurahan_kantor_dup').hide();


            $('#form_detail input[name=nama_debitur]').val(data_debitur.nama_lengkap);
            $('#form_detail input[name=gelar_keagamaan]').val(data_debitur.gelar_keagamaan);
            $('#form_detail input[name=gelar_pendidikan]').val(data_debitur.gelar_pendidikan);

            if (data_debitur.jenis_kelamin == "L") {
             document.getElementById("L").selected = "true";
            }else {
            document.getElementById("P").selected = "true";  
            }

            if (data_debitur.status_nikah == "NIKAH") {
             document.getElementById("nikah").selected = "true";
            }else
            if (data_debitur.status_nikah == "SINGLE") {
            document.getElementById("single").selected = "true";  
            }else
            if (data_debitur.status_nikah == "CERAI") {
            document.getElementById("cerai").selected = "true";  
            }

            $('#form_detail input[name=ibu_kandung]').val(data_debitur.ibu_kandung);
            $('#form_detail input[name=no_ktp]').val(data_debitur.no_ktp);
            $('#form_detail input[name=no_ktp_kk]').val(data_debitur.no_ktp_kk);
            $('#form_detail input[name=no_kk]').val(data_debitur.no_kk);
            $('#form_detail input[name=no_npwp]').val(data_debitur.no_npwp);
            $('#form_detail input[name=tempat_lahir]').val(data_debitur.tempat_lahir);
            $('#form_detail input[name=tgl_lahir_deb]').val(data_debitur.tgl_lahir);

            if (data_debitur.agama == "ISLAM") {
             document.getElementById("agama_deb1").selected = "true";
            }else
            if (data_debitur.agama == "KATHOLIK") {
            document.getElementById("agama_deb2").selected = "true";  
            }else
            if (data_debitur.agama == "KRISTEN") {
            document.getElementById("agama_deb3").selected = "true";  
            }else
            if (data_debitur.agama == "HINDU") {
            document.getElementById("agama_deb4").selected = "true";  
            }else
            if (data_debitur.agama == "BUDHA") {
            document.getElementById("agama_deb5").selected = "true";  
            }else
            if (data_debitur.agama == "LAIN2 KEPERCAYAAN") {
            document.getElementById("agama_deb6").selected = "true";  
            }


            $('#form_detail input[name=tinggi_badan]').val(data_debitur.tinggi_badan);
            $('#form_detail input[name=berat_badan]').val(data_debitur.berat_badan);
            $('#form_detail input[name=alamat_ktp]').val(data_debitur.alamat_ktp.alamat_singkat);
            $('#form_detail input[name=rt_ktp]').val(data_debitur.alamat_ktp.rt);
            $('#form_detail input[name=rw_ktp]').val(data_debitur.alamat_ktp.rw);

            var select_provinsi_ktp = [];
                var option_provinsi_ktp = [
                    '<option value="'+data_debitur.alamat_ktp.provinsi.id+'">'+data_debitur.alamat_ktp.provinsi.nama+'</option>'
                ].join('\n');
                select_provinsi_ktp.push(option_provinsi_ktp);
            $('#form_detail select[id=provinsi_ktp]').html(select_provinsi_ktp);
            var select_kabupaten_ktp = [];
                var option_kabupaten_ktp = [
                    '<option value="'+data_debitur.alamat_ktp.kabupaten.id+'">'+data_debitur.alamat_ktp.kabupaten.nama+'</option>'
                ].join('\n');
                select_kabupaten_ktp.push(option_kabupaten_ktp);
            $('#form_detail select[id=kabupaten_ktp]').html(select_kabupaten_ktp);

            var select_kecamatan_ktp = [];
                var option_kecamatan_ktp = [
                    '<option value="'+data_debitur.alamat_ktp.kecamatan.id+'">'+data_debitur.alamat_ktp.kecamatan.nama+'</option>'
                ].join('\n');
                select_kecamatan_ktp.push(option_kecamatan_ktp);
            $('#form_detail select[id=kecamatan_ktp]').html(select_kecamatan_ktp);

            var select_kelurahan_ktp = [];
                var option_kelurahan_ktp = [
                    '<option value="'+data_debitur.alamat_ktp.kelurahan.id+'">'+data_debitur.alamat_ktp.kelurahan.nama+'</option>'
                ].join('\n');
                select_kelurahan_ktp.push(option_kelurahan_ktp);
            $('#form_detail select[id=kelurahan_ktp]').html(select_kelurahan_ktp);

            $('#form_detail input[name=kode_pos_ktp]').val(data_debitur.alamat_ktp.kode_pos);
            $('#form_detail input[name=alamat_domisili]').val(data_debitur.alamat_domisili.alamat_singkat);
            $('#form_detail input[name=rt_domisili]').val(data_debitur.alamat_domisili.rt);
            $('#form_detail input[name=rw_domisili]').val(data_debitur.alamat_domisili.rw);

            var select_provinsi_domisili = [];
                var option_provinsi_domisili = [
                    '<option value="'+data_debitur.alamat_domisili.provinsi.id+'">'+data_debitur.alamat_domisili.provinsi.nama+'</option>'
                ].join('\n');
                select_provinsi_domisili.push(option_provinsi_domisili);
            $('#form_detail select[id=provinsi_domisili]').html(select_provinsi_domisili);

            var select_kabupaten_domisili = [];
                var option_kabupaten_domisili = [
                    '<option value="'+data_debitur.alamat_domisili.kabupaten.id+'">'+data_debitur.alamat_domisili.kabupaten.nama+'</option>'
                ].join('\n');
                select_kabupaten_domisili.push(option_kabupaten_domisili);
            $('#form_detail select[id=kabupaten_domisili]').html(select_kabupaten_domisili);

            var select_kecamatan_domisili = [];
                var option_kecamatan_domisili = [
                    '<option value="'+data_debitur.alamat_domisili.kecamatan.id+'">'+data_debitur.alamat_domisili.kecamatan.nama+'</option>'
                ].join('\n');
                select_kecamatan_domisili.push(option_kecamatan_domisili);
            $('#form_detail select[id=kecamatan_domisili]').html(select_kecamatan_domisili);

            var select_kelurahan_domisili = [];
                var option_kelurahan_domisili = [
                    '<option value="'+data_debitur.alamat_domisili.kelurahan.id+'">'+data_debitur.alamat_domisili.kelurahan.nama+'</option>'
                ].join('\n');
                select_kelurahan_domisili.push(option_kelurahan_domisili);
            $('#form_detail select[id=kelurahan_domisili]').html(select_kelurahan_domisili);

            $('#form_detail input[name=kode_pos_domisili]').val(data_debitur.alamat_domisili.kode_pos); 

            var select_pendidikan_terakhir = [];
                var option_pendidikan_terakhir = [
                    '<option value="'+data_debitur.pendidikan_terakhir+'">'+data_debitur.pendidikan_terakhir+'</option>',
                    '<option value="TIDAK TAMAT SD">TIDAK TAMAT SD</option>',
                    '<option value="SD">SD</option>',
                    '<option value="SMP">SMP</option>',
                    '<option value="SMA SEDERAJAT">SMA SEDERAJAT</option>',
                    '<option value="D1">D1</option>',
                    '<option value="D2">D2</option>',
                    '<option value="D3">D3</option>',
                    '<option value="S1">S1</option>',
                    '<option value="S2">S2</option>',
                    '<option value="S3">S3</option>'
                ].join('\n');
                select_pendidikan_terakhir.push(option_pendidikan_terakhir);
            $('#form_detail select[name=pendidikan_terakhir]').html(select_pendidikan_terakhir);
      
            $('#form_detail input[name=jumlah_tanggungan]').val(data_debitur.jumlah_tanggungan);   
            $('#form_detail input[name=no_telp]').val(data_debitur.no_telp);
            $('#form_detail input[name=no_hp]').val(data_debitur.no_hp);

            var select_alamat_surat = [];
                var option_alamat_surat = [
                    '<option value="'+data_debitur.alamat_surat+'">'+data_debitur.alamat_surat+'</option>',
                    '<option value="KTP">KTP</option>',
                    '<option value="DOMISILI">DOMISILI</option>',
                    '<option value="KANTOR">KANTOR</option>'
                ].join('\n');
                select_alamat_surat.push(option_alamat_surat);
            $('#form_detail select[name=alamat_surat]').html(select_alamat_surat);

            var select_pekerjaan_deb = [];
                var option_pekerjaan_deb = [
                    '<option value="'+data_debitur.pekerjaan.nama_pekerjaan+'">'+data_debitur.pekerjaan.nama_pekerjaan+'</option>',
                    '<option value="KARYAWAN">KARYAWAN</option>',
                    '<option value="PNS">PNS</option>',
                    '<option value="WIRASWASTA">WIRASWASTA</option>'
                ].join('\n');
                select_pekerjaan_deb.push(option_pekerjaan_deb);
            $('#form_detail select[name=pekerjaan_deb]').html(select_pekerjaan_deb);

            $('#form_detail input[name=posisi]').val(data_debitur.pekerjaan.posisi_pekerjaan);
            $('#form_detail input[name=nama_perusahaan]').val(data_debitur.pekerjaan.nama_tempat_kerja);
            $('#form_detail input[name=jenis_usaha]').val(data_debitur.pekerjaan.jenis_pekerjaan);
            $('#form_detail input[name=masa_kerja_usaha]').val(data_debitur.pekerjaan.tgl_mulai_kerja);
            $('#form_detail input[name=no_telp_kantor_usaha]').val(data_debitur.pekerjaan.no_telp_tempat_kerja);
            $('#form_detail input[name=alamat_usaha_kantor]').val(data_debitur.pekerjaan.alamat.alamat_singkat);
            $('#form_detail input[name=rt_usaha_kantor]').val(data_debitur.pekerjaan.alamat.rt);
            $('#form_detail input[name=rw_usaha_kantor]').val(data_debitur.pekerjaan.alamat.rw);
            $('#form_detail input[name=kode_pos_kantor]').val(data_debitur.pekerjaan.alamat.kode_pos); 

            var select_provinsi_kantor = [];
                var option_provinsi_kantor = [
                    '<option value="'+data_debitur.pekerjaan.alamat.provinsi.id+'">'+data_debitur.pekerjaan.alamat.provinsi.nama+'</option>'
                ].join('\n');
                select_provinsi_kantor.push(option_provinsi_kantor);
            $('#form_detail select[id=provinsi_kantor]').html(select_provinsi_kantor);

            var select_kabupaten_kantor = [];
                var option_kabupaten_kantor = [
                    '<option value="'+data_debitur.pekerjaan.alamat.kabupaten.id+'">'+data_debitur.pekerjaan.alamat.kabupaten.nama+'</option>'
                ].join('\n');
                select_kabupaten_kantor.push(option_kabupaten_kantor);
            $('#form_detail select[id=kabupaten_kantor]').html(select_kabupaten_kantor);

            var select_kecamatan_kantor = [];
                var option_kecamatan_kantor = [
                    '<option value="'+data_debitur.pekerjaan.alamat.kecamatan.id+'">'+data_debitur.pekerjaan.alamat.kecamatan.nama+'</option>'
                ].join('\n');
                select_kecamatan_kantor.push(option_kecamatan_kantor);
            $('#form_detail select[id=kecamatan_kantor]').html(select_kecamatan_kantor);

            var select_kelurahan_kantor = [];
                var option_kelurahan_kantor = [
                    '<option value="'+data_debitur.pekerjaan.alamat.kelurahan.id+'">'+data_debitur.pekerjaan.alamat.kelurahan.nama+'</option>'
                ].join('\n');
                select_kelurahan_kantor.push(option_kelurahan_kantor);
            $('#form_detail select[id=kelurahan_kantor]').html(select_kelurahan_kantor);

            $('#form_detail input[name=kode_pos_kantor]').val(data_debitur.pekerjaan.alamat.kode_pos); 

                var a1 = [
                '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_ktp+'" data-lightbox="example-set" data-title="Lampiran KTP Debitur"><img id="img_ktp_deb" class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_ktp+'" /> </a>'
                ].join('\n');
                html.push(a1);
                $('#gambar_ktp').html(html); 
       
                var b = [
                '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_kk+'" data-lightbox="example-set" data-title="Lampiran KK Debitur"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_kk+'" /> </a>'
                ].join('\n');
                html1.push(b);
                $('#gambar_kk').html(html1); 

                var c = [
                '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_sertifikat+'" data-lightbox="example-set" data-title="Lampiran Sertifkat Debitur"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_sertifikat+'" /> </a>'
                ].join('\n');
                html2.push(c);
                $('#gambar_sertifikat').html(html2);                 
                
      

                var d = [
                '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_sttp_pbb+'" data-lightbox="example-set" data-title="Lampiran PBB Debitur"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_sttp_pbb+'" /> </a>'
                ].join('\n');
                html3.push(d);
                $('#gambar_pbb').html(html3);                 
                
       
 
                var e = [
                '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_imb+'" data-lightbox="example-set" data-title="Lampiran IMB Debitur"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_imb+'" /> </a>'
                ].join('\n');
                html4.push(e);
                $('#gambar_imb').html(html4);
                 console.log(data_debitur.lampiran.lamp_buku_tabungan);

                var m = [
                '<a class="example-image-link target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_buku_tabungan+'" data-lightbox="example-set" data-title="Lampiran IMB Debitur"><img class="thumbnail img-responsive" alt="" src="<?php echo $this->config->item('img_url') ?>'+data_debitur.lampiran.lamp_buku_tabungan+'" /> </a>'
                ].join('\n');
                html7.push(m);
                $('#gambar_buku_tabungan').html(html7); 
            })
        }
            load_pasangan= function(){  
            get_data_pasangan({}, id_pasangan)
            .done(function(response){
            var data_pasangan = response.data;
            console.log(data_pasangan);
            //pasangan
            $('#form_detail input[name=nama_lengkap_pas]').val(data_pasangan.nama_lengkap);
            $('#form_detail input[name=nama_ibu_kandung_pas]').val(data_pasangan.nama_ibu_kandung);
            if (data_pasangan.jenis_kelamin == "L") {
             document.getElementById("L_pas").selected = "true";
            }else {
            document.getElementById("P_pas").selected = "true";  
            }

            $('#form_detail input[name=no_ktp_pas]').val(data_pasangan.no_ktp);
            $('#form_detail input[name=no_ktp_kk_pas]').val(data_pasangan.no_ktp_kk);
            $('#form_detail input[name=no_npwp_pas]').val(data_pasangan.no_npwp);
            $('#form_detail input[name=tempat_lahir_pas]').val(data_pasangan.tempat_lahir);
            $('#form_detail input[name=tgl_lahir_pas]').val(data_pasangan.tgl_lahir);
            $('#form_detail textarea[name=alamat_ktp_pas]').val(data_pasangan.alamat_ktp);
            $('#form_detail input[name=no_telp_pas]').val(data_pasangan.no_telp);
            $('#form_detail select[name=pekerjaan_pas]').val(data_pasangan.pekerjaan.nama_pekerjaan);
            $('#form_detail input[name=posisi_pekerjaan_pas]').val(data_pasangan.pekerjaan.posisi_pekerjaan);
            $('#form_detail input[name=nama_perusahaan_pas]').val(data_pasangan.pekerjaan.nama_tempat_kerja);
            $('#form_detail input[name=jenis_usaha_pas]').val(data_pasangan.pekerjaan.jenis_pekerjaan);
            $('#form_detail input[name=tgl_mulai_kerja_pas]').val(data_pasangan.tgl_mulai_kerja);
            $('#form_detail input[name=no_telp_tempat_kerja_pas]').val(data_pasangan.no_telp_tempat_kerja);
            $('#form_detail input[name=masa_kerja_lama_usaha_pas]').val(data_pasangan.pekerjaan.tgl_mulai_kerja);

            var select_pekerjaan_pas = [];
                var option_pekerjaan_pas = [
                    '<option value="KARYAWAN">KARYAWAN</option>',
                    '<option value="PNS">PNS</option>',
                    '<option value="WIRASWASTA">WIRASWASTA</option>',
                    '<option value="PENGURUS_RT">IBU RUMAH TANGGA</option>'
                ].join('\n');
                select_pekerjaan_pas.push(option_pekerjaan_pas);
            $('#form_detail select[name=pekerjaan_pas]').html(select_pekerjaan_pas);

            $('#form_detail input[name=posisi_pekerjaan_pas]').val(data_pasangan.pekerjaan.posisi_pekerjaan);
            $('#form_detail input[name=nama_perusahaan_pas]').val(data_pasangan.pekerjaan.nama_tempat_kerja);
            $('#form_detail input[name=jenis_usaha_pas]').val(data_pasangan.pekerjaan.jenis_pekerjaan);
            $('#form_detail input[name=masa_kerja_lama_usaha_pas]').val(data_pasangan.pekerjaan.tgl_mulai_kerja);
            $('#form_detail input[name=no_telp_kantor_usaha_pas]').val(data_pasangan.pekerjaan.no_telp_tempat_kerja);
            $('#form_detail input[name=alamat_usaha_kantor_pas]').val(data_pasangan.pekerjaan.alamat.alamat_singkat);
            $('#form_detail input[name=rt_kantor_usaha_pas]').val(data_pasangan.pekerjaan.alamat.rt);
            $('#form_detail input[name=rw_kantor_usaha_pas]').val(data_pasangan.pekerjaan.alamat.rw);
            $('#form_detail input[name=kode_pos_kantor_usaha_pas').val(data_pasangan.pekerjaan.alamat.kode_pos); 


            })
        }
        load_penjamin= function(){  
            var id_penjamin = {};
                get_data_penjamin = function(opts, id_penjamin){
                    var url = '<?php echo config_item('api_url') ?>api/penjamin/';

                    if(id_penjamin != undefined){
                            url+=id_penjamin;
                    }

                    if(opts != undefined){
                    var data = opts;
                    }

                    return $.ajax({
                        // type : 'GET',
                        url: url,
                        data: data,
                        headers: {
                            'Authorization': 'Bearer '+localStorage.getItem('token')
                        }
                    });
                }
            $.each(data.data_penjamin, function(index,item){
                var id_penjamin= [];
                 // $.each(data.penjamin, function(index,item){
                    id_penjamin= item.id;
                    // console.log(id_penjamin);
                    console.log(id_penjamin)
                    get_data_penjamin({}, id_penjamin)

                    .done(function(response){
                        
                        // console.log("response", response);
                        var datapenjamin = response.data;
                       
                        var html = [];
                        var no   = 0;

                        var jenis_kelamin_pen = "";

                        if(datapenjamin.jenis_kelamin_pen == 'L') {
                            jenis_kelamin_pen = 'LAKI-LAKI';
                        } else {
                            jenis_kelamin_pen = 'PEREMPUAN';
                        }

                        // var jenis_kelamin_pen;
                        // if (datapenjamin.jenis_kelamin_pen == 'L') {
                        //     jenis_kelamin_pen == 'LAKI-LAKI'
                        // }else if(datapenjamin.jenis_kelamin_pen == 'P'){
                        //     jenis_kelamin_pen == 'PEREMPUAN'
                        // }
                                var tr = [
                                    '<tr>',
                                        '<td style="width:210px">'+ datapenjamin.nama_ktp +'</td>',
                                        '<td style="width:210px">'+ datapenjamin.nama_ibu_kandung +'</td>',
                                        '<td>'+ datapenjamin.no_ktp +'</td>',
                                        '<td>'+ datapenjamin.no_npwp +'</td>',
                                        '<td style="width:135px">'+ datapenjamin.tempat_lahir +'</td>',
                                        '<td style="width:137px">'+ datapenjamin.tgl_lahir +'</td>',
                                        '<td style="width:160px">'+ jenis_kelamin_pen +'</td>',
                                        '<td style="width:285px">'+ datapenjamin.alamat_ktp +'</td>',
                                        '<td>'+ datapenjamin.no_telp +'</td>',
                                        '<td style="width:185px">'+ datapenjamin.hubungan_debitur +'</td>',
                                        '<td style="width:160px"><a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_ktp+'" data-lightbox="example-set" data-title="Lampiran KTP Debitur"><img class="thumbnail img-responsive" style="width:45px" alt="" src="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_ktp+'" /> </a> </td>',
                                        '<td style="width:200px"><a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_ktp_pasangan+'" data-lightbox="example-set" data-title="Lampiran KTP Debitur"><img class="thumbnail img-responsive" style="width:45px" alt="" src="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_ktp_pasangan+'" /> </a> </td>',
                                        '<td style="width:160px"><a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_kk+'" data-lightbox="example-set" data-title="Lampiran KTP Debitur"><img class="thumbnail img-responsive" style="width:45px"style="width:45px" alt="" src="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_kk+'" /> </a> </td>',
                                        '<td style="width:180px"><a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_buku_nikah+'" data-lightbox="example-set" data-title="Lampiran KTP Debitur"><img class="thumbnail img-responsive" style="width:45px" alt="" src="<?php echo $this->config->item('img_url') ?>'+datapenjamin.lampiran.lamp_buku_nikah+'" /> </a> </td>',
                                        '<td><button type="button" class="btn btn-info btn-sm edit submit" data-toggle="modal" data-target="#modal_penjamin"data="'+datapenjamin.id+'"><i class="fas fa-pencil-alt"></i></button></td>',
                                    '</tr>'
                                ].join('\n');
                                html.push(tr);
                
                            $('#data_penjamin').html(html);
                            
                            $('#example2').DataTable({
                              "paging": true,
                              "retrieve": true,
                              "lengthChange": true,
                              "searching": true,
                              "ordering": true,
                              "info": true,
                              "autoWidth": false,
                            });
                    })
                    .fail(function(response){
                        
                    });
                // })
                // var id_penjamin1 = e.id;
                 
            // load_penjamin= function(){
                // id_penjamin[data.penjamin[i]['id']] = data.penjamin[i]['id'];
                // var id_penjamin1 = e.id;
                
                
                // console.log(id_penjamin);
            // }

            });

        }
            load_fasilitas();
            load_debitur();
            load_pasangan();
            load_penjamin();
            console.log(load_penjamin);


            $.each(data.lampiran.ideb,function(item){
                var a = [
                '<a class="example-image-link" target="window.open()" download href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.ideb[item]+'"><p style="font-size: 13px; font-weight: 400;">'+ data.lampiran.ideb[item] +'</p></a>',
                ].join('\n');
                htmlideb.push(a);
            });
             $('#dataideb').html(htmlideb);

            $.each(data.lampiran.pefindo,function(item){
                var b = [
                '<a class="example-image-link" target="window.open()" download href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.pefindo[item]+'"><p style="font-size: 13px; font-weight: 400;">'+ data.lampiran.pefindo[item] +'</p></a>',
                ].join('\n');
                htmlpefindo.push(b);
            });
             $('#datapefindo').html(htmlpefindo);


            if (data.data_pasangan.lamp_buku_nikah == null ) {
                 $('#buku_nikah').hide();
            // $('imb').hide();  
            }else{
               
            var f = [
            '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.data_pasangan.lamp_buku_nikah+'" data-lightbox="example-set" data-title="Lampiran IMB Debitur"><img class="thumbnail img-responsive" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.data_pasangan.lamp_buku_nikah+'" /> </a>'
            ].join('\n');
            html5.push(f);
            $('#gambar_buku_nikah').html(html5);
            }  

            if (data.data_pasangan.lamp_ktp == null ) {
                 $('#ktp_pasangan').hide();
            // $('imb').hide();  
            }else{
               
            var g = [
            '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.data_pasangan.lamp_ktp+'" data-lightbox="example-set" data-title="Lampiran IMB Debitur"><img class="thumbnail img-responsive" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.data_pasangan.lamp_ktp+'" /> </a>'
            ].join('\n');
            html6.push(g);
            $('#gambar_ktp_pasangan').html(html6);
            }  
        })

        .fail(function(jqXHR){
            // $('#modal_data_credit').modal('close');
            bootbox.alert('Data tidak ditemukan, coba refresh kembali!!');

        });
        hide_all();
        $('#lihat_detail').show();

    });

        //submit ubah data debitur
        $('#form_debitur ').on('submit',function(e){
            var id = $('input[name=id_debitur]', this).val();
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('nama_lengkap',$('input[name=nama_debitur]',this).val());
            formData.append('gelar_keagamaan',$('input[name=gelar_keagamaan]',this).val());
            formData.append('gelar_pendidikan',$('input[name=gelar_pendidikan]',this).val());
            formData.append('jenis_kelamin',$('select[name=jenis_kelamin]',this).val());
            formData.append('status_nikah',$('select[name=status_nikah]',this).val());
            formData.append('ibu_kandung',$('input[name=ibu_kandung]',this).val());
            formData.append('no_ktp',$('input[name=no_ktp]',this).val());
            formData.append('no_ktp_kk',$('input[name=no_ktp_kk]',this).val());
            formData.append('no_kk',$('input[name=no_kk]',this).val());
            formData.append('no_npwp',$('input[name=no_npwp]',this).val());
            formData.append('tempat_lahir',$('input[name=tempat_lahir]',this).val());
            formData.append('tgl_lahir',$('input[name=tgl_lahir_deb]',this).val());
            formData.append('agama',$('select[name=agama]',this).val());
            formData.append('alamat_ktp',$('input[name=alamat_ktp]',this).val());
            formData.append('rt_ktp',$('input[name=rt_ktp]',this).val());
            formData.append('rw_ktp',$('input[name=rw_ktp]',this).val());
            formData.append('id_prov_ktp',$('select[name=provinsi_ktp]',this).val());
            formData.append('id_kab_ktp',$('select[name=kabupaten_ktp]',this).val());
            formData.append('id_kec_ktp',$('select[name=kecamatan_ktp]',this).val());
            formData.append('id_kel_ktp',$('select[name=kelurahan_ktp]',this).val());
            formData.append('alamat_domisili',$('input[name=alamat_domisili]',this).val());
            formData.append('rt_domisili',$('input[name=rt_domisili]',this).val());
            formData.append('rw_domisili',$('input[name=rw_domisili]',this).val());
            formData.append('id_prov_domisili',$('select[name=provinsi_domisili]',this).val());
            formData.append('id_kab_domisili',$('select[name=kabupaten_domisili]',this).val());
            formData.append('id_kec_domisili',$('select[name=kecamatan_domisili]',this).val());
            formData.append('id_kel_domisili',$('select[name=kelurahan_domisili]',this).val());
            formData.append('pendidikan_terakhir',$('select[name=pendidikan_terakhir]',this).val());
            formData.append('jumlah_tanggungan',$('input[name=jumlah_tanggungan]',this).val());
            formData.append('tinggi_badan',$('input[name=tinggi_badan]',this).val());
            formData.append('berat_badan',$('input[name=berat_badan]',this).val());

            $.each($('input[name="nama_anak[]"]'), function(i, e){
                formData.append('nama_anak[]', e.value);
            });
            $.each($('input[name="tgl_lahir_anak[]"]'), function(i, e){
                formData.append('tgl_lahir_anak[]', e.value);
            });
            formData.append('no_telp',$('input[name=no_telp]',this).val());
            formData.append('no_hp',$('input[name=no_hp]',this).val());
            formData.append('alamat_surat',$('select[name=alamat_surat]',this).val());
            formData.append('pekerjaan',$('select[name=pekerjaan_deb]',this).val());
            formData.append('nama_tempat_kerja',$('input[name=nama_perusahaan]',this).val());
            formData.append('posisi_pekerjaan',$('input[name=posisi]',this).val());
            formData.append('jenis_pekerjaan',$('input[name=jenis_usaha]',this).val());
            formData.append('alamat_tempat_kerja',$('input[name=alamat_usaha_kantor]',this).val());
            formData.append('rt_tempat_kerja',$('input[name=rt_usaha_kantor]',this).val());
            formData.append('rw_tempat_kerja',$('input[name=rw_usaha_kantor]',this).val());
            formData.append('id_prov_tempat_kerja',$('select[name=provinsi_kantor]',this).val());
            formData.append('id_kab_tempat_kerja',$('select[name=kabupaten_kantor]',this).val());
            formData.append('id_kec_tempat_kerja',$('select[name=kecamatan_kantor]',this).val());
            formData.append('id_kel_tempat_kerja',$('select[name=kelurahan_kantor]',this).val());
            formData.append('tgl_mulai_kerja',$('input[name=masa_kerja_usaha]',this).val());
            formData.append('no_telp_tempat_kerja',$('input[name=no_telp_kantor_usaha]',this).val());

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    load_data();
                    // load_debitur();
                });
            })
        .fail(function(jqXHR){
            var data = jqXHR.responseJSON.message;
            var error = "";
            if(typeof data == 'string') {
                error = '<p>'+ data +'</p>';
            }else{
                $.each(data.pekerjaan, function(index,item){
                    error+='<p>'+ item +"</p>";
                });
                $.each(data.id_prov_tempat_kerja, function(index,item){
                    error+='<p>'+ item +"</p>";
                });
                $.each(data.id_kec_tempat_kerja, function(index,item){
                    error+='<p>'+ item +"</p>";
                });
                $.each(data.id_kel_tempat_kerja, function(index,item){
                    error+='<p>'+ item +"</p>";
                });

            }
            bootbox.alert(error,function(){
                $("#batal").click();
            });
        });
        });  

        //submit ubah data pasangan
        $('#form_pasangan').on('submit',function(e){
            var id = $('input[name=id_pasangan]', this).val();
            e.preventDefault();
            var formData = new FormData();
        //     //Data Pasangan
            formData.append('nama_lengkap_pas',$('input[name=nama_lengkap_pas]',this).val());
            formData.append('nama_ibu_kandung_pas',$('input[name=nama_ibu_kandung_pas]',this).val());
            formData.append('jenis_kelamin_pas',$('select[name=jenis_kelamin_pas]',this).val());
            formData.append('alamat_ktp_pas',$('textarea[name=alamat_ktp_pas]',this).val());
            formData.append('no_ktp_pas',$('input[name=no_ktp_pas]',this).val());
            formData.append('no_ktp_kk_pas',$('input[name=no_ktp_kk_pas]',this).val());
            formData.append('no_npwp_pas',$('input[name=no_npwp_pas]',this).val());
            formData.append('tempat_lahir_pas',$('input[name=tempat_lahir_pas]',this).val());
            formData.append('tgl_lahir_pas',$('input[name=tgl_lahir_pas]',this).val());
            formData.append('no_telp_pas',$('input[name=no_telp_pas]',this).val());
            formData.append('pekerjaan_pas',$('select[name=pekerjaan_pas]',this).val());
            formData.append('nama_tempat_kerja_pas',$('input[name=nama_perusahaan_pas]',this).val());
            formData.append('posisi_pekerjaan_pas',$('input[name=posisi_pekerjaan_pas]',this).val());
            formData.append('jenis_pekerjaan_pas',$('input[name=jenis_usaha_pas]',this).val());
            formData.append('alamat_tempat_kerja_pas',$('input[name=alamat_usaha_kantor_pas]',this).val());
            formData.append('rt_tempat_kerja_pas',$('input[name=rt_kantor_usaha_pas]',this).val());
            formData.append('rw_tempat_kerja_pas',$('input[name=rw_kantor_usaha_pas]',this).val());
            formData.append('id_prov_tempat_kerja_pas',$('select[name=provinsi_kantor_usaha_pas]',this).val());
            formData.append('id_kab_tempat_kerja_pas',$('select[name=id_kabupaten_kantor_usaha_pas]',this).val());
            formData.append('id_kec_tempat_kerja_pas',$('select[name=kecamatan_kantor_usaha_pas]',this).val());
            formData.append('id_kel_tempat_kerja_pas',$('select[name=kelurahan_kantor_usaha_pas]',this).val());
            formData.append('tgl_mulai_kerja_pas',$('input[name=masa_kerja_lama_usaha_pas]',this).val());
            formData.append('no_telp_tempat_kerja_pas',$('input[name=no_telp_kantor_usaha_pas]',this).val());

            update_pasangan(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    load_data();
                    load_pasangan();
                    // hide_all();

                    // $('#lihat_detail').show();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!')
                // bootbox.alert(error);
            });
        });  

        //submit ubah data penjamin
        $('#form_penjamin').on('submit',function(e){
            var id = $('input[name=id_penjamin]', this).val();
            e.preventDefault();
            var formData = new FormData();

            // $.each($('input[name="nama_ktp_pen[]"]'), function(i, e){
            //     formData.append('nama_ktp_pen[]', e.value);
            // });
            // $.each($('input[name="nama_ibu_kandung_pen[]"]'), function(i, e){
            //     formData.append('nama_ibu_kandung_pen[]', e.value);
            // });
            // $.each($('input[name="no_ktp_pen[]"]'), function(i, e){
            //     formData.append('no_ktp_pen[]', e.value);
            // });
            // $.each($('input[name="no_npwp_pen[]"]'), function(i, e){
            //     formData.append('no_npwp_pen[]', e.value);
            // });
            // $.each($('input[name="tempat_lahir_pen[]"]'), function(i, e){
            //     formData.append('tempat_lahir_pen[]', e.value);
            // });
            // $.each($('input[name="tgl_lahir_pen[]"]'), function(i, e){
            //     formData.append('tgl_lahir_pen[]', e.value);
            // });
            // $.each($('select[name="jenis_kelamin_pen[]"]'), function(i, e){
            //     formData.append('jenis_kelamin_pen[]', e.value);
            // });
            // $.each($('textarea[name="alamat_ktp_pen[]"]'), function(i, e){
            //     formData.append('alamat_ktp_pen[]', e.value);
            // });
            // $.each($('input[name="no_telp_pen[]"]'), function(i, e){
            //     formData.append('no_telp_pen[]', e.value);
            // });
            // $.each($('input[name="hubungan_debitur_pen[]"]'), function(i, e){
            //     formData.append('hubungan_debitur_pen[]', e.value);
            // });
            // $.each($('input[name="pekerjaan_pen[]"]', this), function(i, e){
            //     formData.append('pekerjaan_pen[]', e.value);
            // });
            // $.each($('input[name="posisi_pekerjaan_pen[]"]', this), function(i, e){
            //     formData.append('posisi_pekerjaan_pen[]', e.value);
            // });
            // $.each($('input[name="nama_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('nama_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="jenis_pekerjaan_pen[]"]', this), function(i, e){
            //     formData.append('jenis_pekerjaan_pen[]', e.value);
            // });
            // $.each($('input[name="id_prov_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('id_prov_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="id_kab_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('id_kab_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="id_kec_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('id_kec_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="id_kel_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('id_kel_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="rt_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('rt_tempat_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="tgl_mulai_kerja_pen[]"]', this), function(i, e){
            //     formData.append('tgl_mulai_kerja_pen[]', e.value);
            // });
            // $.each($('input[name="no_telp_tempat_kerja_pen[]"]', this), function(i, e){
            //     formData.append('no_telp_tempat_kerja_pen[]', e.value);
            // });
            update_penjamin(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){

                    load_data(); 
                    load_penjamin();
                    // hide_all();

                    // $('#lihat_detail').show();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!')
                // bootbox.alert(error);
            });
        });  


        $('#form_edit_ktp_deb ').on('submit',function(e){
            var id = $('input[name=id_debitur_ktp]', this).val();
            console.log(id);
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('lamp_ktp',$('input[name=lamp_ktp_deb]',this)[0].files[0]);

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                })
                // bootbox.alert(error);
            });
            $(".close_deb").click();
        });  

        $('#form_edit_kk_deb ').on('submit',function(e){
            var id = $('input[name=id_debitur_kk]', this).val();
            console.log(id);
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('lamp_kk',$('input[name=lamp_kk_deb]',this)[0].files[0]);

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                })
                // bootbox.alert(error);
            });
        }); 

        $('#form_edit_sertifikat_deb ').on('submit',function(e){
            var id = $('input[name=id_debitur_sertifikat]', this).val();
            console.log(id);
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('lamp_sertifikat',$('input[name=lamp_sertifikat_deb]',this)[0].files[0]);

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                     $(".close_deb").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!')
                // bootbox.alert(error);
            });
           
        });


            $('#form_edit_pbb_deb').on('submit',function(e){
            var id = $('input[name=id_debitur_pbb]', this).val();
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('lamp_pbb',$('input[name=lamp_pbb_deb]',this)[0].files[0]);

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                })
                // bootbox.alert(error);
            });
           
        });

        $('#form_edit_imb_deb').on('submit',function(e){
            var id = $('input[name=id_debitur_imb]', this).val();
            console.log(id);
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            formData.append('lamp_imb',$('input[name=lamp_imb_deb]',this)[0].files[0]);

            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                })
                // bootbox.alert(error);
            });
           
        }); 

        $('#form_edit_buku_tabungan_deb').on('submit',function(e){
            var id = $('input[name=id_debitur_buku_tabungan]', this).val();
            console.log(id);
            e.preventDefault();
            var formData = new FormData();
            //Data Debitur
            $.each($('input[name="lamp_buku_tabungan_deb[]"]', this), function(i, e){
                formData.append('lamp_buku_tabungan[]', e.files[0]);
            });
        
            update_debitur(formData, id)
            .done(function(res){
                console.log(update_debitur);
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                })
            });
        }); 



        // klik submit update
        $('#form_input_ao').on('submit',function(e){
            e.preventDefault();
            var id = $('input[name=id]', this).val();
            var formData = new FormData();
            if( $('#radioPrimary3').prop('checked') ){
                formData.append('catatan_ao',$('textarea[name=catatan_ao]',this).val()); 
                formData.append('status_ao',$('input[type=radio][name=status_ao]:checked',this).val()); 
            }else{
            //verifikasi
            formData.append('ver_ktp_debt',$('select[name=ver_ktp_calon_debitur]',this).val()); 
            formData.append('ver_kk_debt',$('select[name=ver_kk]',this).val()); 
            formData.append('ver_akta_cerai_debt',$('select[name=ver_surat_cerai]',this).val()); 
            formData.append('ver_akta_kematian_debt',$('select[name=ver_akta_kematian]',this).val()); 
            formData.append('ver_rek_tabungan_debt',$('select[name=ver_rekening_tabungan]',this).val()); 
            formData.append('ver_sertifikat_debt',$('select[name=ver_sertifikat]',this).val()); 
            formData.append('ver_sttp_pbb_debt',$('select[name=ver_sttp_pbb]',this).val()); 
            formData.append('ver_imb_debt',$('select[name=ver_imb]',this).val()); 
            formData.append('ver_ktp_pasangan',$('select[name=ver_ktp_pasangan]',this).val()); 
            formData.append('ver_akta_nikah_pasangan',$('select[name=ver_akta_nikah]',this).val()); 
            formData.append('ver_data_penjamin',$('select[name=ver_data_penjamin]',this).val()); 
            formData.append('ver_sku_debt',$('select[name=ver_keterangan_kerja_usaha]',this).val()); 
            formData.append('ver_pembukuan_usaha_debt',$('select[name=ver_slip_gaji]',this).val()); 
            formData.append('catatan_verifikasi',$('textarea[name=catatan_ver]',this).val());             

            //Validasi
            formData.append('val_data_debt',$('select[name=val_calon_debitur]',this).val()); 
            formData.append('val_lingkungan_debt',$('select[name=val_cek_lingkungan]',this).val()); 
            formData.append('val_domisili_debt',$('select[name=val_domisili_tinggal]',this).val()); 
            formData.append('val_pekerjaan_debt',$('select[name=val_pekerjaan]',this).val()); 
            formData.append('val_data_pasangan',$('select[name=val_pas_calon_debitur]',this).val());
            formData.append('val_data_penjamin',$('select[name=val_penjamin]',this).val());
            formData.append('val_agunan_tanah',$('select[name=val_agunan_tanah]',this).val()); 
            formData.append('val_agunan_kendaraan',$('select[name=val_agunan_kendaraan]',this).val());
            formData.append('val_usaha_debt',$('select[name=val_usaha]',this).val());
            formData.append('catatan_validasi',$('textarea[name=catatan_val]',this).val()); 

            //Agunan Tanah
            formData.append('tipe_lokasi_agunan[]',$('select[name="tipe_lokasi_agunan[]"]',this).val());
            // $.each($('select[name="tipe_lokasi_agunan[]"]'), function(i, e){
            //     formData.append('tipe_lokasi_agunan[]', e.value);
            // });
            $.each($('input[name="alamat_agunan[]"]'), function(i, e){
                formData.append('alamat_agunan[]', e.value);
            });
            $.each($('select[name="id_prov_agunan[]"]'), function(i, e){
                formData.append('id_prov_agunan[]', e.value);
            });
            $.each($('select[name="id_kab_agunan[]"]'), function(i, e){
                formData.append('id_kab_agunan[]', e.value);
            });
            $.each($('select[name="id_kec_agunan[]"]'), function(i, e){
                formData.append('id_kec_agunan[]', e.value);
            });
            $.each($('select[name="id_kel_agunan[]"]'), function(i, e){
                formData.append('id_kel_agunan[]', e.value);
            });
            $.each($('input[name="rt_agunan[]"]'), function(i, e){
                formData.append('rt_agunan[]', e.value);
            });
            $.each($('input[name="rw_agunan[]"]'), function(i, e){
                formData.append('rw_agunan[]', e.value);
            });
            $.each($('input[name="luas_tanah[]"]'), function(i, e){
                formData.append('luas_tanah[]', e.value);
            });
            $.each($('input[name="luas_bangunan[]"]'), function(i, e){
                formData.append('luas_bangunan[]', e.value);
            });
            $.each($('input[name="nama_pemilik_sertifikat[]"]'), function(i, e){
                formData.append('nama_pemilik_sertifikat[]', e.value);
            });
            $.each($('select[name="jenis_sertifikat[]"]'), function(i, e){
                formData.append('jenis_sertifikat[]', e.value);
            });
            $.each($('input[name="no_sertifikat[]"]'), function(i, e){
                formData.append('no_sertifikat[]', e.value);
            });
            $.each($('input[name="no_ukur_sertifikat[]"]'), function(i, e){
                formData.append('tgl_ukur_sertifikat[]', e.value);
            });
            $.each($('input[name="tgl_berlaku_shgb[]"]'), function(i, e){
                formData.append('tgl_berlaku_shgb[]', e.value);
            });
            $.each($('input[name="no_imb[]"]'), function(i, e){
                formData.append('no_imb[]', e.value);
            });
            $.each($('input[name="njop[]"]'), function(i, e){
                formData.append('njop[]', e.value);
            });
            $.each($('input[name="nop[]"]'), function(i, e){
                formData.append('nop[]', e.value);
            });

            $.each($('input[name="agunan_bag_depan[]"]', this), function(i, e){
                formData.append('agunan_bag_depan[]', e.files[0]);
            });

            $.each($('input[name="agunan_bag_jalan[]"]', this), function(i, e){
                formData.append('agunan_bag_jalan[]', e.files[0]);
            });

            $.each($('input[name="agunan_bag_ruangtamu[]"]', this), function(i, e){
                formData.append('agunan_bag_ruangtamu[]', e.files[0]);
            });

            $.each($('input[name="agunan_bag_kamarmandi[]"]', this), function(i, e){
                formData.append('agunan_bag_kamarmandi[]', e.files[0]);
            });

            $.each($('input[name="agunan_bag_dapur[]"]', this), function(i, e){
                formData.append('agunan_bag_dapur[]', e.files[0]);
            });

            //Agunan Kendaraan
            $.each($('input[name="no_bpkb_ken[]"]'), function(i, e){
                formData.append('no_bpkb_ken[]', e.value);
            });
            $.each($('input[name="nama_pemilik_ken[]"]'), function(i, e){
                formData.append('nama_pemilik_ken[]', e.value);
            });
            $.each($('input[name="alamat_pemilik_ken[]"]'), function(i, e){
                formData.append('alamat_pemilik_ken[]', e.value);
            });
            $.each($('input[name="merk_ken[]"]'), function(i, e){
                formData.append('merk_ken[]', e.value);
            });
            $.each($('input[name="jenis_ken[]"]'), function(i, e){
                formData.append('jenis_ken[]', e.value);
            });
            $.each($('input[name="no_rangka_ken[]"]'), function(i, e){
                formData.append('no_rangka_ken[]', e.value);
            });
            $.each($('input[name="no_mesin_ken[]"]'), function(i, e){
                formData.append('no_mesin_ken[]', e.value);
            });
            $.each($('input[name="warna_ken[]"]'), function(i, e){
                formData.append('warna_ken[]', e.value);
            });
            $.each($('input[name="tahun_ken[]"]'), function(i, e){
                formData.append('tahun_ken[]', e.value);
            });
            $.each($('input[name="no_polisi_ken[]"]'), function(i, e){
                formData.append('no_polisi_ken[]', e.value);
            });
            $.each($('input[name="no_stnk_ken[]"]'), function(i, e){
                formData.append('no_stnk_ken[]', e.value);
            });
            $.each($('input[name="tgl_exp_pajak_ken[]"]'), function(i, e){
                formData.append('tgl_exp_pajak_ken[]', e.value);
            });
            $.each($('input[name="tgl_exp_stnk_ken[]"]'), function(i, e){
                formData.append('tgl_exp_stnk_ken[]', e.value);
            });
            $.each($('input[name="no_faktur_ken[]"]'), function(i, e){
                formData.append('no_faktur_ken[]', e.value);
            });
            // $.each($('input[name="lamp_agunan_depan_ken[]"]', this), function(i, e){
            //     formData.append('lamp_agunan_depan_ken[]', e.files[0]);
            // });
            // $.each($('input[name="lamp_agunan_kanan_ken[]"]', this), function(i, e){
            //     formData.append('lamp_agunan_kanan_ken[]', e.files[0]);
            // });
            // $.each($('input[name="lamp_agunan_kiri_ken[]"]', this), function(i, e){
            //     formData.append('lamp_agunan_kiri_ken[]', e.files[0]);
            // });
            // $.each($('input[name="lamp_agunan_belakang_ken[]"]', this), function(i, e){
            //     formData.append('lamp_agunan_belakang_ken[]', e.files[0]);
            // });
            // $.each($('input[name="lamp_agunan_dalam_ken[]"]', this), function(i, e){
            //     formData.append('lamp_agunan_dalam_ken[]', e.files[0]);
            // });

            // //Pemeriksa Tanah & Bangunan
            $.each($('input[name="nama_penghuni_agunan[]"]', this), function(i, e){
                formData.append('nama_penghuni_agunan[]', e.value);
            });
            $.each($('select[name="status_penghuni_agunan[]"]', this), function(i, e){
                formData.append('status_penghuni_agunan[]', e.value);
            });
            $.each($('input[name="bentuk_bangunan_agunan[]"]', this), function(i, e){
                formData.append('bentuk_bangunan_agunan[]', e.value);
            });
            $.each($('select[name="kondisi_bangunan_agunan[]"]', this), function(i, e){
                formData.append('kondisi_bangunan_agunan[]', e.value);
            });
            $.each($('input[name="fasilitas_agunan[]"]', this), function(i, e){
                formData.append('fasilitas_agunan[]', e.value);
            });
            $.each($('input[name="listrik_agunan[]"]', this), function(i, e){
                formData.append('listrik_agunan[]', e.value);
            });
            $.each($('input[name="nilai_taksasi_agunan[]"]', this), function(i, e){
                formData.append('nilai_taksasi_agunan[]', e.value);
            });
            $.each($('input[name="nilai_taksasi_bangunan[]"]', this), function(i, e){
                formData.append('nilai_taksasi_bangunan[]', e.value);
            });
            $.each($('input[name="tgl_taksasi_agunan[]"]', this), function(i, e){
                formData.append('tgl_taksasi_agunan[]', e.value);
            });
            $.each($('input[name="nilai_likuidasi_agunan[]"]', this), function(i, e){
                formData.append('nilai_likuidasi_agunan[]', e.value);
            });
            $.each($('input[name="nilai_agunan_independen[]"]', this), function(i, e){
                formData.append('nilai_agunan_independen[]', e.value);
            });
            $.each($('input[name="perusahaan_penilai_independen[]"]', this), function(i, e){
                formData.append('perusahaan_penilai_independen[]', e.value);
            });

            //Pemeriksaan Kendaraan
            $.each($('input[name="nama_pengguna_ken[]"]', this), function(i, e){
                formData.append('nama_pengguna_ken[]', e.value);
            });
            $.each($('input[name="status_pengguna_ken[]"]', this), function(i, e){
                formData.append('status_pengguna_ken[]', e.value);
            });
            $.each($('input[name="jml_roda_ken[]"]', this), function(i, e){
                formData.append('jml_roda_ken[]', e.value);
            });
            $.each($('input[name="kondisi_ken[]"]', this), function(i, e){
                formData.append('kondisi_ken[]', e.value);
            });
            $.each($('input[name="keberadaan_ken[]"]', this), function(i, e){
                formData.append('keberadaan_ken[]', e.value);
            });
            $.each($('input[name="body_ken[]"]', this), function(i, e){
                formData.append('body_ken[]', e.value);
            });
            $.each($('input[name="interior_ken[]"]', this), function(i, e){
                formData.append('interior_ken[]', e.value);
            });
            $.each($('input[name="km_ken[]"]', this), function(i, e){
                formData.append('km_ken[]', e.value);
            });
            $.each($('input[name="modifikasi_ken[]"]', this), function(i, e){
                formData.append('modifikasi_ken[]', e.value);
            });
            $.each($('input[name="aksesoris_ken[]"]', this), function(i, e){
                formData.append('aksesoris_ken[]', e.value);
            });

            //Kapasitas Bulanan
            var pemasukan_debitur = $('input[name=pemasukan_debitur]',this).val();
            pemasukan_debitur = pemasukan_debitur.replace(/[^\d]/g,"");
            formData.append('pemasukan_debitur',pemasukan_debitur); 

            var pemasukan_pasangan = $('input[name=pemasukan_pasangan]',this).val();
            pemasukan_pasangan = pemasukan_pasangan.replace(/[^\d]/g,"");
            formData.append('pemasukan_pasangan',pemasukan_pasangan);

            var pemasukan_penjamin = $('input[name=pemasukan_penjamin]',this).val();
            pemasukan_penjamin = pemasukan_penjamin.replace(/[^\d]/g,"");
            formData.append('pemasukan_penjamin',pemasukan_penjamin); 

            var biaya_rumah_tangga = $('input[name=biaya_rumah_tangga]',this).val();
            biaya_rumah_tangga = biaya_rumah_tangga.replace(/[^\d]/g,"");
            formData.append('biaya_rumah_tangga',biaya_rumah_tangga); 

            var biaya_transportasi = $('input[name=biaya_transportasi]',this).val();
            biaya_transportasi = biaya_transportasi.replace(/[^\d]/g,"");
            formData.append('biaya_transport',biaya_transportasi); 

            var biaya_pendidikan = $('input[name=biaya_pendidikan]',this).val();
            biaya_pendidikan = biaya_pendidikan.replace(/[^\d]/g,"");
            formData.append('biaya_pendidikan',biaya_pendidikan); 

            var biaya_telp_listr_air = $('input[name=biaya_telp_listr_air]',this).val();
            biaya_telp_listr_air = biaya_telp_listr_air.replace(/[^\d]/g,"");
            formData.append('biaya_telp_listr_air',biaya_telp_listr_air); 

            var biaya_lain = $('input[name=biaya_lain]',this).val();
            biaya_lain = biaya_lain.replace(/[^\d]/g,"");
            formData.append('biaya_lain',biaya_lain); 

            // //Pendapatan Usaha
            var pemasukan_tunai = $('input[name=pemasukan_tunai]',this).val();
            pemasukan_tunai = pemasukan_tunai.replace(/[^\d]/g,"");
            formData.append('pemasukan_tunai',pemasukan_tunai); 

            var pemasukan_kredit = $('input[name=pemasukan_kredit]',this).val();
            pemasukan_kredit = pemasukan_kredit.replace(/[^\d]/g,"");
            formData.append('pemasukan_kredit',pemasukan_kredit);
            // pengeluaran sewa 

            var biaya_sewa = $('input[name=biaya_sewa]',this).val();
            biaya_sewa = biaya_sewa.replace(/[^\d]/g,"");
            formData.append('biaya_sewa',biaya_sewa);

            var biaya_gaji_pegawai = $('input[name=biaya_gaji_pegawai]',this).val();
            biaya_gaji_pegawai = biaya_gaji_pegawai.replace(/[^\d]/g,"");
            formData.append('biaya_gaji_pegawai',biaya_gaji_pegawai);

            var biaya_belanja_brg = $('input[name=biaya_belanja_brg]',this).val();
            biaya_belanja_brg = biaya_belanja_brg.replace(/[^\d]/g,"");
            formData.append('biaya_belanja_brg',biaya_belanja_brg); 

            var biaya_telp_listr_air_usaha = $('input[name=biaya_telp_listr_air_usaha]',this).val();
            biaya_telp_listr_air_usaha = biaya_telp_listr_air_usaha.replace(/[^\d]/g,"");
            formData.append('biaya_telp_listr_air_us',biaya_telp_listr_air_usaha); 

            var biaya_sampah_keamanan = $('input[name=biaya_sampah_keamanan]',this).val();
            biaya_sampah_keamanan = biaya_sampah_keamanan.replace(/[^\d]/g,"");
            formData.append('biaya_sampah_keamanan',biaya_sampah_keamanan);

            var biaya_kirim_barang = $('input[name=biaya_kirim_barang]',this).val();
            biaya_kirim_barang = biaya_kirim_barang.replace(/[^\d]/g,"");
            formData.append('biaya_kirim_barang',biaya_kirim_barang);

            var biaya_hutang_dagang = $('input[name=biaya_hutang_dagang]',this).val();
            biaya_hutang_dagang = biaya_hutang_dagang.replace(/[^\d]/g,"");
            formData.append('biaya_hutang_dagang',biaya_hutang_dagang);

            var biaya_angsuran = $('input[name=biaya_angsuran]',this).val();
            biaya_angsuran = biaya_angsuran.replace(/[^\d]/g,"");
            formData.append('biaya_angsuran',biaya_angsuran); 

            var biaya_lain_lain = $('input[name=biaya_lain_lain]',this).val();
            biaya_lain_lain = biaya_lain_lain.replace(/[^\d]/g,"");
            formData.append('biaya_lain_lain',biaya_lain_lain); 

            //Recom AO
            formData.append('produk',$('select[name=produk]',this).val());

            var plafon_kredit = $('input[name=plafon_kredit]',this).val();
            plafon_kredit = plafon_kredit.replace(/[^\d]/g,""); 
            formData.append('plafon_kredit',plafon_kredit); 

            formData.append('jangka_waktu',$('select[name=jangka_waktu]',this).val()); 
            formData.append('suku_bunga',$('input[name=suku_bunga]',this).val()); 
            var pembayaran_bunga = $('input[name=pembayaran_bunga]',this).val();
            pembayaran_bunga = pembayaran_bunga.replace(/[^\d]/g,""); 
            formData.append('pembayaran_bunga',pembayaran_bunga); 
            formData.append('akad_kredit',$('select[name=akad_kredit]',this).val()); 
            formData.append('ikatan_agunan',$('select[name=ikatan_agunan]',this).val()); 
            formData.append('analisa_ao',$('input[name=analisa_ao]',this).val()); 
            var biaya_provisi = $('input[name=biaya_provisi]',this).val();
            biaya_provisi = biaya_provisi.replace(/[^\d]/g,""); 
            formData.append('biaya_provisi',biaya_provisi); 
            var biaya_administrasi = $('input[name=biaya_administrasi]',this).val();
            biaya_administrasi = biaya_administrasi.replace(/[^\d]/g,""); 
            formData.append('biaya_administrasi',biaya_administrasi); 
            var biaya_credit_checking = $('input[name=biaya_credit_checking]',this).val();
            biaya_credit_checking = biaya_credit_checking.replace(/[^\d]/g,""); 
            formData.append('biaya_credit_checking',biaya_credit_checking); 
            var biaya_tabungan = $('input[name=biaya_tabungan]',this).val();
            biaya_tabungan = biaya_tabungan.replace(/[^\d]/g,""); 
            formData.append('biaya_tabungan',biaya_tabungan); 
            formData.append('catatan_ao',$('textarea[name=catatan_ao]',this).val()); 
            formData.append('status_ao',$('input[type=radio][name=status_ao]:checked',this).val()); 

            //lampiran

            formData.append('lamp_skk',$('input[name=lamp_skk]',this)[0].files[0]);


            formData.append('form_persetujuan_ideb',$('input[name=form_persetujuan_ideb]',this)[0].files[0]);

            $.each($('input[name="lamp_buku_tabungan[]"]', this), function(i, e){
                formData.append('lamp_buku_tabungan[]', e.files[0]);
            });

            $.each($('input[name="foto_pembukuan_usaha[]"]', this), function(i, e){
                formData.append('foto_pembukuan_usaha[]', e.files[0]);
            });

            $.each($('input[name="lamp_sku[]"]', this), function(i, e){
                formData.append('lamp_sku[]', e.files[0]);
            });

            $.each($('input[name="lamp_foto_usaha[]"]', this), function(i, e){
                formData.append('lamp_foto_usaha[]', e.files[0]);
            });
            formData.append('lamp_slip_gaji',$('input[name=lamp_slip_gaji]',this)[0].files[0]);
            }
            
            update_ao(formData, id)
            .done(function(res){
                console.log(update_ao);
                var data = res.data;
                    bootbox.alert('Data berhasil disimpan',function(){
                    $("#batal").click();
                    load_data();
                    $('#form_input_ao')[0].reset();
                    hide_all();
                    $('#lihat_data_credit').show();
                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON.message;
                var error = "";
                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                }else{
                    $.each(data.lamp_slip_gaji, function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                    $.each(data['agunan_bag_dapur.0'], function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                    $.each(data['agunan_bag_depan.0'], function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                    $.each(data['agunan_bag_jalan.0'], function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                    $.each(data['agunan_bag_kamarmandi.0'], function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                    $.each(data['agunan_bag_ruangtamu.0'], function(index,item){
                        error+='<p>'+ item +"</p>";
                    });
                }
                bootbox.alert(error);
                $("#batal").click();
            });
        });  
});

function click_detail() {
    $('#form_detail .form-control').prop('disabled', true);
    $('.submit').hide(); 
    $('#status_ao').hide();
    $('.ao').show();
    $('.submit').hide();
}


        // Click ubah
        $('#data_penjamin').on('click', '.edit', function(e){
            e.preventDefault();

            var id_penjamin = $(this).attr('data');
            var html1       = [];
            var html2       = [];
            var html3       = [];
            var html4       = [];

            get_data_penjamin({}, id_penjamin)
            .done(function(response){
                var data = response.data;
                
                $('#form_edit_penjamin input[type=hidden][name=edit_id_penjamin]').val(data.id);
                $('#form_edit_penjamin input[name=nama_pen]').val(data.nama_ktp);
                $('#form_edit_penjamin input[name=nama_ibu_kandung_pen]').val(data.nama_ibu_kandung);
                $('#form_edit_penjamin input[name=no_ktp_pen]').val(data.no_ktp);
                $('#form_edit_penjamin input[name=no_npwp_pen]').val(data.no_npwp);
                $('#form_edit_penjamin input[name=tempat_lahir_pen]').val(data.tempat_lahir);
                $('#form_edit_penjamin input[name=tgl_lahir_pen]').val(data.tgl_lahir);

                $('#form_edit_ktp_penjamin input[type=hidden][name=id_ktp_pen]').val(data.id);
                $('#form_edit_kk_penjamin input[type=hidden][name=id_kk_pen]').val(data.id);
                $('#form_edit_ktp_pas_penjamin input[type=hidden][name=id_ktp_pasangan_pen]').val(data.id);
                $('#form_edit_buku_nikah_penjamin input[type=hidden][name=id_buku_nikah_pen]').val(data.id);


                var select_jenis_kel_pen = [];
                    var option_jenis_kel_pen = [
                    '<option id="L_pen" value="L">LAKI-LAKI</option>',
                    '<option id="P_pen" value="P">PEREMPUAN</option>',
                    ].join('\n');
                    select_jenis_kel_pen.push(option_jenis_kel_pen);
                $('#form_edit_penjamin select[id=select_jenis_kel_pen]').html(select_jenis_kel_pen);


                if (data.jenis_kelamin == "L") {
                 document.getElementById("L_pen").selected = "true";
                }else {
                document.getElementById("P_pen").selected = "true";  
                }
                $('#form_edit_penjamin textarea[name=alamat_ktp_pen]').val(data.alamat_ktp);
                $('#form_edit_penjamin input[name=notelp_pen]').val(data.no_telp);

                // var a = [
                // '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_ktp+'" data-lightbox="example-set" data-title="Lampiran KTP Penjamin"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_ktp+'" /> </a>'
                // ].join('\n');
                // html.push(a);
                // $('#gambar_ktp_penjamin').html(html1)
                // console.log(data.lampiran.lamp_ktp);

                // var b = [
                // '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_ktp_pasangan+'" data-lightbox="example-set" data-title="Lampiran KTP Pasangan Penjamin"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_ktp_pasangan+'" /> </a>'
                // ].join('\n');
                // html2.push(b);
                // $('#gambar_ktp_pas_penjamin').html(html2)

                // var c = [
                // '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_kk+'" data-lightbox="example-set" data-title="Lampiran KK Penjamin"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_kk+'" /> </a>'
                // ].join('\n');
                // html3.push(c);
                // $('#gambar_kk_penjamin').html(html3)

                // var d = [
                // '<a class="example-image-link" target="window.open()" href="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_buku_nikah+'" data-lightbox="example-set" data-title="Lampiran Buku Nikah Penjamin"><img class="thumbnail img-responsive img" alt="" src="<?php echo $this->config->item('img_url') ?>'+data.lampiran.lamp_buku_nikah+'" /> </a>'
                // ].join('\n');
                // html4.push(d);
                // $('#gambar_bukunikah_penjamin').html(html4)

            })
            .fail(function(jqXHR){
                bootbox.alert('Data tidak ditemukan');
            });
            // hide_all();
            // $('#lihat_ubah_asaldata').show();
        });

        //SUBMIT EDIT KTP PENJAMIN
        $('#form_edit_ktp_penjamin').on('submit',function(e){
            var id = $('input[name=id_ktp_pen]', this).val();
            e.preventDefault();
            var formData = new FormData();

            formData.append('lamp_ktp_pen',$('input[name=lamp_ktp_pen]',this)[0].files[0]);

            update_penjamin(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                    load_data();
                    load_penjamin();
                    // $(".close").click();

                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                });
            });
        }); 


        //SUBMIT EDIT KK PENJAMIN
        $('#form_edit_kk_penjamin').on('submit',function(e){
            var id = $('input[name=id_kk_pen]', this).val();
            e.preventDefault();
            var formData = new FormData();

            formData.append('lamp_kk_pen',$('input[name=lamp_kk_pen]',this)[0].files[0]);

            update_penjamin(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                    load_data();
                    load_penjamin();
                    // $(".close").click();

                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                });
            });
        }); 

        //SUBMIT EDIT KTP PASANGAN PENJAMIN
        $('#form_edit_ktp_pas_penjamin').on('submit',function(e){
            var id = $('input[name=id_ktp_pasangan_pen]', this).val();
            e.preventDefault();
            var formData = new FormData();

            formData.append('lamp_ktp_pasangan_pen',$('input[name=lamp_ktp_pasangan_pen]',this)[0].files[0]);

            update_penjamin(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                    load_data();
                    load_penjamin();
                    $(".close_deb").click();
                    // $(".close").click();

                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                });
            });
        }); 

        //SUBMIT EDIT BUKU NIKAH PENJAMIN
        $('#form_edit_buku_nikah_penjamin').on('submit',function(e){
            var id = $('input[name=id_buku_nikah_pen]', this).val();
            e.preventDefault();
            var formData = new FormData();

            formData.append('lamp_buku_nikah_pen',$('input[name=lamp_buku_nikah_pen]',this)[0].files[0]);

            update_penjamin(formData, id)
            .done(function(res){
                var data = res.data;
                    bootbox.alert('Data berhasil diubah',function(){
                    $("#batal").click();
                    $(".close_deb").click();
                    load_data();
                    load_penjamin();
                    // $(".close").click();

                });
            })
            .fail(function(jqXHR){
                var data = jqXHR.responseJSON;
                var error = "";

                if(typeof data == 'string') {
                    error = '<p>'+ data +'</p>';
                } else {
                    $.each(data, function(index, item){
                        error += '<p>'+ item +'</p>'+"\n";
                    });
                }
                bootbox.alert('Data gagal diubah, Silahkan coba lagi dan cek jaringan anda !!',function(){
                    $("#batal").click();
                });
            });
        });
</script>
