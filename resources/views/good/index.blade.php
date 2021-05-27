@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Good Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Good</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    
<section class="content">
   <div class="card">
      <div class="card-header">
        @if(Auth::user()->hasPermissionTo('good.store','web'))
         <a href="{{route('good.store')}}" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</a>
         @endif
         @if(Auth::user()->hasPermissionTo('good.trash','web'))
         <div class="card-tools">
              <a href="{{route('good.trash')}}" class="btn btn-danger btn-sm float-right ml-1" title="Daftar Barang Terhapus">
                   <i class="fas fa-trash-restore-alt fa-lg"></i>
              </a>            
          </div>
          @endif
      </div>
      <div class="card-body">
        <div class="table-responsive">
         <table id="goods-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalGood">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormGood">
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
                           <div class="form-group ">
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Barang" required  maxlength="30">
                           </div>
                           <div class="form-group ">
                              <input type="text" class="form-control" id="brand" name="brand" placeholder="Masukkan nama Brand" required  maxlength="30">
                           </div>
                           <div class="form-group ">
                           <textarea class="form-control" id="description" name="description" rows="3" placeholder="Masukkan Keterangan Barang"></textarea>
                           </div>
                           <div class="form-group">
                              <select class="form-control" name="type" id="type">
                                 <option value="" disabled selected>Pilih Type</option>
                                 <option value="Stock">Stock</option>
                                 <option value="Production">Production</option>
                                 <option value="Pengemasan">Pengemasan</option>
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
<div class="modal" tabindex="-1" role="dialog" id="deleteGoodModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormDeleteGood">
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
   
   $("#name_shelf").select2({
         tags: true,
         placeholder: "Pilih atau Buat Rak",
         tags: true
      });


   var table = $('#goods-table').DataTable({
       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "{{route('good.index')}}",
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
              title :"barcode",
               "data": "barcode",
               render : function (data, type, row){
                return  '<img src="/barcode/'+data+'" alt="barcode" style="width: 250;" />';
              },
              "orderable": false,
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
           title :"Action",
               render: function(data, type, row) {
                   return  '@if(Auth::user()->hasPermissionTo('good.update','web'))<a href="/good/update/'+row.id+'" data-toggle="tooltip" title="Edit" class="edit-btn  badge badge-info" data-name="'+row.name+'" data-id="'+row.id+'"><i class="far fa-edit fa-lg"></i></a> &nbsp;@endif' + 
                    '@if(Auth::user()->hasPermissionTo('good.destroy','web'))<a href="#" class="btn-delete badge badge-danger" data-name="'+row.name+'" data-id="'+row.id+'"  data-toggle="tooltip" data-placement="bottom" title="Hapus"><i class="fa fa-trash fa-lg"></i></a> &nbsp;@endif';
               },
           }
       ],
       "order": [1, 'desc'],
       "fnCreatedRow": function(nRow, aData, iDataIndex) {
           $(nRow).attr('data', JSON.stringify(aData));
       }
   }); 
    
   $("#barcode").JsBarcode("Hi!");
   
   //GoodLocation
   $('#goods-table').on('click', '.btn-location', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#FormLocationGood #good_id').val(id);
        $('#LocationGoodModal').modal('show');
        $('#FormLocationGood .modal-title').text("Konfirmasi Location");
    });

    $('#FormLocationGood').submit(function(event) {
        event.preventDefault();
        var form =$('#FormLocationGood');
        var data = form.serialize();
        $.ajax({
            url: '/good/location',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
               if (data.success){
                  toastr.success(data.message);
                  $('#LocationGoodModal').modal('hide');
                  table.draw();
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