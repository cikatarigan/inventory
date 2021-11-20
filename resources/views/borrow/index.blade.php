@extends('layouts.app')
@section('content')


<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Borrow Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">borrow</li>
            </ol>
         </div>
      </div>
   </div>
</section>


<section class="content">
   <div class="card">
      <div class="card-header">
         {{-- @if(Auth::user()->hasPermissionTo('borrow.add','web'))
         <a href="{{route('borrow.add')}}" id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</a>
         @endif --}}
         <label for="exampleInputPassword1">Filter Status </label>
         <select name="status" id="status" class="form-control">
            <option value="" disabled selected>Pilih Status</option>
            <option value="Still Borrow" >Masih di pinjam</option>
            <option value="Done" >Sudah di kembalikan</option>
         </select>
      </div>
      <div class="card-body">
          <div class="table-responsive">
            <table id="borrow-table" class="table display responsive table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
          </div>
      </div>
   </div>
</section>
{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalBorrow">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormPool">
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
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Pools" required style="text-transform: capitalize;" maxlength="30">
                           </div>
                           <div class="form-group">
                              <input type="number" class="form-control" id="capacity" name="capacity" placeholder="Masukkan Kapasitas Pool" required style="text-transform: capitalize;">
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
<div class="modal" tabindex="-1" role="dialog" id="deleteAdminModal">
   <div class="modal-dialog">
      <div class="modal-content">
         <form action="#" method="post" id="FormDeleteAdmin">
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
function format ( d ) {
    return '<b>description:</b> '+d.description+'';
}

      var table = $('#borrow-table').DataTable({
         "bFilter": true,
         "processing": true,
         "serverSide": true,
         "lengthChange": true,
         "responsive" : true,
          "ajax": {
              "url": "/borrow",
              "type": "POST",
              "data": function (d) {
               return $.extend({}, d, {
                     status : $('#status').val(),
                });
             }
          },
          "language": {
              "emptyTable": "Tidak ada data yang tersedia",
          },
          "columns": [
                   {
              "className": 'details-control',
              "orderable": false,
              "data": null,
              "defaultContent": ''
             },
             {
              title :"ID pinjam",
                  "data": "id",
                  "orderable": true,
              },
             {
              title :"Barang",
                  "data": "good.name",
                  "orderable": true,
              },
              {
              title :"Jumlah",
                  "data": "amount",
                  "orderable": true,
              },
              {
             title :"User",
                  "data": "user.name",
                  "orderable": true,
              },
              {
              title :"Status",
                  "data": "status",
                  render : function (data, type, row){
                      if(data == 'Still Borrow'){
                          return '<span class="badge badge-warning">Masih di pinjam </span>';
                      }else{
                         return ' <span class="badge badge-info">Sudah kembali</span>';
                      }
                  },
                  "orderable": false,
              },
               {
             title :"Created At",
                  "data": "created_at",
                  render : function (data, type, row){
                  return moment(data).format('dddd, Do MMMM YYYY h:mm')
                },
                  "orderable": true,
              }

          ],
          "order": [1, 'desc'],
          "fnCreatedRow": function(nRow, aData, iDataIndex) {
              $(nRow).attr('data', JSON.stringify(aData));
          }
      });

      // Add event listener for opening and closing details
    $('#borrow-table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $('#status').change(function (event) {
      table.draw();
    });


   });
</script>
@endsection
