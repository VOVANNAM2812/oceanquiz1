@extends('layouts.ocean')
@section('title', 'Tài khoản')
@section('content')
<div class="auth-card"><span class="eyebrow">BẮT ĐẦU CHUYẾN KHÁM PHÁ</span><h1>Chào bạn đến với biển xanh.</h1><p class="muted" style="margin-bottom:25px">Một đại dương kiến thức đang chờ bạn.</p>{{ $slot }}@if(request()->routeIs('login'))<p class="help" style="margin-top:22px">Chưa có tài khoản? <a class="text-link" href="{{ route('register') }}">Đăng ký khám phá</a></p>@endif</div>
@endsection
