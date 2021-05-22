@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
   <div class="card">
      <div class="card-header">
        @if(Auth::user()->hasPermissionTo('user.store','web'))
         <button id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
         @endif
      </div>
      <div class="card-body">
        <div class="table-responsive">
         <table id="users-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalUser">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormUser">
            <div class="modal-header">
               <h5 class="modal-title"></h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="box box-info">
                  <div class="box-header">
                     <div class="box-body">

                        <div class="form-group">
                          <div class="form-group">
                            <label for="InputEmail1">Email address</label>
                              <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required maxlength="30">
                           </div>
                           <div class="form-group">
                            <label for="Inputname">Name</label>
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama" required maxlength="30">
                           </div>
                           <div class="form-group">
                            <label for="Inputusername">Username</label>
                              <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required maxlength="30">
                           </div>
                           <div class="form-group" id="password_input">
                            <label for="Inputusername">Password</label>
                              <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                           </div>
                            <div class="form-group">
                            <label for="Inputusername">Phone</label>
                              <input type="number" class="form-control" id="phone" name="phone" placeholder="Masukkan No Telepon" required maxlength="30">
                           </div>
                              <div class="form-group">
                                <label for="Inputusername">Roles</label>
                                  <select name="roles" id="roles" class="form-control">
                                     <option value="" disabled selected>Pilih roles</option>
                                       @foreach($roles as $item)
                                            <option value="{{$item}}">{{$item}}</option>
                                       @endforeach
                                  </select>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

{{-- Modal Delete --}}
<div class="modal" tabindex="-1" role="dialog" id="deleteUserModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormDeleteUser">
        <input type="hidden" id="id_delete" name="id" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="del-success"></p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ya</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal restore --}}
<div class="modal" tabindex="-1" role="dialog" id="restoreUserModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormRestoreUser">
        <input type="hidden" id="id_restore" name="id" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="del-success"></p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ya</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Change Password --}}
<div class="modal" tabindex="-1" role="dialog" id="ChangePasswordUserModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormChangePassword">
        <input type="hidden" id="id_change" name="id" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
             <div class="form-group">
            <label for="Inputname">password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Masukkan Password" required maxlength="30">
           </div>
          </div>
            <div class="form-group">
             <div class="form-group">
            <label for="Inputname">Konfirmasi Password</label>
              <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" placeholder="Masukkan Konfirmasi Password" required maxlength="30">
           </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ya</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

@endsection
@section('script')
<script>
jQuery(document).ready(function ($) {

  var table = $('#users-table').DataTable({
    "bFilter": true,
    "processing": true,
    "serverSide": true,
    "lengthChange": true,
    "responsive": true,
    "ajax": {
      "url": "/user",
      "type": "POST",
    },
    "language": {
      "emptyTable": "Tidak ada data yang tersedia",
    },
    "columns": [{
        title: "username",
        "data": "name",
        "orderable": true,
      },
      {
        title: "Email",
        "data": "email",
        "orderable": true,
      },
      {
        title: "Role",
        "data": "roles",
        render: function (data) {
          if (data.length > 0) {
            return data[0].name;
          } else {
            return '';
          }
        },
        "orderable": false,
      },
      {
        title: "Status",
        "data": "status",
        render: function (data) {
          if (data == 'active') {
            return '<span class="right badge badge-success">Active</span>';
          } else {
            return '<span class="right badge badge-danger">Banned</span>';
          }
        },
        "orderable": true,
      },
      {
        title: "Action",
        render: function (data, type, row) {
          if (row.status == 'active') {
            return '@if(Auth::user()->hasPermissionTo('user.update','web'))<a href="#" class="btn-edit badge badge-secondary" data-name="' + row.name + '" data-id="' + row.id + '"><i class="far fa-edit fa-lg"></i></a> @endif '+'@if(Auth::user()->hasPermissionTo('user.destroy','web'))<a href="#" class="btn-delete badge badge-danger" data-name="' + row.name + '" data-id="' + row.id + '"><i class="fa fa-ban fa-lg"></i></a> @endif ' + '@if(Auth::user()->hasPermissionTo('user.change','web'))<a href="#" class="btn-change badge badge-warning" data-name="' + row.name + '" data-id="' + row.id + '"><i class="fas fa-key fa-lg"></i></a> @endif ';
          } else {
            return '@if(Auth::user()->hasPermissionTo('user.restore','web'))<a href="#" class="btn-restore badge badge-success" data-name="' + row.name + '" data-id="' + row.id + '"><i class="fa fa-check fa-lg"></i></a>@endif';
          }
        },
        "orderable": false,
      }
    ],
    "order": [1, 'asc'],
    "fnCreatedRow": function (nRow, aData, iDataIndex) {
      $(nRow).attr('data', JSON.stringify(aData));
    }
  });


  var url;

  // Add
  $('#btnAdd').click(function () {
    $('#FormUser')[0].reset();
    $('#FormUser button[type=submit]').button('reset');
    $('#FormUser .modal-title').text("Add Pool");
    $('#FormUser div.form-group').removeClass('has-error');
    $('#FormUser .help-block').empty();

    $('#FormUser input[name="_method"]').remove();
    url = '{{ route("user.store") }}';

    var data = $('#FormUser').serializeArray();
    $.each(data, function (key, value) {
      $("#FormUser input[name='" + data[key].name + "']").parent().find('.help-block').hide();
    });

    $('#ModalUser').modal('show');
  });

  // Edit
  $('#users-table').on('click', '.btn-edit', function (e) {
    $('#FormUser .modal-title').text("Edit User");
    $('#FormUser .help-block').empty();
    $('#FormUser')[0].reset();

    var aData = JSON.parse($(this).parent().parent().attr('data'));
    console.log(aData);

    $('#FormUser .modal-body .form-horizontal').append('<input type="hidden" name="_method" value="POST">');

    url = '{{ route("user.index") }}' + '/update/' + aData.id;

    var data = $('#FormUser').serializeArray();

    $.each(data, function (key, value) {
      $("#FormUser input[name='" + data[key].name + "']").parent().find('.help-block').hide();
    });

    $('#id').val(aData.id);
    $('#username').val(aData.username).prop('disabled', true);
    $('#name').val(aData.name).prop('disabled', true);
    $('#email').val(aData.email).prop('disabled', true);
    $('#roles').val(aData.roles.names);
    $('#phone').val(aData.phone);
    $("#password_input").remove();
    $('#ModalUser').modal('show');
  });

  $('#FormUser').submit(function (event) {
    event.preventDefault();
    var $this = $(this);
    var form = $('#FormUser');
    var data = form.serialize();
    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      cache: false,
      success: function (data) {
        console.log(data)
        if (data.success) {
          toastr.success(data.message);
          $('#ModalUser').modal('hide');
          table.draw();
        } else {
          toastr.error(data.message);
        }
      },
      error: function (response) {
        if (response.status === 422) {
          let errors = response.responseJSON.errors;
          $.each(errors, function (key, error) {
            var item = form.find('input[name=' + key + ']');
            item = (item.length > 0) ? item : form.find('select[name=' + key + ']');
            item = (item.length > 0) ? item : form.find('textarea[name=' + key + ']');
            item = (item.length > 0) ? item : form.find("input[name='" + key + "[]']");

            var parent = (item.parent().hasClass('form-group')) ? item.parent() : item.parent().parent();
            parent.addClass('has-error');
            parent.append('<span class="help-block" style="color:red;">' + error + '</span>');
          })
        }
      }
    })
  });

  // Delete
  $('#users-table').on('click', '.btn-delete', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#FormDeleteUser #id_delete').val(id);
    $('#deleteUserModal').modal('show');
    $('#FormDeleteUser .modal-title').text("Konfirmasi Hapus");
    $('#FormDeleteUser #del-success').html("Apakah Anda yakin ingin menghapus user <b>" + name + "</b> ini ?");
  });

  $('#FormDeleteUser').submit(function (event) {
    event.preventDefault();
    var form = $('#FormDeleteUser');
    var data = form.serialize();
    $.ajax({
      url: '/user/delete',
      type: 'POST',
      data: data,
      cache: false,
      success: function (data) {
        if (data.success) {
          toastr.success(data.message);
          $('#deleteUserModal').modal('hide');
          table.draw();
        }
      },
    })
  });

  //Restore
  $('#users-table').on('click', '.btn-restore', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#FormRestoreUser #id_restore').val(id);
    $('#restoreUserModal').modal('show');
    $('#FormRestoreUser .modal-title').text("Konfirmasi Restore");
    $('#FormRestoreUser #del-success').html("Apakah Anda yakin ingin merestore user <b>" + name + "</b> ini ?");
  });

  $('#FormRestoreUser').submit(function (event) {
    event.preventDefault();
    var form = $('#FormRestoreUser');
    var data = form.serialize();
    $.ajax({
      url: '/user/restore',
      type: 'POST',
      data: data,
      cache: false,
      success: function (data) {
        if (data.success) {
          toastr.success(data.message);
          $('#restoreUserModal').modal('hide');
          table.draw();
        }
      },
    })
  });


  //Change Password
  $('#users-table').on('click', '.btn-change', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#FormChangePassword #id_change').val(id);
    $('#ChangePasswordUserModal').modal('show');
    $('#FormChangePassword .modal-title').text("Konfirmasi Change Password");
  });

  $('#FormChangePassword').submit(function (event) {
    event.preventDefault();
    $('#FormChangePassword .help-block').empty();
    var form = $('#FormChangePassword');
    var data = form.serialize();
    $.ajax({
      url: '/user/change-password',
      type: 'POST',
      data: data,
      cache: false,
      success: function (data) {
        if (data.success) {
          toastr.success('password Telah Di ganti Telah diSimpan');
          $('#ChangePasswordUserModal').modal('hide');
        } else {
          $('#error_current_password').text(data.message);
          $('#error_current_password').show();
          $('#current_password').parent().parent().addClass('has-error');
        }
      },
      error: function (response) {
        if (response.status === 422) {
          let errors = response.responseJSON.errors;
          $.each(errors, function (key, error) {
            var item = form.find('input[name=' + key + ']');
            item = (item.length > 0) ? item : form.find('select[name=' + key + ']');
            item = (item.length > 0) ? item : form.find('textarea[name=' + key + ']');
            item = (item.length > 0) ? item : form.find("input[name='" + key + "[]']");

            var parent = (item.parent().hasClass('form-group')) ? item.parent() : item.parent().parent();
            parent.addClass('has-error');
            parent.append('<span class="help-block" style="color:red;">' + error + '</span>');
          })
        }
      }
    });
  });
});
</script>
@endsection