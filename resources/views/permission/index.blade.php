@extends('layouts.app')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Permission Table</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Permission</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
<section class="content">
   <div class="card">
      <div class="card-body">
        <div class="table-responsive">
         <table id="permission-table" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;"></table>
        </div>
      </div>
   </div>
</section>

{{-- Modal Add --}}
<div class="modal fade" tabindex="-1" role="dialog" id="ModalRole">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormRole">
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
                              <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Role" required style="text-transform: capitalize;" maxlength="30">
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
   
   var table = $('#permission-table').DataTable({
      "pageLength": 100,
       "bFilter": true,
       "processing": true,
       "serverSide": true,
       "lengthChange": true,
       "responsive" : true,
       "ajax": {
           "url": "/permission",
           "type": "POST",
       },
       "language": {
           "emptyTable": "Tidak ada data yang tersedia",
       },
       "columns": [{
               title : "Name",
               "data": "name",
               "orderable": true,
           },
           {
               title : "Display_name",
               "data": "display_name",
               "orderable": true,
           }
       ],
       "fnCreatedRow": function(nRow, aData, iDataIndex) {
           $(nRow).attr('data', JSON.stringify(aData));
       }
   }); 

    var url;

    
       // Edit
       $('#permission-table').on('click', '.edit-btn', function(e){
           $('#FormRole .modal-title').text("Edit Role");
           $('#FormRole .help-block').empty();
           $('#FormRole')[0].reset();
   
           var aData = JSON.parse($(this).parent().parent().attr('data'));
           console.log(aData);
   
           $('#FormRole .modal-body .form-horizontal').append('<input type="hidden" name="_method" value="POST">');
          
           url= '{{ route("good.index") }}' + '/update/' + aData.id;
   
           var data = $('#FormRole').serializeArray();
   
           $.each(data, function(key, value){
               $("#FormRole input[name='" + data[key].name + "']").parent().find('.help-block').hide();
           });   

           $('#id').val(aData.id);
           $('#name').val(aData.name);
           $('#location').val(aData.location);
           $('#role').val(aData.role);
           $('#ModalRole').modal('show');       
       });
   
       $('#FormRole').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormRole');
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
                     $('#ModalRole').modal('hide');
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