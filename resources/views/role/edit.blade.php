@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Role Edit Table</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Role</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<div class="card card-success">
   <div class="card-header">
      <h3 class="card-title">Check Permission For Access</h3>
   </div>
   <div class="card-body">
      <form action="#" method="post" id ="FormPermission">
         <div class="row">
            <div class="col-sm-6">
               <div style="overflow-y: scroll;height: 800px;">
                  
                  <div class="table-responsive">
                  <table class="no-border table table-sm table-striped">
                    <tbody>
                      @foreach($permission as $item)
                      <tr>
                        <td style="width: 25px"><div class="form-group clearfix"><div class="form-check">
                        <input type="checkbox" class="form-check-input check-permissions" name="permissions[]" value="{{$item->name}}" {{(count($item->roles) == 1) ? "checked" : ""}}></div></div>
                        </td>
                        <td><label>{{$item->display_name}}</label><br><small>{{$item->name}}</small></td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
@endsection
@section('script')
<script>
  $(function () {
       $('input.check-permissions').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });  
  });  
  
  $(document).ready(function($){ 
   $('#FormPermission').submit(function(event) {
        event.preventDefault();
        var form =$('#FormPermission');
        var data = form.serialize();
        $.ajax({
            url: '/role/edit/{{$role->id}}',
            type: 'POST',
            data : data,
            cache : false,
            success: function(data){
               if (data.success){
                  toastr.success(data.message);
                  setTimeout(function () { 
                       location.replace('/role');
                       }, 1000);
               }
            },
        })
    });
   
   
   });
</script>
@endsection