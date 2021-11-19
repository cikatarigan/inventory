@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Allotment Entry Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item">Allotment</li>
              <li class="breadcrumb-item active">Add</li>
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
               <h3 class="card-title">Form Pemberian</h3>
            </div>
            <form  id="FormAllomentEntry">
               <div class="card-body">
               <div class="form-group">
                <label for="exampleInputPassword1">Lokasi Barang</label>
                  <select name="location_id" id="location_id" class="form-control select-custom ">
                     <option value="" disabled selected>Location</option>
                  </select>
               </div>
                <div class="form-group">
                <label for="exampleInputPassword1">Ruangan Barang</label>
                  <select name="location_shelf" id="location_shelf" class="form-control select-custom ">
                     <option value="" disabled selected>Ruangan barang</option>
                  </select>
               </div>
               <div class="form-group">
                <label for="exampleInputPassword1">Nama Barang</label>
                   <select name="goods" id="goods" class="form-control">
                     <option value="" disabled selected>Barang</option>
                  </select>
               </div>
                  
                  <div class="form-group">
                     <label for="exampleInputPassword1">Jumlah Barang</label>
                     <input type="number" class="form-control" name="amount" id="amount" placeholder="Jumlah">
                  </div>

                  <div class="form-group">
                     <label for="exampleInputPassword1">Kepada</label>
                     <select class="js-example-basic-single form-control select-custom" name="user" id="user" width="100%">
                        <option value="" disabled selected>Pilih User</option>
                        @foreach($users as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                     </select>
                  </div>

                  <div class="form-group">
                     <label for="exampleInputPassword1">Description</label>
                     <textarea class="form-control" name="description" id="description" placeholder="Keterangan" rows="3"></textarea> 
                  </div>
               </div>
               <div class="card-footer">
                <button type="submit" id ="BtnPost" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Modal Password User -->
  <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormAllotment">
            <div class="modal-header">
               <h5 class="modal-title">Konfirmasi password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="box box-info">
                  <div class="box-header">
                     <div class="box-body">
                          <input type="hidden" id="locationCheck" name="data_location" value="">
                          <input type="hidden" id="shelfCheck" name="data_shelf" value="">
                          <input type="hidden" id="goodCheck" name="data_goods" value="">
                          <input type="hidden" id="amountCheck" name="data_amount" value="">
                          <input type="hidden" id="userCheck" name="data_user" value="">
                          <input type="hidden" id="descriptionCheck" name="data_description" value="">
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
      @if (session('success'))
          toastr.success("{{ session('success') }}");
      @endif

      @if (session('error'))
          toastr.error("{{ session('error') }}");
      @endif

     $('#location_id').select2({
         placeholder: "Pilih location",
         ajax: {
             url: '/find/locations',
             dataType: 'json'
         }
     });


    $('#location_shelf').select2({
        minimumResultsForSearch: -1,
         placeholder: "Pilih Rak",
         tags: true,
         ajax: {
             url: function (params) {
                 return '/find/shelf/' + $('#location_id').val();
             },
             dataType: 'json'
         }
     });

     $('#goods').select2({
         placeholder: "Pilih Barang",
         ajax: {
             url: function (params) {
            
                 return '/find/goods/' + $('#location_shelf').val();
             },
             dataType: 'json'
         }
     });

    $('#user').select2();



   $('#FormAllomentEntry').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormAllomentEntry');
         $('#FormAllomentEntry div.form-group').removeClass('has-error');
         $('#FormAllomentEntry .help-block').empty();
         var data = form.serialize();
         $.ajax({
             url: '/allotment/check',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {  
                     $('#exampleModal').modal('show');
                     var location = $("#location_id").val();
                     var shelf = $("#location_shelf").val();
                     var good = $("#goods").val();
                     var amount = $("#amount").val();
                     var user = $("#user").val();
                     var description = $("#description").val();
                      $('#FormAllotment #locationCheck').val(location);
                      $('#FormAllotment #shelfCheck').val(shelf);
                      $('#FormAllotment #goodCheck').val(good);
                      $('#FormAllotment #amountCheck').val(amount);
                      $('#FormAllotment #userCheck').val(user);
                      $('#FormAllotment #descriptionCheck').val(description);
                   
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



    $('#FormAllotment').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormAllotment');

         var data = form.serialize();
         $.ajax({
             url: '/allotment/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                      window.setTimeout(function() {
                        window.location.href = '/allotment';
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


     $('#location_id').change(function (event) {
          $('#location_shelf').empty();
         $('#goods').empty();
     });

     $('#location_shelf').change(function (event) {
         $('#goods').empty();
     });
   
 });
</script>
@endsection