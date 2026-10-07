@if ($errors->any())
        <div class="alert alert-danger alert-premium alert-autoClose">
        @foreach ($errors->all() as $error)
      <span>{{ $error }}</span>
        @endforeach
        </div>
      @endif

@if(session()->has('message'))
    <div class="alert alert-success alert-premium alert-autoClose">
         {{ session()->get('message') }}
    </div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-success alert-premium alert-block alert-autoClose">
    <strong>{{ $message }}</strong>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-premium alert-block alert-autoClose">
    <strong>{{ $message }}</strong>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-premium alert-block alert-autoClose">
    <strong>{{ $message }}</strong>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-premium alert-block alert-autoClose">
    <strong>{{ $message }}</strong>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
</div>
@endif

<script>

$(document).ready(function() {
    $(".alert-autoClose").fadeTo(2000, 500).slideUp(500, function() {
      $(".alert-autoClose").slideUp(500);
  });
});

</script>
