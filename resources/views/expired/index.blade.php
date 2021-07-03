@extends('layouts.app')
@section('content')


<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Expired Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Expired</li>
            </ol>
         </div>
      </div>
   </div>
</section>


<section class="content">
   <div class="card">

      <div class="card-body">
          <div class="table-responsive">
            <table id="expired-table" class="table display responsive table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
          </div>
      </div>
   </div>
</section>

@endsection
@section('script')
<script>
   jQuery(document).ready(function($) { 
      var table = $('#expired-table').DataTable({
          "bFilter": true,
          "processing": true,
          "serverSide": true,
          "lengthChange": true,
          "responsive" : true,
          "ajax": {
              "url": "/expired",
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
             title :"location",
                  "data": "location.name",
                  "orderable": true,
              },
               {
             title :"Expired Date",
                  "data": "date",
                  render : function (data, type, row){
                  return moment(data).format('dddd, Do MMMM YYYY')
                },
                  "orderable": true,
              }

          ],
          "order": [0, 'desc'],
          "fnCreatedRow": function(nRow, aData, iDataIndex) {
              $(nRow).attr('data', JSON.stringify(aData));
          }
      }); 
  
   }); 
</script>
@endsection