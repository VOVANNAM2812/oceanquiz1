@extends('layouts.ocean')
@section('content')
@isset($header)<div class="page-heading">{{ $header }}</div>@endisset
{{ $slot }}
@endsection
