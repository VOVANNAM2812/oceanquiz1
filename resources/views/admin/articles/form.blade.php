@extends('layouts.ocean')
@section('title', $article->exists ? 'Sửa bài kiến thức' : 'Thêm bài kiến thức')
@section('content')
<div class="content-narrow"><div class="page-heading"><div><a class="eyebrow" href="{{ route('admin.articles.index') }}">← BÀI KIẾN THỨC</a><h1>{{ $article->exists ? 'Biên tập bài viết' : 'Thêm bài kiến thức' }}</h1></div></div>
@if($article->exists && $article->hasAttempts())<div class="notice error">Bài đã có lượt làm liên quan. Nội dung được giữ nguyên để bảo toàn lịch sử; hãy tạo bài mới nếu cần thay đổi.</div>@endif
<form class="panel" method="post" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">@csrf @if($article->exists)@method('patch')@endif
<div class="form-field"><label class="field-label" for="title">Tiêu đề *</label><input id="title" name="title" type="text" value="{{ old('title', $article->title) }}" required maxlength="200"></div>
<div class="form-field"><label class="field-label" for="content">Nội dung kiến thức *</label><textarea id="content" name="content" rows="16" required maxlength="100000">{{ old('content', $article->content) }}</textarea><p class="help">Văn bản thuần; xuống dòng để chia đoạn. Nội dung này là cơ sở của các câu hỏi.</p></div>
<div class="form-field"><label class="field-label" for="image_url">URL ảnh minh họa</label><input id="image_url" name="image_url" type="url" value="{{ old('image_url', $article->image_url) }}" maxlength="2048"><p class="help">Dùng ảnh có quyền sử dụng. Để trống sẽ dùng minh họa đại dương của dự án.</p></div>
<div class="form-grid"><div class="form-field"><label class="field-label" for="source_name">Tên nguồn</label><input id="source_name" name="source_name" type="text" value="{{ old('source_name', $article->source_name) }}" maxlength="255"></div><div class="form-field"><label class="field-label" for="source_url">Link nguồn</label><input id="source_url" name="source_url" type="url" value="{{ old('source_url', $article->source_url) }}" maxlength="2048"></div></div>
<div class="actions">@if(!$article->exists || !$article->hasAttempts())<button class="btn">Lưu bài viết ↗</button>@endif<a class="btn btn-outline" href="{{ route('admin.articles.index') }}">Quay lại</a></div></form></div>
@endsection
