@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Samples Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Sample</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content consolata-regular">
   <div class="card">
      <div class="card-header">
         @if(Auth::user()->hasPermissionTo('sample.store','web'))
         <a href="{{route('sample.store')}}" id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</a>
         @endif

      </div>
      <div class="card-body">
        <div class="table-responsive">
             <table id="sample-table" class="table table-striped table-bordered dataTable display" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Delete --}}
<div class="modal" tabindex="-1" role="dialog" id="SampleDeleteModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormDeleteSample">
        <input type="hidden" id="id_delete" name="id_delete" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="del-success"></p>
        <p id="del-success">Apakah Anda yakin ingin menghapus Sample ini ?</p>
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

   var table = $('#sample-table').DataTable({

       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "/sample",
           "type": "POST",
       },
       "language": {
           "emptyTable": "Tidak ada data yang tersedia",
       },
       "columns": [{
        "data": "id",
        title :"No",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
           {
              title :"Lemari",
               "data": "cupboard",
               "orderable": true,
           },
           {
              title :"Kode",
               "data": "code",
               "orderable": true,
           },
           {
              title :"Title",
               "data": "title",
               "orderable": true,
           },
           {
              title :"Sumber",
               "data": "location",
               "orderable": true,
           },
           {
            title :"Action",
               render: function(data, type, row) {
                   return  '@if(Auth::user()->hasPermissionTo('sample.update','web'))<a href="/sample/update/'+row.id+'" data-toggle="tooltip" title="Edit" class="edit-btn  badge badge-info" data-name="'+row.name+'" data-id="'+row.id+'"><i class="far fa-edit fa-lg"></i></a> &nbsp;@endif' +
                   '@if(Auth::user()->hasPermissionTo('sample.view','web'))<a href="sample/view/'+row.id+'" data-toggle="tooltip" title="View" class="view-btn  badge badge-warning" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fas fa-eye fa-lg"></i></a> &nbsp;@endif' +  '@if(Auth::user()->hasPermissionTo('sample.destroy','web'))<a href="#" class="btn-delete badge badge-danger" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fa fa-trash fa-lg"></i></a> &nbsp; @endif' ;

               },
           }
       ],
       "order": [1, 'desc'],
       "fnCreatedRow": function(nRow, aData, iDataIndex) {
           $(nRow).attr('data', JSON.stringify(aData));
       }
   });


    $('#sample-table').on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        $('#FormDeleteSample #id_delete').val(id);
        console.log(id);
        $('#SampleDeleteModal').modal('show');
        $('#FormDeleteSample .modal-title').text("Konfirmasi");
    });

    $('#FormDeleteSample').submit(function(event) {
        event.preventDefault();
        var form =$('#FormDeleteSample');
        var data = form.serialize();
        $.ajax({
            url: '/sample/destroy',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
              console.log(data)
               if (data.success){
                  toastr.success('Data Telah dihapus');
                  $('#SampleDeleteModal').modal('hide');
                  table.draw();
               }else{
                  toastr.error(data.message);
                  $('#SampleDeleteModal').modal('hide');
               }
            },
        })
    });


});
</script>
@endsection
