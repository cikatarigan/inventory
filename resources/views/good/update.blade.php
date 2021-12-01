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
                     <div class="row" id="imageUpload">
                        @foreach($good->good_images as $key => $item)
                        <div class="col-md-4">
                           <img class="image-preview" data-id="image-{{$key}}" src="{{Storage::url($item->image->path)}}">
                           <div class="text-center">
                              <button class="btn btn-warning deleteBtn m-2" data-id={{$item->id}} >hapus</button>
                           </div>
                        </div>
                        @endforeach
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Title</label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama Barang" value="{{$good->name}}">
               </div>
               <div class="form-group">
                  <label>Brand</label>
                  <select class="js-example-basic-single form-control select-custom" id="brand" name="brand" width="100%">
                     <option value="" disabled selected>Pilih atau buat Brand</option>
                     @foreach($brand as $item)
                     <option value="{{$item->brand}}" {{$item->brand == $good->brand  ? 'selected' : ''}}>{{$item->brand}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label>Category</label>
                  <select class="js-example-basic-single form-control select-custom" id="category" name="category" width="100%">
                     <option value="" disabled selected>Pilih atau buat Category</option>
                     @foreach($category as $item)
                     <option value="{{$item->category}}" {{$item->category == $good->category  ? 'selected' : ''}}>{{$item->category}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label>Unit</label>
                  <select name="unit" class="form-control select2" style="width: 100%;" required="">
                  @foreach($unit as $item)
                     <option value="{{$item->unit}}" {{$item->unit == $good->unit  ? 'selected' : ''}}>{{$item->unit}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label>Desciption</label>
                  <textarea id="description" name="description" row="3" class="form-control"><?php echo($good->description); ?></textarea>
               </div>

               <div class="form-group">
                <div class="custom-control custom-switch">
                   <input type="checkbox" class="custom-control-input" id="isexpired" name="isexpired" {{$good->isexpired == 'on'  ? 'checked' : ''}}>
                   <label class="custom-control-label" for="isexpired">Apakah Ada Date Expired ?</label>
                </div>
             </div>
               <button type="submit" id="submit-all" class="btn btn-primary btn-block text-capitalize" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">Save</button>
            </form>
         </div>
      </div>
   </div>
</section>
@endsection
@section('script')
<script>
   jQuery(document).ready(function() {
      var image_id = 0;
      var data = [];

       $(document).on('click', '#btnAddImage', function (event) {
               event.preventDefault();
               var input = $('<input type="file" name="images[]" class="images-item" data-id="image-'+ image_id+'" style="display:none;" accept="image/x-png,image/jpeg"/>');
               $('#FormGood').append(input);
               input.click();
               image_id++;
           });

       $(document).on('change', '#FormGood input.images-item', function(event) {
         event.preventDefault();
         var preview = $('<img class="image-preview" data-id="'+ $(this).data('id') +'"/>');
         readURL(this, preview)
         $('#imageUpload').append($('<div class="col-md-4"></div>').append(preview).append('<div style="text-align:center;"><button class="btn btn-warning deleteBtn m-2 text-capitalize">hapus</button></div>').append(this));
       });

        $('#FormGood').submit(function (event) {
           event.preventDefault();

           var data = new FormData($(this)[0]);
           $.ajax({
               url: '/good/update/{{$good->id}}',
               type: 'POST',
               data: data,
               cache: false,
               processData: false,
               contentType: false,
               success: function (response) {
                 console.log(data);
                   if (response.success) {
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
                      parent.append('<span class="help-block">'+ error +'</span>');
                    })
                  }
                }
           });
       });



        $(document).on('click', '.deleteBtn', function(event){
             event.preventDefault();
             $('#FormGood').append('<input type="hidden" name="deleted_image[]" value="'+$(this).data('id')+'">');
             $(this).parent().parent().remove();

        });

       function readURL(input, image) {
         if (input.files && input.files[0]) {
             var reader = new FileReader();
             reader.onload = function (e) {
                 image.attr('src', e.target.result)
             };
             reader.readAsDataURL(input.files[0]);
         }
       };
   });
</script>
@endsection
