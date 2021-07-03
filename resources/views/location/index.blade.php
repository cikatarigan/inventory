@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Location Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Location</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
   <div class="card">
      <div class="card-header">
         @if(Auth::user()->hasPermissionTo('location.store','web'))
         <button id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
         @endif
         @if(Auth::user()->hasPermissionTo('location.trash','web'))
         <div class="card-tools">
              <a href="{{route('location.trash')}}" class="btn btn-danger btn-sm float-right ml-1" title="Daftar Warehouse Terhapus">
                   <i class="fas fa-trash-restore-alt fa-lg"></i>
              </a>            
          </div>
          @endif
      </div>
      <div class="card-body">
        <div class="table-responsive">
             <table id="location-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalLocation">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormLocation">
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
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lokasi" required  maxlength="30">
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
<div class="modal" tabindex="-1" role="dialog" id="deleteLocationModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormDeleteLocation">
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

{{-- Modal location Shelf --}}
<div class="modal" tabindex="-1" role="dialog" id="addShelfModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormAddShelf">
          <input type="hidden" id="id_location" name="id" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="form-group">
            <input type="text" class="form-control" id="name_shelf" name="name_shelf" placeholder="Insert Name Shelf" required  maxlength="30">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('script')
<script>
jQuery(document).ready(function($) { 
   
   var table = $('#location-table').DataTable({
       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "/location",
           "type": "POST",
       },
       "language": {
           "emptyTable": "Tidak ada data yang tersedia",
       },
       "columns": [
           {
              title :"Nama Location",
               "data": "name",
               "orderable": true,
           },
           {
           title :"Action",
               render: function(data, type, row) {
                   return  '@if(Auth::user()->hasPermissionTo('location.update','web'))<a href="#" data-toggle="tooltip" title="Edit" class="edit-btn  badge badge-info" data-name="'+row.name+'" data-id="'+row.id+'"><i class="far fa-edit fa-lg"></i></a> &nbsp;@endif' + 
                    '@if(Auth::user()->hasPermissionTo('location.destroy','web'))<a href="#" class="btn-delete badge badge-danger" data-name="'+row.name+'" data-id="'+row.id+'"  data-toggle="tooltip" data-placement="bottom" title="Hapus"><i class="fa fa-trash fa-lg"></i></a> &nbsp;@endif' +
                    '@if(Auth::user()->hasPermissionTo('sublocation.store','web'))<a href="#" data-toggle="tooltip" title="Add Sub" class="add-btn  badge badge-success" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fas fa-external-link-alt  fa-lg"></i></a> &nbsp;@endif'  ;
               },
              "orderable": false,
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
        $('#FormLocation')[0].reset();
        $('#FormLocation button[type=submit]').button('reset');
        $('#FormLocation .modal-title').text("Add Location");
        $('#FormLocation div.form-group').removeClass('has-error');
        $('#FormLocation .help-block').empty();

        $('#FormLocation input[name="_method"]').remove();
        url = '{{ route("location.store") }}';

        var data = $('#FormLocation').serializeArray();
        $.each(data, function(key, value){
            $("#FormLocation input[name='" + data[key].name + "']").parent().find('.help-block').hide();
        });

        $('#ModalLocation').modal('show');
    });

       // Edit
       $('#location-table').on('click', '.edit-btn', function(e){
           $('#FormLocation .modal-title').text("Edit Location");
           $('#FormLocation .help-block').empty();
           $('#FormLocation')[0].reset();
   
           var aData = JSON.parse($(this).parent().parent().attr('data'));
           console.log(aData);
   
           $('#FormLocation .modal-body .form-horizontal').append('<input type="hidden" name="_method" value="POST">');
          
           url= '{{ route("location.index") }}' + '/update/' + aData.id;
   
           var data = $('#FormLocation').serializeArray();
   
           $.each(data, function(key, value){
               $("#FormLocation input[name='" + data[key].name + "']").parent().find('.help-block').hide();
           });   

           $('#id').val(aData.id);
           $('#name').val(aData.name);
           $('#ModalLocation').modal('show');       
       });
   
       $('#FormLocation').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormLocation');
         $('#FormLocation div.form-group').removeClass('has-error');
         $('#FormLocation .help-block').empty();
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
                     $('#ModalLocation').modal('hide');
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

       // Delete
       $('#location-table').on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#FormDeleteLocation #id_delete').val(id);
        $('#deleteLocationModal').modal('show');
        $('#FormDeleteLocation .modal-title').text("Konfirmasi Hapus");
        $('#FormDeleteLocation #del-success').html("Apakah Anda yakin ingin menghapus Lokasi <b>"+name+"</b> ini ?");
    });
   
   $('#FormDeleteLocation').submit(function(event) {
        event.preventDefault();
        var form =$('#FormDeleteLocation');
        var data = form.serialize();
        $.ajax({
            url: '/location/delete',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
               if (data.success){
                  toastr.success(data.message);
                  $('#deleteLocationModal').modal('hide');
                  table.draw();
               }
            },
        })
    });




   $('#location-table').on('click', '.add-btn', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#FormAddShelf #id_location').val(id);
        $('#addShelfModal').modal('show');
        $('#FormAddShelf .modal-title').text("Add Shelf");

    });

    $('#FormAddShelf').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormAddShelf');
         $('#FormAddShelf div.form-group').removeClass('has-error');
         $('#FormAddShelf .help-block').empty();
         
         var data = form.serialize();
         $.ajax({
             url: '/sub/location/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                     $('#addShelfModal').modal('hide');
                     $("#FormAddShelf")[0].reset();
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

}); 
</script>
@endsection