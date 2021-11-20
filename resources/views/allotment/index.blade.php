@extends('layouts.app')
@section('content')


<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Pemberian Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Allotment</li>
            </ol>
         </div>
      </div>
   </div>
</section>


<section class="content">
   <div class="card">
      <div class="card-header">
         {{-- @if(Auth::user()->hasPermissionTo('allotment.add','web'))
         <a href="{{route('allotment.add')}}" id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</a>
         @endif --}}
         <label for="exampleInputPassword1">Filter Tanggal </label>
         <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
         </div>
      </div>
      <div class="card-body">
          <div class="table-responsive">
            <table id="allotment-table" class="table display responsive table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
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

    function dateCustom(date){
                date = date.split('-');
                var day   = date[2];
                var mount = parseInt(date[1]);
                var year  = date[0];

                var mountChar = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                return day + ' ' + mountChar[mount] + ' ' + year;

            }

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }


    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

        //   console.log( $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')));
         $('#reportrange').on('apply.daterangepicker', (e, picker) => {
            table.draw();
         });


    cb(start, end);

    function format ( d ) {
    return '<b>description:</b> '+d.description+'';
    }

      var table = $('#allotment-table').DataTable({
          "bFilter": true,
          "processing": true,
          "serverSide": true,
          "lengthChange": true,
          "responsive" : true,
          "ajax": {
              "url": "/allotment",
              "type": "POST",
              "data": function (d) {
               return $.extend({}, d, {
                  'startdate' : $('#reportrange').data('daterangepicker').startDate._d.getFullYear()+'-'+($('#reportrange').data('daterangepicker').startDate._d.getMonth()+1)+'-'+$('#reportrange').data('daterangepicker').startDate._d.getDate(),
                   'enddate' : $('#reportrange').data('daterangepicker').endDate._d.getFullYear()+'-'+($('#reportrange').data('daterangepicker').endDate._d.getMonth()+1)+'-'+$('#reportrange').data('daterangepicker').endDate._d.getDate(),
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
              title :"Kepada",
                  "data": "user.name",
                  "orderable": true,
              },
              {
              title :"Lokasi",
                  "data": "location_shelf.location.name",
                  "orderable": true,
              },

              {
              title :"Ruangan Lokasi",
                  "data": "location_shelf.name_shelf",
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
             title :"Handle By",
                  "data": "user.name",
                  "orderable": true,
              }
          ],
          "order": [6, 'desc'],
          "fnCreatedRow": function(nRow, aData, iDataIndex) {
              $(nRow).attr('data', JSON.stringify(aData));
          }
      });
        // Add event listener for opening and closing details
    $('#allotment-table tbody').on('click', 'td.details-control', function () {
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
