@extends('layouts.app')
@section('content')


<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Stock Entry Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Pools</li>
            </ol>
         </div>
      </div>
   </div>
</section>


<section class="content">
   <div class="card">
      <div class="card-header">
         @if(Auth::user()->hasPermissionTo('stockentry.add','web'))
         <a href="{{route('stockentry.add')}}" id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</a>
         @endif
      </div>
      <div class="card-body">
          <div class="table-responsive">
            <table id="stockentry-table" class="table display responsive table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
          </div>
      </div>
   </div>
</section>
{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalPool">
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
{{-- Modal show barcode --}}
<div class="modal" tabindex="-1" role="dialog" id="ShowQrcodeModal">
   <div class="modal-dialog">
      <div class="modal-content">
         <form action="#" method="post" id="FormDeleteAdmin">

            <div class="modal-header">
               <h4 class="modal-title">QR CODE</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <div class="text-center">
                 <div id="qrcode"></div>
                Nama Barang : <p id="NameGoods"></p>
                Expired : <p id="ExpiredOn"></p>
              </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
         </form>
      </div>
   </div>
</div>


{{-- Modal Print Barcode--}}
<div class="modal" tabindex="-1" role="dialog" id="PrintQrCodeModal">
    <div class="modal-dialog">
      <div class="modal-content">
            <form id="formQrCodeModal" method="POST" action="about:blank" target="newStuff" />
          <input type="hidden" id="id_print" name="id" value="">
        <div class="modal-header">
          <h4 class="modal-title">Konfirmasi</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="number" class="form-control" id="loop" name="loop" placeholder="Masukkan jumlah di Print" required  min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button   id="ButtonPrint" class="btn btn-primary">Print</button>
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
      var table = $('#stockentry-table').DataTable({
          "bFilter": true,
          "processing": true,
          "serverSide": true,
          "lengthChange": true,
          "responsive" : true,
          "ajax": {
              "url": "/receipt",
              "type": "POST",
          },
          "language": {
              "emptyTable": "Tidak ada data yang tersedia",
          },
          "columns": [
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
              title :"Lokasi",
                  "data": "location_shelf.location.name",
                  "orderable": true,
              },
              {
              title :"Tempat Lokasi",
                  "data": "location_shelf.name_shelf",
                  "orderable": true,
              },
             {
             title :"Created At",
                  "data": "created_at",
                  render : function (data, type, row){
                    return moment(data).format('Do MMMM YYYY h:mm')
                  },
                  "orderable": true,
              },
              {
             title :"Handle By",
                  "data": "user.name",
                  "orderable": true,
              },
              {
             title :"Status",
                  "data": "status",
                  render : function (data, type, row){
                     if(data == "expired"){
                        return '<span class="badge badge-secondary">kadaluarsa</span>';
                     }else if(data == "No Expired"){
                        return  '<span class="badge badge-info">Tidak ada Kadaluarsa</span>';
                     }else if (data == "Still Use"){
                        return  '<span class="badge badge-success">Ready</span>';
                     }else if(data == "Expired") {
                        return  '<span class="badge badge-warning">Expired</span>';
                     }else {
                        return  '<span class="badge badge-danger">Barang Habis</span>';
                     }
                }
              },
            {
           title :"Action",
               render: function(data, type, row) {
                   return  '<a href="#" data-toggle="tooltip" title="QR CODE" class="edit-btn  badge badge-info" data-qrcode="'+row.qrcode+'"  data-id="'+row.id+'"><i class="fas fa-qrcode fa-lg"></i></a> &nbsp;<a href="#" data-toggle="tooltip" title="QR CODE" class="print-btn  badge badge-warning" data-qrcode="'+row.qrcode+'"  data-id="'+row.id+'"><i class="fas fa-print fa-lg"></i></a> &nbsp;'
                     ;
               },
              "orderable": false,
           }
          ],
          "order": [4, 'desc'],
          "fnCreatedRow": function(nRow, aData, iDataIndex) {
              $(nRow).attr('data', JSON.stringify(aData));
          }
      });



     // showQrCode
     $('#stockentry-table').on('click', '.edit-btn', function(e){
         var aData = JSON.parse($(this).parent().parent().attr('data'));
        console.log(aData);
         $('#id').val(aData.id);

         $('#ShowQrcodeModal').modal('show');
         $('#NameGoods').text(aData.good.name);
         if(aData.date_expired != null){
            $('#ExpiredOn').text(moment(aData.date_expired).format(('MM/DD/YYYY')));
         }else{
            $('#ExpiredOn').text('Tidak barang Kadaluarsa');
         }

          $('#qrcode').empty();
            $('#qrcode').qrcode({
             text: aData.qrcode,
          });

     });

     $('#stockentry-table').on('click', '.print-btn', function(e){
        var aData = JSON.parse($(this).parent().parent().attr('data'));
        var id = $('#id_print').val(aData.id);
        $('#PrintQrCodeModal').modal('show');

     });



   $('#ButtonPrint').click(function () {
        var id = $('#id_print').val();
         var loop = $('#loop').val();
        window.open('qr-code/print/'+ id + '/'+ loop);

   });

});
</script>
@endsection
