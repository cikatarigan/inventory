@extends('layouts.app')
@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Profile</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Profile</li>
         </ol>
        </div>
      </div>
    </div>
  </section>
<section class="content">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-success">
                    <div class="box-body box-profile">
                    <div class="text-center mb-2">
                        @if($user->image == null)
                        <img class="profile-user-img img-fluid rounded-circle" src="{{asset('images/user2-160x160.jpg')}}" alt="User profile picture">
                        @else
                        <img class="profile-user-img img-fluid rounded-circle" src="{{asset('storage/'.$user->image)}}" alt="User profile picture">
                        @endif
                    </div>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Nama</b>
                            <p class="pull-right">{{ $user->name }}</p>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b>
                            <p class="pull-right">{{ $user->email }}</p>
                        </li>
                    </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card mt-3">
                    <div class="card-header">
                    <ul class="nav  nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Pengaturan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Ubah Kata Sandi</a>
                        </li>

                    </ul>
                    </div>
                    <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="active tab-pane" id="settings">
                                <form class="form-horizontal login-form" method="post" action="" id="formSetting" enctype="multipart/form-data" >
                                <input type="hidden" id="id_setting" name="id" value="{{ $user->id }}">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Foto</label>
                                    <div class="col-sm-10">
                                        <img src="//placehold.it/100" id="preview"   class="avatar img-circle" alt="avatar" style="width:100px;">
                                        <h6>Upload a different photo...</h6>
                                        <input type="file" class="form-control" name="image" id="image">
                                        <span id="image_profile" class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Nama</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama">
                                        <span id="error_name" class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-block btn-success" id="btn-update">Update</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form class="form-horizontal mt-3 login-form" method="post" action="" id="formPassword" >
                                <input type="hidden" id="id_password" name="id" value="{{ $user->id }}">
                                <div class="form-group">
                                <label class="col-sm-3 control-label">Kata Sandi Lama</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Kata Sandi Lama">
                                    <span id="error_current_password" class="help-block"></span>
                                </div>
                                </div>
                                <div class="form-group">
                                <label class="col-sm-3 control-label">Kata Sandi Baru</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Kata Sandi Baru">
                                    <span id="error_new_password" class="help-block"></span>
                                </div>
                                </div>
                                <div class="form-group">
                                <label class="col-sm-3 control-label">Ulangi Kata Sandi</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" placeholder="Ulangi Kata Sandi">
                                    <span id="error_new_password_confirm" class="help-block"></span>
                                </div>
                                </div>
                                <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-block btn-success">Simpan</button>
                                </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
   jQuery(document).ready(function ($) {
     $('#formSetting').submit(function (event) {
       event.preventDefault();
       var _data = new FormData($('#formSetting')[0]);
             // disable button
            $('#btn-update').prop("disabled", true);
            // add spinner to button
            $('#btn-update').html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
            );
       $.ajax({
         url: '/profile/setting',
         type: 'POST',
         data: _data,
         cache : false,
         contentType : false,
         processData : false,

         success: function (data) {
           if (data.success) {
             $('#formSetting button[type=submit]').button('reset');
             $('#formSetting')[0].reset();
             toastr.success('Data Telah diSimpan');
             $('#btn-update').prop("disabled", false);
            $('.spinner-border').hide();
             setTimeout(function () {
               location.reload();
             }, 1000);
           }
         },

         error: function (data) {
           var error = data.responseJSON;
           var _data = new FormData($('#formSetting')[0]);
           $.each(data, function (key, value) {
             if (error.errors[data[key].name] != undefined) {
               $('#error_' + data[key].name).text(error.errors[data[key].name]);
               $('#error_' + data[key].name).show();
               $('#' + data[key].name).parent().parent().addClass('has-error');
               $('#formSetting button[type=submit]').button('reset');
             }
           });
         }
       });
     });

     $(document).on('change', '#image', function(event){
         let imgs = $('input[name="image"]').get(0).files;
         console.log(imgs[0]);
         let reader = new FileReader();
         reader.readAsDataURL(imgs[0]);
         let x = URL.createObjectURL(imgs[0]);

          // if(extension[0] == 'image'){
          //   reader.readAsDataURL(imgs);
          //   x = URL.createObjectURL(imgs);
          // }

          $('#preview').attr('src', x);
     });


     $('#formPassword').submit(function (event) {
       event.preventDefault();
       $('#formPassword div.form-group').removeClass('has-error');
       $('#formPassword .help-block').empty();
       $('#formPassword div.form-group').removeClass('has-error');
       $('#formPassword .help-block').empty();
       var _data = $("#formPassword").serialize();
       $.ajax({
         url: '/profile/password',
         type: 'POST',
         data: _data,
         cache: false,

         success: function (data) {
           if (data.success) {
             $('#formPassword button[type=submit]').button('reset');
             $('#formPassword')[0].reset();
             toastr.success('password telah diganti');
             setTimeout(function () {
               location.reload();
             }, 1000);
           } else {
             $('#error_current_password').text(data.message);
             $('#error_current_password').show();
             $('#current_password').parent().parent().addClass('has-error');
           }
         },

         error: function (data) {
           var error = data.responseJSON;
           var data = $('#formPassword').serializeArray();
           $.each(data, function (key, value) {
             if (error.errors[data[key].name] != undefined) {
               $('#error_' + data[key].name).text(error.errors[data[key].name]);
               $('#error_' + data[key].name).show();
               $('#' + data[key].name).parent().parent().addClass('has-error');
               $('#formPassword button[type=submit]').button('reset');
             }
           });
         }
       });
     });
   });
</script>
@endsection
