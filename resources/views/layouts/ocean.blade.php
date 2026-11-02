<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Khám phá sinh vật biển, đọc kiến thức và thử sức với OceanQuiz.">
    <title>@yield('title', 'Khám phá đại dương') · OceanQuiz</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@auth
<aside class="sidebar">
    <a class="brand" href="{{ route('home') }}"><span class="brand-icon">≈</span> OceanQuiz<span class="brand-dot">.</span></a>
    <span class="nav-label">GÓC KHÁM PHÁ</span>
    <nav aria-label="Điều hướng chính">
        <a class="{{ request()->routeIs('home', 'question-sets.read') ? 'active' : '' }}" href="{{ route('home') }}"><span>◈</span> Thư viện đại dương</a>
        @if(Route::has('history'))<a class="{{ request()->routeIs('history') ? 'active' : '' }}" href="{{ route('history') }}"><span>◷</span> Lịch sử học tập</a>@endif
        @if(Route::has('leaderboard'))<a class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}" href="{{ route('leaderboard') }}"><span>♜</span> Bảng xếp hạng</a>@endif
        @if(auth()->user()->isAdmin())
            <span class="nav-label">KHÔNG GIAN QUẢN TRỊ</span>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">▦ Tổng quan</a>
            @if(Route::has('admin.articles.index'))<a class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">▤ Bài kiến thức</a>@endif
            @if(Route::has('admin.question-sets.index'))<a class="{{ request()->routeIs('admin.question-sets.*') ? 'active' : '' }}" href="{{ route('admin.question-sets.index') }}">◇ Bộ câu hỏi</a>@endif
            @if(Route::has('admin.wikipedia.index'))<a class="{{ request()->routeIs('admin.wikipedia.*') ? 'active' : '' }}" href="{{ route('admin.wikipedia.index') }}">◎ Wikipedia</a>@endif
            @if(Route::has('admin.ai.index'))<a class="{{ request()->routeIs('admin.ai.*') ? 'active' : '' }}" href="{{ route('admin.ai.index') }}">✧ Gemini & nhật ký</a>@endif
        @endif
    </nav>
    <div class="sidebar-note"><span>ĐẠI DƯƠNG RỘNG LỚN.</span><p>Bắt đầu từ một<br>điều nhỏ bé.</p><div class="tiny-wave">∿ ∿ ∿</div></div>
    <div class="account"><span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isAdmin() ? 'Quản trị viên' : 'Nhà thám hiểm' }}</small></div></div>
    <form action="{{ route('logout') }}" method="post" class="logout">@csrf<button type="submit">Đăng xuất ↗</button></form>
</aside>
@endauth
<div class="{{ auth()->check() ? 'workspace' : 'guest-workspace' }}">
    <header class="topbar">
        @guest<a class="brand" href="{{ route('landing') }}"><span class="brand-icon">≈</span> OceanQuiz<span class="brand-dot">.</span></a>@else<span class="topbar-caption">MỘT CHÚT TÒ MÒ. MỘT ĐẠI DƯƠNG KIẾN THỨC.</span>@endguest
        <div class="topbar-right"><span class="live-dot"></span><span>Học điều mới mỗi ngày</span>@guest<a href="{{ route('login') }}" class="btn btn-small btn-outline">Đăng nhập ↗</a>@endguest</div>
    </header>
    <main id="main-content" class="main-content">
        @if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="notice error" role="alert"><strong>Vui lòng kiểm tra lại:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main>
    <footer class="footer"><span>≈ OceanQuiz · Cùng hiểu hơn về biển xanh.</span><span>Đọc. Khám phá. Ghi nhớ.</span></footer>
</div>
</body>
</html>
