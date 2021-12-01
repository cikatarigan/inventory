@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Role Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Role</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
   <div class="card">
      <div class="card-header">
        @if(Auth::user()->hasPermissionTo('role.store','web'))
         <button id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
         @endif
      </div>
      <div class="card-body">
        <div class="table-responsive">
         <table id="role-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalRole">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormRole">
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
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Role" required style="text-transform: capitalize;" maxlength="30">
                           </div>
                           <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Deskripsi Role"></textarea>
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
<div class="modal" tabindex="-1" role="dialog" id="deleteRoleModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormDeleteRole">
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
@endsection
@section('script')
<script>
jQuery(document).ready(function($) {

   var table = $('#role-table').DataTable({
       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "/role",
           "type": "POST",
       },
       "language": {
           "emptyTable": "Tidak ada data yang tersedia",
       },
       "columns": [{
               title : "Name Role",
               "data": "name",
               "orderable": true,
           },
           {
               title : "Deskripsi",
               "data": "description",
               "orderable": true,
           },
           {
              title :"Created At",
               "data": "created_at",
                render : function (data, type, row){
               return moment(data).format('dddd, Do MMMM YYYY h:mm')
                },
               "orderable": true,
           },
           {
           title :"Action",
               render: function(data, type, row) {
                   return  '@if(Auth::user()->hasPermissionTo('role.edit','web'))<a href="/role/edit/'+row.id+'" data-toggle="tooltip" title="Edit" class="edit-btn  badge badge-info" data-name="'+row.name+'" data-id="'+row.id+'"><i class="far fa-edit fa-lg"></i></a> &nbsp; @endif' + '@if(Auth::user()->hasPermissionTo('role.destroy','web'))<a href="#" class="btn-delete badge badge-danger" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fa fa-trash fa-lg"></i></a> &nbsp; @endif';
               },
           }
       ],
       "order": [1, 'desc'],
       "fnCreatedRow": function(nRow, aData, iDataIndex) {
           $(nRow).attr('data', JSON.stringify(aData));
       }
   });

    var url;

      // Add
    $('#btnAdd').click(function () {
        $('#FormRole')[0].reset();
        $('#FormRole button[type=submit]').button('reset');
        $('#FormRole .modal-title').text("Add Role");
        $('#FormRole div.form-group').removeClass('has-error');
        $('#FormRole .help-block').empty();

        $('#FormRole input[name="_method"]').remove();
        url = '{{ route("role.store") }}';

        var data = $('#FormRole').serializeArray();
        $.each(data, function(key, value){
            $("#FormRole input[name='" + data[key].name + "']").parent().find('.help-block').hide();
        });

        $('#ModalRole').modal('show');
    });


       $('#FormRole').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormRole');
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
                     $('#ModalRole').modal('hide');
                     table.draw();
                 } else {
                     toastr.error(data.message);
                 }
             },
             error: function(response) {
               if(response.status === 422){
                let errors = response.responseJSON.errors;
                $.each(errors, function(key, error){
                  var item = form.find('input[name='+ key +']');
                  item = (item.length > 0) ? item : form.find('select[name='+ key +']');
                  item = (item.length > 0) ? item : form.find('textarea[name='+ key +']');
                  item = (item.length > 0) ? item : form.find("input[name='"+ key +"[]']");

                 var parent = (item.parent().hasClass('form-group')) ? item.parent() : item.parent().parent();
                  parent.addClass('has-error');
                  parent.append('<span class="help-block" style="color:red;">'+ error +'</span>');
                })
              }
            }
         })
     });


     $('#role-table').on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#FormDeleteRole #id_delete').val(id);
        $('#deleteRoleModal').modal('show');
        $('#FormDeleteRole .modal-title').text("Konfirmasi Hapus");
        $('#FormDeleteRole #del-success').html("Apakah Anda yakin ingin menghapus Lokasi <b>"+name+"</b> ini ?");
    });

   $('#FormDeleteRole').submit(function(event) {
        event.preventDefault();
        var form =$('#FormDeleteRole');
        var data = form.serialize();
        $.ajax({
            url: '/role/delete',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
               if (data.success){
                  toastr.success(data.message);
                  $('#deleteRoleModal').modal('hide');
                  table.draw();
               } else{
                 toastr.error(data.message);
               }
            },
        })
    });


});
</script>
@endsection
