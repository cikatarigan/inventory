@extends('layouts.app')
@section('content')
<section class="content">

   <div class="card">
      <div class="wrapper row my-5 mx-2">
         <div class="preview col-md-6">
            <div class="preview-pic tab-content">
 
            </div>
            <ul class="preview-thumbnail nav nav-tabs">
 
            </ul>
            {{$data}}
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
                        <td> {{$data->good->isexpired}}</td>
                     </tr>  
                
                  </table>

                
         </div>
      </div>
   </div>

@endsection