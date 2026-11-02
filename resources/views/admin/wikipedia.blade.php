@extends('layouts.ocean')
@section('title', 'Lấy bài Wikipedia')
@section('content')
<div class="page-heading"><div><span class="eyebrow">NGUỒN TRI THỨC MỞ</span><h1>Mang kiến thức từ Wikipedia.</h1><p>Tìm bài tiếng Việt, đọc trước và lưu vào thư viện để biên soạn câu hỏi.</p></div></div>
<form method="get" class="search-form panel" action="{{ route('admin.wikipedia.index') }}"><input class="field" type="text" name="search" placeholder="Ví dụ: cá voi xanh, rùa biển…" value="{{ request('search') }}" required maxlength="100"><button class="btn">Tìm Wikipedia ↗</button></form>
@if($apiError)<div class="notice error" role="alert">{{ $apiError }} <a class="text-link" href="{{ route('admin.articles.create') }}">Nhập bài thủ công</a></div>@endif
@if($preview)<div class="panel"><span class="eyebrow">XEM TRƯỚC NỘI DUNG</span><h2>{{ $preview['title'] }}</h2><div class="article-body" style="max-height:500px;overflow:auto">{{ $preview['content'] }}</div><p class="source">Nguồn: <a href="{{ $preview['source_url'] }}" target="_blank" rel="noopener noreferrer">Wikipedia tiếng Việt</a> · {{ $preview['license'] }}</p><form method="post" action="{{ route('admin.wikipedia.store') }}">@csrf<input type="hidden" name="page_id" value="{{ $preview['wiki_page_id'] }}"><button class="btn">Lưu bài vào thư viện ↗</button></form></div>@endif
@foreach($results as $result)<div class="panel"><h2>{{ $result['title'] }}</h2><p class="muted">{{ $result['snippet'] }}</p><a class="btn btn-small btn-outline" href="{{ route('admin.wikipedia.index', ['page_id' => $result['id']]) }}">Xem trước →</a></div>@endforeach
@if(request('search') && !$results && !$apiError)<div class="empty">Không có bài phù hợp. Hãy thử từ khóa khác.</div>@endif
@endsection
