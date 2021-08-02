@extends('layouts.app')
@section('content')
<section class="content">
   <div class="card">

        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card-header bg-transparent mb-0">
                    <h5 class="text-center">
                      <span class="font-weight-bold text-primary">
                        Scan
                      </span> 
                    </h5>
                </div>
                <div class="card-body">
                    <video id="preview" width="100%" height="500"></video>
                     <form class="" action="{{Route('result')}}">
                      <div class="d-flex justify-content-center">
                         <div class="p-2 bd-highlight">
                    <div class="form-group">
                        <input type="text" class="form-control" name="q" id=q>
                    </div>
                  </div>
                   <div class="p-2 bd-highlight">
                    <div class="form-group">
                          <button type="submit" class="btn btn-login btn-outline-success">Cari</button>
                      </div>
                    </div>
                      </div>

                  </form>  
                 </div> 
            </div>
        </div>
     </div>
   </section>
@endsection
@section('script')
  <script   type="text/javascript" src="{{asset('js/instascan.min.js')}}"></script> 
   <script>

      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
      scanner.addListener('scan', function (content) {
        $('#qrcode').val(content);
      });
      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        } else {
          console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });
  
   </script>

@endsection