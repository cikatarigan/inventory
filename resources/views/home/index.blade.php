@extends('layouts.app')
@section('content')

<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
         </div>
         <!-- /.col -->
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item active">Dashboard</li>
            </ol>
         </div>
         <!-- /.col -->
      </div>
      <!-- /.row -->
   </div>
   <!-- /.container-fluid -->
</div>
@role('admin')
<section class="content">
<div class="row">
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Goods</span>
        <span class="info-box-number">
          {{$goods}}
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>


  <!-- fix for small devices only -->
  <div class="clearfix hidden-md-up"></div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box mb-3">
      <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Samples</span>
        <span class="info-box-number">{{$sample}}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box mb-3">
      <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Member</span>
        <span class="info-box-number">{{$users}}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>

<div class="row">
 <div class="col-md-8">
    <div class="card">
      <div class="card-header border-transparent">
        <h3 class="card-title">Expired Goods</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table m-0">
            <thead>
            <tr>
              <th>Nama Barang</th>
              <th>Lokasi</th>
              <th>Spesifik Lokasi</th>
              <th>Date Expired</th>
              <th>Buang Barang</th>
            </tr>
            </thead>
            <tbody>
          @if ($expired->isNotEmpty())
           @foreach($expired as $item)
            <tr>
              <td>{{$item->good->name}}</td>
              <td>{{$item->location_shelf->location->name}}</td>
              <td>{{$item->location_shelf->name_shelf}}</td>
              <td>{{date('d-m-Y', strtotime($item->date_expired))}}</td>
              <td> <a href="#"  data-id="{{$item->id}}" data-name="{{$item->good->name}}" class="btnDelete text-right btn btn-danger"><i class="fas fa-trash"></i> Buang</a></td>
            </tr>
             @endforeach
             @else
             <tr>
              <td colspan="5">Tidak Ada Data</td>
             </tr>
             @endif
            </tbody>
          </table>
        </div>
        <!-- /.table-responsive -->
      </div>
    </div>
 </div>
 <div class="col-md-4">
  <div class="card">
      <div class="card-header">
        <h3 class="card-title">Barang Di Pinjam</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-0">
        <ul class="products-list product-list-in-card pl-2 pr-2">
          @foreach($borrow as $item)
          <li class="item">
            <div class="product-img">
              @foreach($item->good->good_images as $key => $get)
              @if ($loop->first)
              <img src="{{Storage::url($get->image->path)}}" alt="Product Image" class="img-size-50">
              @endif
              @endforeach
            </div>
            <div class="product-info">
              <a href="javascript:void(0)" class="product-title">{{$item->good->name}}
                <span class="badge badge-warning float-right">{{$item->amount}}</span></a>
              <span class="product-description">
                {{$item->description}}
              </span>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
      <!-- /.card-body -->
      <div class="card-footer text-center">
        <a href="{{route('borrow.index')}}" class="uppercase">View All Borrow</a>
      </div>
      <!-- /.card-footer -->
    </div>
 </div>
</div>
</section>
@else
<section class="content">
  <div class="row">
   <div class="col-md-8">
    <div class="card">
      <div class="card-header border-transparent">
        <h3 class="card-title">Barang Di berikan</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table m-0">
            <thead>
            <tr>
              <th>Nama Barang</th>
              <th>Lokasi</th>
               <th>gambar</th>
              <th>Jumlah</th>
              <th>Kapan Di berikan</th>
            </tr>
            </thead>
            <tbody>
            <tr>
             @foreach($allotment as $item)
              <td>{{$item->good->name}}</td>
              <td>{{$item->location->name}}</td>
              @foreach($item->good->good_images as $key => $get)
              @if ($loop->first)
            <td>   <img src="{{Storage::url($get->image->path)}}" alt="Product Image" class="img-size-50"></td>
              @endif
              @endforeach
              <td>{{$item->amount}}</td>
              <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
              @endforeach
            </tr>

            </tbody>
          </table>
        </div>
        <!-- /.table-responsive -->
      </div>
      <!-- /.card-body -->
    <!--   <div class="card-footer clearfix">
        <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
        <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
      </div> -->
      <!-- /.card-footer -->
    </div>
 </div>
 <div class="col-md-4">
  <div class="card">
      <div class="card-header">
        <h3 class="card-title">Barang Di Pinjam</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-0">
        <ul class="products-list product-list-in-card pl-2 pr-2">
          @foreach($borrow_user as $item)
          <li class="item">
            <div class="product-img">
              @foreach($item->good->good_images as $key => $get)
              @if ($loop->first)
              <img src="{{Storage::url($get->image->path)}}" alt="Product Image" class="img-size-50">
              @endif
              @endforeach
            </div>
            <div class="product-info">
              <a href="javascript:void(0)" class="product-title">{{$item->good->name}}
                <span class="badge badge-warning float-right">Amount :{{$item->amount}}</span></a>
              <span class="product-description">
                {{$item->description}}
              </span>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
      <!-- /.card-body -->
      <div class="card-footer text-center">
        <a href="{{route('borrow.index')}}" class="uppercase">View All Borrow</a>
      </div>
      <!-- /.card-footer -->
    </div>
 </div>
</div>
  </section>


@endrole


<div class="modal" tabindex="-1" role="dialog" id="ExpiredModal">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="#" method="post" id="FormExpired">
        <input type="hidden" id="id_delete" name="id" value="">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="del-success"></p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ya</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
jQuery(document).ready(function($) { 

    $('.btnDelete').click(function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var locationshelf = $(this).data('location-shelf');
            var location  = $(this).data('location');
           $('#FormExpired .modal-title').text("Konfirmasi");
           $('#FormExpired .help-block').empty();
           $('#FormExpired')[0].reset();
           $('#FormExpired #del-success').html("Apakah Anda yakin ingin menghapus <b>"+name+"</b> ini ?");
             url= 'expired/' + id;
             console.log(url);
        $('#ExpiredModal').modal('show');
    });



    $('#FormExpired').submit(function (event) {
         event.preventDefault();
         var $this = $(this);
         var form = $('#FormExpired');
         $('#FormExpired div.form-group').removeClass('has-error');
         $('#FormExpired .help-block').empty();
         
         var data = form.serialize();
         $.ajax({
             url: url,
             type: 'POST',
             data: data,
             cache: false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                     $('#ExpiredModal').modal('hide');
                     $("#FormExpired")[0].reset();
                       location.reload();
                 } else {
                     toastr.error(data.message);
                 }
             },
         })
     });


 });

</script>
@endsection
