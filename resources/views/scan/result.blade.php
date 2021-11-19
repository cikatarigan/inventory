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
               <td> @if($data->date_expired == null)Barang tidak expired @else {{$data->date_expired}}@endif</td>
            </tr>
            <tr>
               <td><b>Amount</b> </td>
               <td>:</td>
               <td>{{$amount}}</td>
            </tr>
            <tr>
               <td><b>Lokasi</b> </td>
               <td>:</td>
               <td>{{$data->location_shelf->location->name}}</td>
            </tr>
            <tr>
               <td><b>Detail Lokasi</b> </td>
               <td>:</td>
               <td>{{$data->location_shelf->name_shelf}}</td>
            </tr>
            <tr>
                <td><b>Status</b> </td>
                <td>:</td>
                <td>@if($data->status == 'Expired')Expired @elseif($data->status == 'Still Use' || $data->status == 'No Expired')Barang Ready @elseif($data->status == 'Out Of Stock')Barang Habis @endif</td>
             </tr>
         </table>
         @if($data->status != 'Expired')
         <form id="FormCheck"  method="POST">
            <table class="table table-striped">
                <input type="hidden" id="entry_id" name="entry_id" value="{{$data->id}}">
                <input type="hidden" id="goods_id" name="goods_id" value="{{$data->good->id}}">
                <input type="hidden" id="location_shelf_id" name="location_shelf_id" value="{{$data->location_shelf_id}}">
                <input type="hidden" id="location" name="location" value="{{$data->location_shelf->location->id}}">
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
               <tr id="defaultUser">
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
             <tr id="BorrowUser" style="display: none;">
                <td><b>Kepada User</b> </td>
                <td>:</td>
                <td>
                   <div class="form-group">
                      <select class="js-example-basic-single form-control select-custom" id="user_return" name="user_return" width="100%">
                         <option value="" disabled selected>Pilih User</option>
                      </select>
                   </div>
                </td>
             </tr>
               <tr id="formborrow" style="display: none;">
                <td> <b>ID Peminjam</b></td>
                <td>:</td>
                <td>
                   <div class="form-group" >
                      <select class="form-control"  id="borrow_id" name="borrow_id">
                        <option value="" disabled selected>Pilih User</option>
                        {{-- <option value="{{$item->id}}">{{$item->id}} |{{$item->created_at->format('d/m/Y')}}</option> --}}

                      </select>
                   </div>
                </td>
             </tr>
               <tr>
                  <td><b>Jumlah barang</b> </td>
                  <td>:</td>
                <td><div class="form-group"><input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah" autocomplete="off" required></div></td>
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
         @endif
      </div>
   </div>
</div>
<div class="card">
    <div class="card-header">
        <h6>Pinjam Histori</h6>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Nama Peminjam</th>
                <th scope="col">Jumlah Pinjam</th>
                <th scope="col">Sudah dikembalikan</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
                @if(!$data->borrow_item->isEmpty())
                @foreach ( $data->borrow_item as $item )
            <tr>
                <td>{{$item->borrow->user->name}}</td>
                <td>{{$item->borrow->amount}}</td>
                <td>{{$item->borrow->stock_back}}</td>
                <td>@if($item->borrow->status == 'Still Borrow')Masih Di pinjam @else Sudah Di kembalikan @endif</td>
            </tr>
            <tr>
                @endforeach
                @else
                <td>Tidak ada Data</td>
                @endif
            </tbody>
        </table>
    </div>
</div>
 <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <form action="#" method="post" id ="FormAction">
            <div class="modal-header">
               <h5 class="modal-title">Konfirmasi password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="box box-info">
                  <div class="box-header">
                     <div class="box-body">
                        <input type="hidden" id="entryview" name="data_entryid" value="">
                          <input type="hidden" id="locationview" name="data_location" value="">
                          <input type="hidden" id="goodview" name="data_goods" value="">
                          <input type="hidden" id="amountview" name="data_amount" value="">
                          <input type="hidden" id="userview" name="data_user" value="">
                          <input type="hidden" id="descriptionview" name="data_description" value="">
                          <input type="hidden" id="locationshelfview" name="data_location_shelf_id" value="">
                          <input type="hidden" id="logview" name="data_log" value="">
                          <input type="hidden" id="userreturnview" name="data_user_return" value="">
                          <input type="hidden" id="borrowidview" name="data_borrow_id" value="">
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

      $("#user").select2();

      $('#user_return').select2({
         placeholder: "Pilih User",
         ajax: {
             url: '/find/users',
             dataType: 'json'
         }
     });

     $('#borrow_id').select2({
         placeholder: "Pilih ID Peminjam",
         ajax: {
             url: function (params) {

                 return '/find/id-borrows/' + $('#user_return').val() + '/' + {{$data->good->id}};
             },
             dataType: 'json'
         }
     });

     $('#user_return').change(function (event) {
         $('#borrow_id').empty();
     });


      $('#log').on('change', function () {
        if(this.value == '3'){
            $('#BorrowUser').show();
            $('#defaultUser').hide();
            $('#user').val('');
            $('#FormCheck div.form-group').removeClass('has-error');
            $('#FormCheck .help-block').empty();

        }else{
            $('#BorrowUser').hide();
            $('#BorrowUser').val('');
            $('#defaultUser').show();
            $('#formborrow').hide();
            $('#formborrow').val('');
            $('#FormCheck div.form-group').removeClass('has-error');
             $('#FormCheck .help-block').empty();

        }
      });

         $('#user_return').on('change', function () {
            $('#formborrow').show();
        });


       $('#FormCheck').submit(function (event) {
         event.preventDefault();
         var form = $('#FormCheck');
         var data = form.serialize();
         $('#FormCheck div.form-group').removeClass('has-error');
         $('#FormCheck .help-block').empty();
         $.ajax({
             url: '/scan/check',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                    $('#exampleModal').modal('show');
                    var  entry_id = $("#entry_id").val();
                     var location = $("#location").val();
                     var location_shelf_id = $("#location_shelf_id").val();
                     var good = $("#goods_id").val();
                     var amount = $("#amount").val();
                     var user = $("#user").val();
                     var log = $("#log").val();
                     var description = $("#description").val();
                     var user_return = $("#user_return").val();
                     var borrow_id  = $("#borrow_id").val();
                      $('#FormAction #entryview').val(entry_id);
                      $('#FormAction #locationview').val(location);
                      $('#FormAction #locationshelfview').val(location_shelf_id);
                      $('#FormAction #goodview').val(good);
                      $('#FormAction #amountview').val(amount);
                      $('#FormAction #userview').val(user);
                      $('#FormAction #logview').val(log);
                      $('#FormAction #descriptionview').val(description);
                      $('#FormAction #userreturnview').val(user_return);
                      $('#FormAction #borrowidview').val(borrow_id);
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

        $('#FormAction').submit(function (event) {
         event.preventDefault();
         var form = $('#FormAction');
         var data = form.serialize();
         $.ajax({
             url: 'action',
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                        location.reload();
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
