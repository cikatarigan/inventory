@extends('layouts.app')
@section('content')
<section class="content">
   
      <div class="card">
         <div class ="card-header">
            <h4>Create Sample</h4>
         </div>
         <div class ="card-body">
            <form id="FormSample" action="#" method="POST`" enctype="multipart/form-data">
               <div class ="row">
                  <div class="col-4">
                     <div class="form-group">
                        <label>Lemari</label>
                        <select class="js-example-basic-single form-control select-custom" id="cupboard" name="cupboard" width="100%">
                        </select>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Kode</label>
                        <input type="text" class="form-control" id="code" name="code">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Tanggal</label>
                        <input type="text" class="form-control" id="years" name="years">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">No Batch</label>
                        <input type="text" class="form-control" id="batch" name="batch">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Tahun Tanam</label>
                        <input type="text" class="form-control" id="planting_year" name="planting_year">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Divisi</label>
                        <input type="text" class="form-control" id="division" name="division">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Blok</label>
                        <input type="text" class="form-control" id="block" name="block">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Baris Ke</label>
                        <input type="text" class="form-control" id="row" name="row">
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <label for="">Pohon Ke</label>
                        <input type="text" class="form-control" id="number_tree" name="number_tree">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label for="exampleInputPassword1">Title</label>
                  <input type="text" class="form-control" id="title" name="title">
               </div>
               <div class="form-group">
                  <label>Description </label>
                  <textarea id="description " name="description " class="form-control" required></textarea>
               </div>
               <div class="form-group">
                  <label>Lokasi</label>
                  <textarea id="location" name="location" class="form-control" rows="4" required></textarea>
               </div>
               <div class="form-group">
                  <label>Image</label>
                  <input type="file" id="photo" name="photo"  accept=".jpg,.gif,.png" />
               </div>
               <div class="form-group">
                  <button type="submit" class="btn btn-block btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
 /section>
@endsection
@section('script')
<script src="//cdn.ckeditor.com/4.11.1/full/ckeditor.js"></script>
<script>
   $(function () {
     CKEDITOR.replace('description ', {
         toolbarGroups: [
             { name: 'document',    groups: [ 'mode', 'document' ] },           
             { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },          
             '/',                                                               
             { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
             { name: 'links' },
             '/',
             { name: 'styles', groups: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
             { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
             { name: 'colors', groups: [ 'TextColor', 'BGColor' ] },
             { name: 'tools', groups: [ 'Maximize', 'ShowBlocks' ] },
             { name: 'insert'},
   
         ]
     });
   });
   jQuery(document).ready(function($) { 
   var cupboard = [
    {
        id: 'a',
        text: 'a'
    },
    {
        id: 'b',
        text: 'b'
    },
    {
        id: 'c',
        text: 'c'
    },
    {
        id: 'd',
        text: 'd'
    },
    {
        id: 'e',
        text: 'e'
    },
        {
        id: 'f',
        text: 'f'
    },
        {
        id: 'g',
        text: 'g'
    },
        {
        id: 'h',
        text: 'h'
    },
        {
        id: 'i',
        text: 'i'
    },
        {
        id: 'j',
        text: 'j'
    },
        {
        id: 'k',
        text: 'k'
    },
        {
        id: 'l',
        text: 'l'
    },
        {
        id: 'm',
        text: 'm'
    },
            {
        id: 'n',
        text: 'n'
    },
            {
        id: 'o',
        text: 'o'
    },
            {
        id: 'p',
        text: 'p'
    },
            {
        id: 'q',
        text: 'q'
    },
            {
        id: 'r',
        text: 'r'
    },
            {
        id: 's',
        text: 's'
    },
            {
        id: 't',
        text: 't'
    },
            {
        id: 'u',
        text: 'u'
    },
            {
        id: 'v',
        text: 'v'
    },
            {
        id: 'w',
        text: 'w'
    },
            {
        id: 'x',
        text: 'x'
    },
            {
        id: 'y',
        text: 'y'
    },
   
   {
        id: 'z',
        text: 'z'
    },
   
   
   ];

    $("#cupboard").select2({
       tags: true,
       data: cupboard
    });
   
  
   $(document).on('submit','#FormSample',function(event) {
        event.preventDefault();
        var form = $(this);
        var data = new FormData($('#FormSample')[0]);
        form.find('div.form-group').removeClass('has-error');
        form.find('.help-block').remove();
        
         $.ajax({
             url: '/sample/add',
            type: 'POST',
            data : data,
            cache : false,
            contentType : false,
            processData : false,
             success: function (data) {
                 console.log(data)
                 if (data.success) {
                     toastr.success(data.message);
                   
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