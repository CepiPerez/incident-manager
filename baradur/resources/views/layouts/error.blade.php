@extends('layouts.main')

@section('content')
<div class="container pt-5">
    <div class="text-center pb-2">
      <i class="ri-error-warning-fill text-danger text-center" style="font-size:64px;"></i>
    </div>
    <p class="text-center text-danger">Error {{$error_code}}</p>
    <p class="text-center text-dark">{{$error_message}}</p>
</div>
@endsection