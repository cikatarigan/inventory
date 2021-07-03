@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Good Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Good</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<section class="content">
   <div class="card">
   <div class="card-body">
      <div class="box">
         <div class="box-header with-border">
            <form method="post" enctype="multipart/form-data" id ="FormGood">
               <div class="card card-info" id="uploadImage">
                  <div class="card-header">Images</div>
                  <div class="panel-body">
                     <div class="m-3">
                        <a href="#" class="btn btn-sm btn-primary" id="btnAddImage"><i class="fa fa-plus"></i> Add Image</a>
                     </div>
                     <div class="row" id="imageUpload"></div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Nama Barang</label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama Barang" required>
               </div>
               <div class="form-group">
                  <label>Brand</label>
                  <select class="js-example-basic-single form-control select-custom" id="brand" name="brand" width="100%">
                     <option value="" disabled selected>Pilih atau buat Brand</option>
                     @foreach($brand as $item)
                     <option value="{{$item->brand}}">{{$item->brand}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label>Category</label>
                  <select class="js-example-basic-single form-control select-custom" id="category" name="category" width="100%">
                     <option value="" disabled selected>Pilih atau buat Brand</option>
                     @foreach($category as $item)
                     <option value="{{$item->category}}">{{$item->category}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label>Unit</label>
                  <select name="unit" class="form-control select2" style="width: 100%;" required="">
                     <option disabled="disabled" selected="selected">Please choose a Status ...</option>
                     <option value="pcs">PCS</option>
                     <option value="kg">KG</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>Keterangan</label>
                  <textarea id="description" name="description" class="form-control" row="3" required placeholder="Masukkan Keterangan Barang"></textarea>
               </div>
               <div class="form-group">
                  <div class="custom-control custom-switch">
                     <input type="checkbox" class="custom-control-input" id="isexpired" name="isexpired">
                     <label class="custom-control-label" for="isexpired">Apakah Ada Date Expired ?</label>
                  </div>
               </div>
               <button type="submit" id="submit-all" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">Save</button>
            </form>
         </div>
      </div>
   </div>
</section>
@endsection
@section('script')
<script>
   jQuery(document).ready(function() {
   
      $("#brand").select2({
         placeholder: "Pilih atau buat Brand",
         tags: true
      });
      
      $("#category").select2({
         placeholder: "Pilih atau buat Category",
         tags: true
      });
   
          var image_id = 0;
          var data = [];
   
           $(document).on('click', '#btnAddImage', function (event) {
               event.preventDefault();
               var input = $('<input type="file" name="images[]" class="images-item" data-id="image-'+ image_id+'" style="display:none;" accept="image/x-png,image/gif,image/jpeg"/>');
               $('#FormGood').append(input);
               input.click();
               image_id++;
           });
   
           $(document).on('change', '#FormGood input.images-item', function(event) {
             event.preventDefault();
             var preview = $('<img class="image-preview" data-id="'+ $(this).data('id') +'"/>');
             readURL(this, preview)
             $('#imageUpload').append($('<div class="col-md-4 m-2" style="border: 2px solid #75979c;"></div>').append(preview).append('<div class="text-center p-2"><button class="btn deleteBtn btn-warning">hapus</button></div>').append(this));
           });
           
           $(document).on('click', '.deleteBtn', function(event){
             event.preventDefault();
             $(this).parent().parent().remove();
           
           });
   
           $('#FormGood').submit(function (event) {
               event.preventDefault();
               $('#error').hide();
               var _button = $('#FormGood button[type=submit]');
               var data = new FormData($(this)[0]);
               var form = $('#FormGood');
               $.ajax({
                   url: '/good/add',
                   type: 'POST',
                   data: data,
                   cache: false,
                   processData: false, 
                   contentType: false,
                   success: function (response) {
                     console.log(data);
                       if (response.success) {
                           $('#FormGood')[0].reset();
                           toastr.success(response.message);
                           setTimeout(function () { 
                              location.replace('/good');
                           }, 1000);
                       }
                       else{
                           toastr.error(response.message);
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
               });
           });
           
           $( '.btnDiscard' ).on('click', function () {
               $('#inputImage').val('');
               $('#image-preview').attr('src', '')
               $('#addImageModal').modal('hide');
           });
   
           function readURL(input, image) {
       	    if (input.files && input.files[0]) {
       	        var reader = new FileReader();
       	        reader.onload = function (e) {
       	            image.attr('src', e.target.result)
       	        };
       	        reader.readAsDataURL(input.files[0]);
       	    }
       	}
       });
   
</script>
@endsection