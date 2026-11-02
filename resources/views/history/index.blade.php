@extends('layouts.ocean')
@section('title', 'Lịch sử học tập')
@section('content')
<div class="page-heading"><div><span class="eyebrow">NHỮNG CHUYẾN ĐI ĐÃ QUA</span><h1>Mỗi lần thử sức, một lần tiến bộ.</h1><p>Lưu lại kiến thức bạn đã khám phá qua {{ $attempts->total() }} lượt hoàn thành.</p></div><a class="btn btn-outline" href="{{ route('home') }}">Khám phá tiếp ↗</a></div>
<div class="panel table-scroll"><table><thead><tr><th>Bộ câu hỏi</th><th>Điểm</th><th>Số câu đúng</th><th>Hoàn thành · giờ VN</th><th></th></tr></thead><tbody>@forelse($attempts as $attempt)<tr><td><strong>{{ $attempt->questionSet->title }}</strong></td><td><strong class="correct">{{ $attempt->total_score }}/50</strong></td><td>{{ $attempt->correct_count }}/5</td><td>{{ $attempt->finished_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</td><td><a class="text-link" href="{{ route('attempts.result', $attempt) }}">Xem kết quả ↗</a></td></tr>@empty<tr><td colspan="5"><div class="empty">Chưa có lượt hoàn thành. Hãy chọn một chủ đề yêu thích và bắt đầu nhé!</div></td></tr>@endforelse</tbody></table></div>{{ $attempts->links() }}
@endsection
