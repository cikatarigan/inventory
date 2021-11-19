<!DOCTYPE html>
<html>
<head>
	<title>Qrcode Print</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
        }
        @media print {
            #btn-print {
                display: none;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <div class="row mt-4">
        @for ($i = 0; $i < $loop; $i++)
        <div class="col-6">
            <div class="row justify-content-md-center my-2">
                <div class="col-3 text-right">
                    {!! QrCode::generate($data->qrcode) !!}
                </div>
                <div class="col-8 " style="display: inline-grid;align-items: center">
                    <p class="m-0">Nama Barang :<b class="text-capitalize"> {{$data->good->name}}</b></p>
                    @if($data->date_expired != Null)
                        Expired : {{$data->date_expired}}
                    @else
                        Expired : Tidak Barang Kadaluarsa
                    @endIf
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
<button id="btn-print" style="position: fixed; bottom: 5px; right: 5px; " >Print</button>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function($) {
        $( "#btn-print" ).click(function() {
            window.print();
        });
    });
</script>
</body>
</html>

