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
            <form role="form" id="FormStockEntry">
               <div class="card-body">
               <div class="form-group">
                <label for="exampleInputPassword1">Lokasi Barang</label>
                  <select name="location" id="location" class="form-control select-custom ">
                     <option value="" disabled selected>Location</option>
                  </select>
               </div>
               <div class="form-group">
                <label for="exampleInputPassword1">Nama Barang</label>
                   <select name="good" id="good" class="form-control">
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
@endsection
@section('script')
<script>
 jQuery(document).ready(function() {

       $('#location').select2({
           placeholder: "Pilih location",
           ajax: {
               url: '/find/locations',
               dataType: 'json'
           }
       });


       $('#good').select2({
           placeholder: "Pilih Barang",
           ajax: {
               url: function (params) {
                console.log($('#location').val());
                   return '/find/goods/' + $('#location').val();
               },
               dataType: 'json'
           }
       });



     $('#user').select2();

  
    $('#FormStockEntry').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormStockEntry');
         var data = form.serialize();
         $.ajax({
             url: '/allotment/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success('Data Berhasil ditambahkan');
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