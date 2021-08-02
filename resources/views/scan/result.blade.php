@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Detail Scan History</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item ">Scan</li>
               <li class="breadcrumb-item active">Detail Good</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<section class="content">
<div class="card">
   <div class="wrapper row my-5 mx-2">
      <div class="preview col-md-6">
         <div class="preview-pic tab-content">
            @foreach($data->good->good_images as $key => $item)
            <div class="tab-pane @if($key== 0){{'active'}}@endif" id="pic-{{$item->id}}">
               <img src="{{Storage::url($item->image->path)}}"  style="height: 450px" />
            </div>
            @endforeach
         </div>
         <ul class="preview-thumbnail nav nav-tabs">
            @foreach($data->good->good_images as $key => $item)
            <li class="@if($key== 0){{'active'}} @endif">
               <a data-target="#pic-{{$item->id}}" data-toggle="tab">
               <img src="{{Storage::url($item->image->path)}}" style="height: 120px;" />
               </a>
            </li>
            @endforeach
         </ul>
         {{$amount}}
      </div>
      <div class="details col-md-6">
         <h3 class="product-description">{{$data->good->name}}</h3>
         <p class="product-description">{{$data->good->description}}</p>
         <table class="table table-striped">
            <tr>
               <td> <b>Brand</b></td>
               <td>:</td>
               <td> {{$data->good->brand}}</td>
            </tr>
            <tr>
               <td><b>Category</b> </td>
               <td>:</td>
               <td> {{$data->good->category}}</td>
            </tr>
            <tr>
               <td><b>Unit</b> </td>
               <td>:</td>
               <td> {{$data->good->unit}}</td>
            </tr>
            <tr>
               <td><b>Expired</b> </td>
               <td>:</td>
               <td> {{$data->date_expired}}</td>
            </tr>
         </table>
         {{$data->good->good_shelves}}
         <form id="FormCheck"  method="POST">
            <table class="table table-striped">
               <input type="hidden" id="good_id" name="id" value="{{$data->good->id}}">
               <input type="hidden" id="good_id" name="id" value="{{$data->good->good_shelves}}">
               <tr>
                  <td> <b>Log Data</b></td>
                  <td>:</td>
                  <td>
                     <div class="form-group">
                        <select class="form-control"  id="log" name="log">
                           <option value="" disabled selected>Pilih Log</option>
                           <option value="1">Peminjaman</option>
                           <option value="2">Pemberian</option>
                           <option value="3">Pengembalian</option>
                        </select>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td><b>Jumlah barang</b> </td>
                  <td>:</td>
                  <td> <input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah" autocomplete="off"></td>
               </tr>
               <tr>
                  <td><b>Kepada User</b> </td>
                  <td>:</td>
                  <td>
                     <div class="form-group">
                        <select class="js-example-basic-single form-control select-custom" id="user" name="user" width="100%">
                           <option value="" disabled selected>Pilih User</option>
                           @foreach($users as $item)
                           <option value="{{$item->id}}">{{$item->name}}</option>
                           @endforeach
                        </select>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td><b>Keterangan</b> </td>
                  <td>:</td>
                  <td>
                     <div class="form-group">
                        <textarea class="form-control" id="description" name="description" placeholder="Keterangan" rows="3"></textarea> 
                     </div>
                  </td>
               </tr>
               <tr>
                  <td colspan="3">
                     <div class="card-footer">
                        <button type="submit" class="btn btn-block btn-primary">Submit</button>
                     </div>
                  </td>
               </tr>
            </table>
         </form>
      </div>
   </div>
</div>

 <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormConfirm">
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
<script >
   jQuery(document).ready(function() {
        var url;
      $('#log').on('change', function() {
       var log = $("#log").val();
         if(log == '1'){
         url = '{{ route("borrow.check") }}';
        
         } else if(log == '2'){
         url = '{{ route("allotment.check") }}';
       
           } else if(log == '3')
           url = '{{ route("return.check") }}';

      });


       $('#FormCheck').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormCheck');
         var data = form.serialize();
         $.ajax({
             url: url,
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
                     console.log(description);
                      $('#FormCheck #locationview').val(location);
                      $('#FormCheck #goodview').val(good);
                      $('#FormCheck #amountview').val(amount);
                      $('#FormCheck #userview').val(user);
                      $('#FormCheck #descriptionview').val(description);
                   
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

  
     $("#FormAction").on("submit", function(event){
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormAction');
         var data = form.serialize();
         console.log(url);
         $.ajax({
             url: 'result/scan',
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

      });

</script>
@endsection