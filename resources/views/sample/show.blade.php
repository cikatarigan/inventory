@extends('layouts.app')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1>Sample Detail</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Home</a></li>
               <li class="breadcrumb-item">Sample</li>
               <li class="breadcrumb-item active">Detail</li>
            </ol>
         </div>
      </div>
   </div>
</section>
<section class="content">
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-5">
                  <img src="{{Storage::url($sample->photo)}}" class=" img-fluid w-100 100"> 
               </div>
               <div class="col-md-7">
                  <h3>Kode : <b>{{$sample->code}} ( Lemari :{{$sample->cupboard}} )</b></h3>
                  <h3><b>{{$sample->title}}</b></h3>
                  <table class="table table-striped">
                     <tr>
                        <td> <b>Lokasi</b></td>
                        <td>:</td>
                        <td> {{$sample->location}}</td>
                     </tr>
                     <tr>
                        <td><b>Tanggal</b> </td>
                        <td>:</td>
                        <td> {{$sample->years}}</td>
                     </tr>
                     <tr>
                        <td><b> No Batch</b> </td>
                        <td>:</td>
                        <td> {{$sample->batch}}</td>
                     </tr>
                     <tr>
                        <td><b>Tahun Tanam</b> </td>
                        <td>:</td>
                        <td> {{$sample->planting_year}}</td>
                     </tr>
                     <tr>
                        <td><b>Divisi</b> </td>
                        <td>:</td>
                        <td> {{$sample->division}}</td>
                     </tr>
                     <tr>
                        <td><b>Blok</b> </td>
                        <td>:</td>
                        <td> {{$sample->block}}</td>
                     </tr>
                     <tr>
                        <td><b>Baris Ke</b> </td>
                        <td>:</td>
                        <td> {{$sample->row}}</td>
                     </tr>
                     <tr>
                        <td><b>Pohon Ke</b> </td>
                        <td>:</td>
                        <td> {{$sample->number_tree}}</td>
                     </tr>
                  </table>
                  <br>
                  <?php if (isset($sample->description))
                     {
                         echo'<label> Description</label>';
                     }
                     ?>            
                  <?php echo($sample->description) ?>
               </div>
            </div>
         </div>
      </div>
</section>
@endsection