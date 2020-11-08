<div class="page-footer">
    <div class="container-fluid">
        <a href="javascript:">
            2020  &copy; Gracfful.
            <span class="pull-right">
</span>
        </a>
        {{-- <div class="pull-right">
            {!! Form::select("",session('allowed_hubs'), session('current_hub'),["class"=>"form-control hide  input-medium","onchange"=>"switchCurrentHub();","id"=>"hubswitcher"]) !!}
            
        </div> --}}
    </div>
</div>
<div class="scroll-to-top">
    <i class="icon-arrow-up">
    </i>
</div>
<script>
jQuery('document').ready(function(){
      $.loading({imgPath:'{{asset('public/js/img/ajax-loading.gif')}}'});
});
</script>
<style type="text/css">
  
</style>
{{-- <script src="{{ URL::asset('public/js/app.js') }}" type="text/javascript"></script> --}}