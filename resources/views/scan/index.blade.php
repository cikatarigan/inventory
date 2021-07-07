@extends('layouts.app')
@section('content')
     <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card-header bg-transparent mb-0">
                    <h5 class="text-center">
                      <span class="font-weight-bold text-primary">
                        Scan
                      </span> 
                    </h5>
                </div>
                <div class="card-body">
                    <video id="preview" width="300" height="300"></video>
                    <div class="form-group">
                        <input type="text" class="form-control" name="qrcode" id=qrcode>
                    </div>
                 </div> 
            </div>
        </div>
     </div>
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