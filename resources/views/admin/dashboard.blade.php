@extends('layouts.ocean')
@section('title', 'Tổng quan quản trị')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KHÔNG GIAN QUẢN TRỊ</span><h1>Nuôi dưỡng một đại dương tri thức.</h1><p>Chuẩn bị nội dung chất lượng cho những chuyến khám phá tiếp theo.</p></div></div>
<div class="stats"><div class="stat"><span>Bài kiến thức</span><strong>{{ $articleCount }}</strong></div><div class="stat"><span>Bộ đã xuất bản</span><strong>{{ $setCount }}</strong></div><div class="stat"><span>Lượt hoàn thành</span><strong>{{ $attemptCount }}</strong></div></div>
<div class="panel"><h2>Luồng biên soạn</h2><p class="muted">Tạo hoặc lấy bài Wikipedia → Thêm 5 câu hoặc sinh bằng Gemini → Kiểm tra nội dung → Xuất bản.</p><div class="header-actions">@if(Route::has('admin.articles.create'))<a class="btn" href="{{ route('admin.articles.create') }}">Thêm bài viết ↗</a>@endif @if(Route::has('admin.question-sets.create'))<a class="btn btn-outline" href="{{ route('admin.question-sets.create') }}">Tạo bộ câu hỏi</a>@endif</div></div>
<div class="panel"><h2>Hoạt động Gemini gần đây</h2>@forelse($logs as $log)<p><span class="badge {{ $log->status }}">{{ $log->status === 'success' ? 'Thành công' : 'Thất bại' }}</span> {{ $log->article?->title ?: 'Bài đã xóa' }} <small class="muted">· {{ $log->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m H:i') }}</small></p>@empty<p class="muted">Chưa có lần sinh câu hỏi nào.</p>@endforelse</div>
@endsection
