@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Borrow Entry Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Borrow</li>
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
            <form role="form" id="FormBorrowCheck">
               <div class="card-body">
               <div class="form-group">
                <label for="exampleInputPassword1">Lokasi Barang</label>
                  <select name="location" id="location" class="form-control select-custom ">
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
                     <input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah">
                  </div>

                  <div class="form-group">
                     <label for="exampleInputPassword1">Kepada</label>
                     <select class="js-example-basic-single form-control select-custom" id="user" name="user" width="100%">
                        <option value="" disabled selected>Pilih User</option>
                        @foreach($users as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                     </select>
                  </div>
                 <div class="form-group">
                     <label for="exampleInputPassword1">Description</label>
                     <textarea class="form-control" id="description" name="description" placeholder="Keterangan" rows="3"></textarea> 
                  </div>
               </div>
               <!-- /.card-body -->
               <div class="card-footer">
                  <button type="submit" class="btn btn-block btn-primary">Submit</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormBorrow">
            <div class="modal-header">
               <h5 class="modal-title">Konfirmasi password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="box box-info">
                  <div class="box-header">
                     <div class="box-body">
                          <input type="hidden" id="locationcheck" name="data_location" value="">
                           <input type="hidden" id="shelfCheck" name="data_shelf" value="">
                          <input type="hidden" id="goodcheck" name="data_goods" value="">
                          <input type="hidden" id="amountcheck" name="data_amount" value="">
                          <input type="hidden" id="usercheck" name="data_user" value="">
                          <input type="hidden" id="descriptioncheck" name="data_description" value="">
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


     $('#location').select2({
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
                 return '/find/shelf/' + $('#location').val();
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

  
    $('#FormBorrowCheck').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormBorrowCheck');
          $('#FormBorrowCheck div.form-group').removeClass('has-error');
         $('#FormBorrowCheck .help-block').empty();
         var data = form.serialize();
         $.ajax({
             url: '/borrow/check',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                        $('#exampleModal').modal('show');
                         var location = $("#location").val();
                         var shelf = $("#location_shelf").val();
                         var good = $("#goods").val();
                         var amount = $("#amount").val();
                         var user = $("#user").val();
                         var description = $("#description").val();
                          $('#FormBorrow #locationcheck').val(location);
                          $('#FormBorrow #shelfCheck').val(shelf);
                          $('#FormBorrow #goodcheck').val(good);
                          $('#FormBorrow #amountcheck').val(amount);
                          $('#FormBorrow #usercheck').val(user);
                          $('#FormBorrow #descriptioncheck').val(description);
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


    $('#FormBorrow').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormBorrow');
         var data = form.serialize();
         $.ajax({
             url: '/borrow/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                     window.setTimeout(function() {
                        window.location.href = '/borrow';
                    }, 3000);
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

     $('#location').change(function (event) {
         $('#location_shelf').empty();
         $('#goods').empty();
     });

     $('#location_shelf').change(function (event) {
      $('#goods').empty();
     });
 });
</script>
@endsection