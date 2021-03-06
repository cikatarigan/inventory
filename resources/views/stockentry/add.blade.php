@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Stock Entry Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item">Stock Entry</li>
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
               <h3 class="card-title">Form Stock Entry</h3>
            </div>
            <form role="form" id="FormStockEntry">
               <div class="card-body">
                  <div class="form-group">
                     <label for="exampleInputPassword1">Barang</label>
                     <select class="js-example-basic-single form-control select-custom" id="good_id" name="good_id" width="100%">
                      <option value="" disabled selected>Pilih Barang</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Jumlah Barang</label>
                     <input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah" autocomplete="off">
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Location</label>
                     <select class="js-example-basic-single form-control select-custom" id="location_id" name="location_id" width="100%">

                     </select>
                  </div>
                  <div class="form-group">
                     <label for="exampleInputPassword1">Name Shelf</label>
                     <select class="js-example-basic-single form-control select-custom" id="location_shelf" name="location_shelf" width="100%">
                        <option value="" disabled selected>Pilih Rak</option>
                     </select>
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
@endsection
@section('script')
<script>


 jQuery(document).ready(function() {

    $("#good_id").select2({
       data: {!!json_encode($good)!!},
       placeholder: "Pilih Barang",
       tags: true,
    });

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

    $('#good_id').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        if(data.isexpired == 'on'){
          $('#formexpired').show();
        }else{
          $('#formexpired').hide();
        }
    });


    $('#FormStockEntry').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormStockEntry');
          $('#FormStockEntry div.form-group').removeClass('has-error');
         $('#FormStockEntry .help-block').empty();
         var data = form.serialize();
         $.ajax({
             url: '/receipt/add',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success('Data Berhasil ditambahkan');
                     window.location.replace('/')
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
     });

 });
</script>
@endsection
