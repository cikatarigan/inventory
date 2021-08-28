@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Return Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Return</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<div class="container-fluid">
   <div class="row">
      <div class="col-md-6">
         <div class="card card-primary">
            <div class="card-header">
               <h3 class="card-title">Form Pengembalian</h3>
            </div>
            <form role="form" id="FormReturnCheck">
               <div class="card-body">
                <div class="form-group">
                     <label for="exampleInputPassword1">Kepada</label>
                     <select class="js-example-basic-single form-control select-custom" id="user" name="user" width="100%">
                        <option value="" disabled selected>Pilih User</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Barang</label>
                     <select class="js-example-basic-single form-control select-custom" id="goods" name="goods" width="100%">
                      <option value="" disabled selected>Pilih Barang</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Jumlah Barang</label>
                     <input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah" autocomplete="off">
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Location</label>
                     <select class="js-example-basic-single form-control select-custom" id="location" name="location" width="100%">
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Name Shelf</label>
                     <select class="js-example-basic-single form-control select-custom" id="nameshelf" name="nameshelf" width="100%">
                        <option value="" disabled selected>Pilih Rak</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Keterangan</label>
                     <textarea  class="form-control" id="description" name="description" rows="3"></textarea>
                  </div>
                    <div class="form-group" id="formexpired">
                       <label for="exampleInputPassword1">Date_expired</label>
                       <div class="input-group date" data-provide="datepicker">
                          <input class="datepicker form-control" id="date_expired" name="date_expired" data-date-format="mm/dd/yyyy" autocomplete="off">
                          <div class="input-group-addon">
                             <span class="glyphicon glyphicon-th"></span>
                          </div>
                       </div>
                    </div>
               </div>
               <!-- /.card-body -->
               <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

  <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormReturn">
            <div class="modal-header">
               <h5 class="modal-title">Konfirmasi password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="box box-info">
                  <div class="box-header">
                     <div class="box-body">
                          <input type="hidden" id="locationCheck" name="data_location">
                          <input type="hidden" id="shelfCheck" name="data_shelf">
                          <input type="hidden" id="goodsCheck" name="data_goods">
                          <input type="hidden" id="amountCheck" name="data_amount">
                          <input type="hidden" id="userCheck" name="data_user"> 
                           <input type="hidden" id="descriptionCheck" name="data_description">                      
                           <div class="form-group">
                              <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password Penerima">
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
 jQuery(document).ready(function() {

     $('#user').select2({
         placeholder: "Pilih User",
         ajax: {
             url: '/find/users',
             dataType: 'json'
         }
     });

     $('#goods').select2({
         placeholder: "Pilih Barang",
         ajax: {
             url: function (params) {
            
                 return '/find/borrows/' + $('#user').val();
             },
             dataType: 'json'
         }
     });

    $('#location').select2({
         placeholder: "Pilih location",
         ajax: {
             url: '/find/locations',
             dataType: 'json'
         }
     });


    $('#nameshelf').select2({
        minimumResultsForSearch: -1,
         placeholder: "Pilih Rak",
         tags: true,
         ajax: {
             url: function (params) {
                 return '/find/shelf/' + $('#location').val();
             },
             dataType: 'json'
         }
     });

   $('#goods').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        if(data.isexpired == 'on'){
          $('#formexpired').show();
        }else{
          $('#formexpired').hide();
        }
    });
      
    $('#FormReturnCheck').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormReturnCheck');
         var data = form.serialize();
         $.ajax({
             url: '/return/check',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {  
                    $('#exampleModal').modal('show');
                     var location = $("#location").val();
                     var shelf = $('#nameshelf').val();
                     var goods = $("#goods").val();
                     var amount = $("#amount").val();
                     var user = $("#user").val();
                     var description = $("#description").val();
                     
                      $('#FormReturn #locationCheck').val(location);
                      $('#FormReturn #shelfCheck').val(shelf);
                      $('#FormReturn #goodsCheck').val(goods);
                      $('#FormReturn #amountCheck').val(amount);
                      $('#FormReturn #userCheck').val(user);
                      $('#FormReturn #descriptionCheck').val(description);

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

        $('#FormReturn').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormReturn');
         var data = form.serialize();
         $.ajax({
             url: '/return/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                      window.setTimeout(function() {
                        window.location.href = '/return';
                    }, 2000);
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

     $('#user').change(function (event) {
         $('#goods').empty();
     });

 });
</script>
@endsection