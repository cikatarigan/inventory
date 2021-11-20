@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Good TrashTable</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="{{route('good.index')}}">Good</a></li>
              <li class="breadcrumb-item active">Trash</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
   <div class="card">
      <div class="card-header">
      </div>
      <div class="card-body">
        <div class="table-responsive">
         <table id="goods-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Restore --}}
<div class="modal" tabindex="-1" role="dialog" id="restoreGoodModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormRestoreGood">
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

   var table = $('#goods-table').DataTable({
       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "/good/trash",
           "type": "POST",
       },
       "language": {
           "emptyTable": "Tidak ada data yang tersedia",
       },
       "columns": [{
               title : "Name Barang",
               "data": "name",
               "orderable": true,
           },
           {
              title :"Category",
               "data": "category",
               "orderable": true,
           },
           {
              title :"Brand",
               "data": "brand",
               "orderable": true,
           },
           {
              title :"Ada Expired",
               "data": "isexpired",
               render: function(data, type, row) {
                   if(data == "on"){
                       return  'Ada Expired';
                   }else{
                        return  'Tidak ada Expired';
                   }
               }
           },
           {
           title :"Action",
               render: function(data, type, row) {
                   return  '@if(Auth::user()->hasPermissionTo('good.restore','web'))<a href="#" data-toggle="tooltip" title="Restore" class="btn-restore badge badge-info" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fas fa-redo fa-lg"></i></a> &nbsp; @endif';
               },
           }
       ],
       "order": [1, 'desc'],
       "fnCreatedRow": function(nRow, aData, iDataIndex) {
           $(nRow).attr('data', JSON.stringify(aData));
       }
   });

    // Restore
       $('#goods-table').on('click', '.btn-restore', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#FormRestoreGood #id_delete').val(id);
        $('#restoreGoodModal').modal('show');
        $('#FormRestoreGood .modal-title').text("Konfirmasi Restore");
        $('#FormRestoreGood #del-success').html("Apakah Anda yakin ingin Restore barang <b>"+name+"</b> ini ?");
    });

       $('#FormRestoreGood').submit(function(event) {
        event.preventDefault();
        var form =$('#FormRestoreGood');
        var data = form.serialize();
        $.ajax({
            url: '/good/restore',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
               if (data.success){
                  toastr.success(data.message);
                  $('#restoreGoodModal').modal('hide');
                  table.draw();
               }
            },
        })
    });


});
</script>
@endsection
