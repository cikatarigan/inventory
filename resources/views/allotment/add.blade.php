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
               <li class="breadcrumb-item active">Allotment</li>
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
                  <select name="locationview" id="locationview" class="form-control select-custom ">
                     <option value="" disabled selected>Location</option>
                  </select>
               </div>
               <div class="form-group">
                <label for="exampleInputPassword1">Nama Barang</label>
                   <select name="goodview" id="goodview" class="form-control">
                     <option value="" disabled selected>Barang</option>
                  </select>
               </div>
                  
                  <div class="form-group">
                     <label for="exampleInputPassword1">Jumlah Barang</label>
                     <input type="number" class="form-control" name="amountview" id="amountview" placeholder="Jumlah">
                  </div>

                  <div class="form-group">
                     <label for="exampleInputPassword1">Kepada</label>
                     <select class="js-example-basic-single form-control select-custom" name="userview" id="userview" width="100%">
                        <option value="" disabled selected>Pilih User</option>
                        @foreach($users as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                     </select>
                  </div>

                  <div class="form-group">
                     <label for="exampleInputPassword1">Description</label>
                     <textarea class="form-control" name="descriptionview" id="descriptionview" placeholder="Keterangan" rows="3"></textarea> 
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
                          <input type="hidden" id="location" name="location" value="">
                          <input type="hidden" id="good" name="good" value="">
                          <input type="hidden" id="amount" name="amount" value="">
                          <input type="hidden" id="user" name="user" value="">
                          <input type="hidden" id="description" name="description" value="">
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

     $('#locationview').select2({
         placeholder: "Pilih location",
         ajax: {
             url: '/find/locations',
             dataType: 'json'
         }
     });

     $('#goodview').select2({
         placeholder: "Pilih Barang",
         ajax: {
             url: function (params) {
            
                 return '/find/goods/' + $('#locationview').val();
             },
             dataType: 'json'
         }
     });

    $('#userview').select2();



   $('#FormAllomentEntry').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormAllomentEntry');
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
                         var location = $("#locationview").val();
                         var good = $("#goodview").val();
                         var amount = $("#amountview").val();
                         var user = $("#userview").val();
                         var description = $("descriptionview").val();
                          $('#FormAllotment #location').val(location);
                          $('#FormAllotment #good').val(good);
                          $('#FormAllotment #amount').val(amount);
                          $('#FormAllotment #user').val(user);
                          $('#FormAllotment #description').val(description);
                   
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


     $('#locationview').change(function (event) {
         $('#goodview').empty();
     });
   
 });
</script>
@endsection