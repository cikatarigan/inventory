@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Unit Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Unit</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
        <div class="card">
           <div class="card-header">
             @if(Auth::user()->hasPermissionTo('unit.store','web'))
              <button id="btnAdd" class="text-right btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
              @endif
           </div>
           <div class="card-body">
             <div class="table-responsive">
              <table id="unit-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
             </div>
           </div>
        </div>
     </section>

     {{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalUnit">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
          <form action="#" method="post" id ="FormUnit">
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
                               <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Unit" required style="text-transform: capitalize;" maxlength="30">
                            </div>
                            <div class="form-group">
                             <textarea class="form-control" id="description" name="description" rows="2" placeholder="Deskripsi Unit"></textarea>
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
 @endsection

 @section('script')
 <script>
 jQuery(document).ready(function($) {

    var table = $('#unit-table').DataTable({
        "bFilter": true,
        "processing": true,
        "serverSide": true,
        "lengthChange": true,
        "responsive" : true,
        "ajax": {
            "url": "/unit",
            "type": "POST",
        },
        "language": {
            "emptyTable": "Tidak ada data yang tersedia",
        },
        "columns": [{
                title : "Name Unit",
                "data": "name",
                "orderable": true,
            },
            {
                title : "Deskripsi",
                "data": "description",
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
        ],
        "order": [1, 'desc'],
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
            $(nRow).attr('data', JSON.stringify(aData));
        }
    });


        $('#btnAdd').click(function () {
         $('#FormUnit')[0].reset();
         $('#FormUnit button[type=submit]').button('reset');
         $('#FormUnit .modal-title').text("Add Unit");
         $('#FormUnit div.form-group').removeClass('has-error');
         $('#FormUnit .help-block').empty();

         $('#FormUnit input[name="_method"]').remove();
         url = '{{ route("unit.store") }}';

         var data = $('#FormUnit').serializeArray();
         $.each(data, function(key, value){
             $("#FormUnit input[name='" + data[key].name + "']").parent().find('.help-block').hide();
         });

         $('#ModalUnit').modal('show');
     });


        $('#FormUnit').submit(function (event) {
          event.preventDefault();
          var $this = $(this);
          var form = $('#FormUnit');
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
                      $('#ModalUnit').modal('hide');
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
  });
 </script>
 @endsection
