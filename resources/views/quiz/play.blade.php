@extends('layouts.ocean')
@section('title', 'Làm bài · '.$set->title)
@section('content')
<div class="content-narrow"><div class="page-heading"><div><span class="eyebrow">ĐẾN LÚC THỬ SỨC</span><h1>{{ $set->title }}</h1><p>5 câu hỏi · Đúng 10 điểm/câu · Không giới hạn thời gian</p></div><span class="badge published">Đang làm bài</span></div><div class="inline-note" style="margin-bottom:28px">Bạn có thể thay đổi lựa chọn trước khi nộp hoặc bỏ trống câu chưa chắc chắn. Lựa chọn chưa nộp sẽ không được giữ khi tải lại trang.</div>
<form method="post" action="{{ route('attempts.submit', $attempt) }}" data-submit-once>@csrf
@foreach($questions as $question)<fieldset class="question-block"><legend>{{ $question->position }}. {{ $question->content }}</legend><div class="options-grid">@foreach($question->options as $option)<label class="option"><input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" @checked((string)old('answers.'.$question->id) === (string)$option->id)><span class="option-letter">{{ chr(64 + $option->position) }}</span><span>{{ $option->content }}</span></label>@endforeach</div></fieldset>@endforeach
<div class="actions"><button class="btn" type="submit">Nộp bài & xem kết quả ↗</button><span class="muted" style="font-size:12px">Kết quả được chấm và lưu khi nộp bài.</span></div></form></div>
@endsection
