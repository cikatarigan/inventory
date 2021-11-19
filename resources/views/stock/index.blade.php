@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Stock Goods Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Stock Goods</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
  <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                 <table id="stock-table" class="table table-striped table-bordered dataTable no-footer display" style="width: 100%;"></table>
            </div>
       </div>
      </div>
</section>
@endsection
@hasrole('admin')
@section('script')
<script>
jQuery(document).ready(function($) { 
   function format ( d ) {
    var test = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td><b>Lokasi</b></td>'+
            '<td><b>Lokasi</b></td>'+
            '<td><b>Stock</b></td>'+
            '</tr>';+
            
   $.each(d.stock , function(index, val) { 
     test +=  '<tr>'+
            '<td>'+val.sublocation+'</td>'+       
            '<td>'+val.location+'</td>'+
            '<td>'+val.stock+'</td>'+
        '</tr>';
        
    });
  
   return test +='</table>';
}

 var table = $('#stock-table').DataTable({
     "bFilter": true,
     "processing": true,
     "serverSide": true,
     "lengthChange": true,
     "responsive" : true,
     "ajax": {
         "url": "/stock/goods",
         "type": "POST",
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
             title : "Nama Barang",
             "data": "name",
         },
         {
            title :"Brand",
             "data": "brand",
             "orderable": true,
         },
         {
            title :"Category",
             "data": "category",
             "orderable": true,
         },
         {
            title :"Stock Keseluruhan",
             "data": "stock",
           render: function (data) {
            var temp = 0
            for(var i = 0; i < data.length ; i++){
               temp += data[i].stock;
            }
              return temp;  
            
          },
             "orderable": true,
         },
        {
            title :"Unit",
             "data": "unit",
             "orderable": true,
         },
        {
        title :"Action",
           render: function(data, type, row) {

            return '<a href="/stock/goods/details/'+row.id+'" data-toggle="tooltip" role="button" data-placement="bottom" title="Informasi Detail" class="edit-btn btn btn-info btn-flat" data-name="'+row.name+'" data-id="'+row.id+'"><i class="fa fa-list "></i></a> &nbsp;'
           }
         
         }

     ],
     "order": [1, 'desc'],
     "fnCreatedRow": function(nRow, aData, iDataIndex) {
         $(nRow).attr('data', JSON.stringify(aData));
     }
 }); 

// Add event listener for opening and closing details
    $('#stock-table tbody').on('click', 'td.details-control', function () {
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

}); 
</script>

@endsection
@else
@section('script')
<script>

jQuery(document).ready(function($) {
 var table = $('#stock-table').DataTable({
     "bFilter": true,
     "processing": true,
     "serverSide": true,
     "lengthChange": true,
     "responsive" : true,
     "ajax": {
         "url": "/admin/stock/goods",
         "type": "POST",
     },
     "language": {
         "emptyTable": "Tidak ada data yang tersedia",
     },
     "columns": [          {
             title : "Nama Barang",
             "data": "name",
         },
         {
            title :"Type",
             "data": "type",
             "orderable": true,
         },
         {
            title :"Stock",
             "data": "stock",
             "orderable": false,
         }

     ],
     "order": [1, 'desc'],
     "fnCreatedRow": function(nRow, aData, iDataIndex) {
         $(nRow).attr('data', JSON.stringify(aData));
     }
 });
});  
</script>
@endsection

@endhasrole