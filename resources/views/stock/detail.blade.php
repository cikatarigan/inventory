@extends('layouts.app')
@section('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css">
@endsection
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Detail Stock History</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item ">Stock</li>
               <li class="breadcrumb-item active">Detail Stock</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<section class="content">

   <div class="card">
      <div class="wrapper row my-5 mx-2">
         <div class="preview col-md-6">
            <div class="preview-pic tab-content">
               @foreach($good->good_images as $key => $item)
               <div class="tab-pane @if($key== 0){{'active'}}@endif" id="pic-{{$item->id}}">
                  <img src="{{Storage::url($item->image->path)}}"  style="height: 450px" />
               </div>
               @endforeach
            </div>
            <ul class="preview-thumbnail nav nav-tabs">
               @foreach($good->good_images as $key => $item)
               <li class="@if($key== 0){{'active'}} @endif">
                  <a data-target="#pic-{{$item->id}}" data-toggle="tab">
                  <img src="{{Storage::url($item->image->path)}}" style="height: 120px;" />
                  </a>
               </li>
               @endforeach 
            </ul>
         </div>
         <div class="details col-md-6">
            <h3 class="product-description">{{$good->description}}</h3>
            <p class="product-description">{{$good->description}}</p>
                <table class="table table-striped">
                     <tr>
                        <td> <b>Brand</b></td>
                        <td>:</td>
                        <td> {{$good->brand}}</td>
                     </tr>
                     <tr>
                        <td><b>Category</b> </td>
                        <td>:</td>
                        <td> {{$good->category}}</td>
                     </tr>
                     <tr>
                        <td><b>Unit</b> </td>
                        <td>:</td>
                        <td> {{$good->unit}}</td>
                     </tr>
                     <tr>
                        <td><b>Expired</b> </td>
                        <td>:</td>
                        <td>@if($good->isexpired == null)-  @else{{$good->isexpired}}@endif</td>
                     </tr>  
                
                  </table>

                
         </div>
      </div>
   </div>


<div class="row">
   <div class="col-md-12">
      <section class="content">
         <div class="card">
            <div class="card-body">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <div class="row form-group">
                           <label class="col-sm-4 col-form-label">Lokasi</label>
                           <div class="col-sm-8">
                              <select name="location_id" id="location_id" class="form-control">
                                 <option value="" disabled selected>Pilih Lokasi</option>
                                 <option value="" >Semua</option>
                                 @foreach($location as $item)
                                 <option value="{{$item->id}}">{{$item->name}}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="table-responsive">
                  <table id="stock-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
               </div>
            </div>
      </section>
      </div>

</div>
</section>
@endsection
@section('script')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"> </script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"> </script>
<script>


   jQuery(document).ready(function($) { 
  
    var date_start = "";
    var date_end = "";
   

    console.log(date_start);
   
    var table = $('#stock-table').DataTable({
      dom: '<"html5buttons">Bfrtip',
      lengthMenu: [[25, 100, -1], [25, 100, "All"]],
      pageLength: 25,
      language: {
      buttons: {
                  colvis : 'show / hide', // label button show / hide
                  colvisRestore: "Reset Kolom" //lael untuk reset kolom ke default
                }
              },
              buttons : [
              {extend: 'colvis', postfixButtons: [ 'colvisRestore' ] },
              {extend:'csv'},
              {extend: 'pdf', title:'File PDF Datatables'},
              {extend: 'excel', title: 'File Excel Datatables'},
              {extend:'print',title: 'Print Datatables'},
              ],
              "bFilter": true,
              "processing": true,
              "serverSide": true,
              "lengthChange": true,
              "responsive" : true,
              "ajax": {
                "url": "{{route('stock.detail' ,['id' => $id] )}}",
                "type": "POST",
                "data": function (d) {
                  return $.extend({}, d, {
                    location_id : $('#location_id').val(),
                    date_start : date_start,
                    date_end : date_end,
                  });
                }
              },
              "language": {
                "emptyTable": "Tidak ada data yang tersedia",
              },
              "columns": [{
                title : "Stock Awal",
                "data": "start_balance",
                "orderable": false,
              },
              {
               title :"Jumlah",
               render : function (data, type, row){
                 if(row.type == 'IN'){
                   return '<span class="text-success">+ '+row.amount+'</span>';
                 }else{
                   return '<span class="text-danger">- '+row.amount+ '</span>';
                 }
               },
               "orderable": false,
             },
             {
               title :"Stock Akhir",
               "data": "end_balance",
               "orderable": false,
             },
             {

               title :"Type",
               "data": "detailable_type",
               render: function (data) {
                 if(data == 'App\\Models\\StockTaking'){
                   return'Stock Opname';
                 }else if(data == 'App\\Models\\Production'){
                   return 'Produksi';
                 }else if(data == 'App\\Models\\StockEntry'){
                   return  'Stock Entri';
                 }else if(data == 'App\\Models\\Allotment'){
                   return 'Pemberian';
                  }else if(data == 'App\\Models\\Expired'){
                    return 'Expired'; 
                 }else {
                   return 'Peminjaman';
                 }
               },
               "orderable": false,
             },
             {
               title :"Lokasi",
               "data": "location_shelf.location.name",
               "orderable": false,
             },
              {
               title :"Ruangan",
               "data": "location_shelf.name_shelf",
               "orderable": false,
             },
             {
               title :"Expired Date",
               "data": "stock_entry.date_expired",
               render : function (data, type, row){
                return moment(data).format('Do MMMM YYYY')
              },
              "orderable": false,
             },
    
             {
               title :"Tanggal",
               "data": "created_at",
               render : function (data, type, row){
                return moment(data).format('Do MMMM YYYY h:mm')
              },
              "orderable": false,
             }
            ],
            "fnCreatedRow": function(nRow, aData, iDataIndex) {
              $(nRow).attr('data', JSON.stringify(aData));
            }
          }); 

    $('#location_id').change(function (event) {
      table.draw();              
    });
   
  
   }); 
</script>
@endsection