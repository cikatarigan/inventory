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
                     <select class="js-example-basic-single form-control select-custom" id="good" name="good" width="100%">
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
                        <option value="" disabled selected>Pilih Lokasi</option>
                        @foreach($location as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Name Shelf</label>
                     <select class="js-example-basic-single form-control select-custom" id="nameshelf" name="nameshelf" width="100%">
                        <option value="" disabled selected>Pilih Rak</option>
                        @foreach($nameshelf as $item)
                        <option value="{{$item->name_shelf}}">{{$item->name_shelf}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Keterangan</label>
                     <textarea  class="form-control" id="description" name="description" rows="3"></textarea>
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
                          <input type="hidden" id="locationview" name="locationview" value="">
                          <input type="hidden" id="goodview" name="goodview" value="">
                          <input type="hidden" id="amountview" name="amountview" value="">
                          <input type="hidden" id="userview" name="userview" value="">
                          <input type="hidden" id="descriptionview" name="descriptionview" value="">
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

     $('#good').select2({
         placeholder: "Pilih Barang",
         ajax: {
             url: function (params) {
            
                 return '/find/borrows/' + $('#user').val();
             },
             dataType: 'json'
         }
     });

    $("#nameshelf").select2({
       placeholder: "Pilih rak",
       tags: true,
    });

    
    $('#good').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        if(data.isexpired == 'on'){
          $('#formexpired').show();
        }else{
          $('#formexpired').hide();
        }
    });
    
    $('#date_expired').datepicker({
       maxDate: '0'
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
                     var good = $("#good").val();
                     var amount = $("#amount").val();
                     var user = $("#user").val();
                     var description = $("description").val();
                      $('#FormReturn #locationview').val(location);
                      $('#FormReturn #goodview').val(good);
                      $('#FormReturn #amountview').val(amount);
                      $('#FormReturn #userview').val(user);
                      $('#FormReturn #descriptionview').val(description);
                   
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
         $('#good').empty();
     });

 });
</script>
@endsection