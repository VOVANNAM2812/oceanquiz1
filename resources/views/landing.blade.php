@extends('layouts.ocean')
@section('title', 'Chạm vào đại dương tri thức')
@section('content')
<div class="hero landing-hero"><img class="hero-art" src="{{ asset('ocean-scene.svg') }}" alt="Minh họa cá voi giữa đại dương xanh"><div class="hero-copy"><span class="eyebrow">CHÀO MỪNG ĐẾN VỚI OCEANQUIZ</span><h2>Mỗi lần tò mò,<br>một lần <em>khám phá.</em></h2><p>Từ rạn san hô rực rỡ đến những người khổng lồ hiền lành. Cùng đọc, thử sức và hiểu thêm về thế giới dưới mặt nước.</p><a class="btn btn-mint" href="{{ auth()->check() ? route('home') : route('register') }}">Bắt đầu hành trình <span>↗</span></a><span class="hero-footnote">5 CÂU HỎI · KHÔNG ÁP LỰC THỜI GIAN · MỖI NGÀY MỘT ĐIỀU MỚI</span></div></div>
<div class="section-head"><div><span class="eyebrow">HỌC THEO CÁCH CỦA BẠN</span><h2>Một hành trình, ba bước nhỏ.</h2></div></div>
<div class="steps"><div class="step"><strong>01 / ĐỌC</strong><h3>Gặp một người bạn biển.</h3><p>Chọn chủ đề yêu thích và đọc bài kiến thức ngắn, rõ ràng, có nguồn tham khảo.</p></div><div class="step"><strong>02 / THỬ SỨC</strong><h3>Biến tò mò thành hiểu biết.</h3><p>Năm câu hỏi, bốn lựa chọn. Dành thời gian suy nghĩ theo nhịp độ của bạn.</p></div><div class="step"><strong>03 / GHI NHỚ</strong><h3>Mang kiến thức đi xa hơn.</h3><p>Xem giải thích, theo dõi lịch sử và cùng các nhà thám hiểm chinh phục bảng xếp hạng.</p></div></div>
@endsection
