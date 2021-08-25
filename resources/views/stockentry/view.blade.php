<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id="qrcode"></div>
</body>
</html>
@section('script')
<script>
jQuery(document).ready(function($) { 
 
	     $('#qrcode').qrcode({
             text: 'test',
          });
 });	     
</script>
@endsection