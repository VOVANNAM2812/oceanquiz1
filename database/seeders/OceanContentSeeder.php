<?php

namespace Database\Seeders;

use App\Models\{Article, QuestionSet};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OceanContentSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'title' => 'Cá voi xanh: người khổng lồ của biển',
                'description' => 'Gặp một loài thú biển khổng lồ với cách kiếm ăn thật đặc biệt.',
                'url' => 'https://www.fisheries.noaa.gov/species/blue-whale',
                'content' => "Cá voi xanh là động vật có vú sống ở biển. Chúng cần lên mặt nước để hít thở không khí.\n\nThức ăn chủ yếu của cá voi xanh là nhuyễn thể krill. Khi kiếm ăn, cá voi ngậm một lượng nước lớn rồi đẩy nước ra, giữ con mồi lại bằng các tấm sừng hàm.\n\nVa chạm với tàu và mắc vào ngư cụ là những mối đe dọa đối với cá voi xanh.",
                'questions' => [
                    ['Cá voi xanh thuộc nhóm nào?', ['Cá', 'Động vật có vú', 'Chim', 'Côn trùng'], 1, 'Cá voi xanh là động vật có vú.'],
                    ['Cá voi xanh cần lên mặt nước để làm gì?', ['Đẻ trứng', 'Uống nước ngọt', 'Hít thở không khí', 'Mọc vây'], 2, 'Cá voi cần hít thở không khí.'],
                    ['Thức ăn chủ yếu của cá voi xanh là gì?', ['Krill', 'Rong biển', 'San hô', 'Cát'], 0, 'Krill là thức ăn chủ yếu của cá voi xanh.'],
                    ['Bộ phận nào giúp cá voi xanh giữ lại con mồi?', ['Mai', 'Chân', 'Mang', 'Tấm sừng hàm'], 3, 'Các tấm sừng hàm giữ con mồi khi nước được đẩy ra.'],
                    ['Điều nào là mối đe dọa được nêu trong bài?', ['Sóng nhỏ', 'Va chạm với tàu', 'Ánh trăng', 'Mưa trên biển'], 1, 'Va chạm với tàu là một mối đe dọa đối với cá voi.'],
                ],
            ],
            [
                'title' => 'Rùa biển: hành trình trở về bãi cát',
                'description' => 'Tìm hiểu nhịp sống dưới nước và những bãi biển cần được bảo vệ.',
                'url' => 'https://www.fisheries.noaa.gov/feature-story/what-can-you-do-help-sea-turtles',
                'content' => "Rùa biển phải lên mặt nước để thở. Rùa cái trưởng thành lên bãi cát, đào tổ và đẻ trứng.\n\nÁnh sáng nhân tạo trên bờ có thể gây khó khăn cho rùa con tìm đường ra biển. Khi gặp rùa, hãy giữ khoảng cách và không làm phiền.\n\nThu gom rác, giảm đồ nhựa dùng một lần và giữ bãi biển sạch là những việc giúp bảo vệ môi trường sống của rùa.",
                'questions' => [
                    ['Rùa biển lên mặt nước để làm gì?', ['Xây tổ', 'Thở', 'Tìm lửa', 'Thay mai'], 1, 'Rùa biển cần lên mặt nước để thở.'],
                    ['Rùa cái thường đào tổ ở đâu?', ['Trên cây', 'Trong băng', 'Bãi cát', 'Giữa mặt biển'], 2, 'Rùa cái đào tổ trên bãi cát để đẻ trứng.'],
                    ['Yếu tố nào có thể gây khó khăn cho rùa con tìm ra biển?', ['Ánh sáng nhân tạo', 'Bãi biển sạch', 'Giữ khoảng cách', 'Giảm rác nhựa'], 0, 'Ánh sáng nhân tạo có thể làm rùa con mất định hướng.'],
                    ['Nên làm gì khi gặp rùa biển?', ['Đuổi theo', 'Chạm vào', 'Mang về nhà', 'Giữ khoảng cách'], 3, 'Giữ khoảng cách để không làm phiền rùa.'],
                    ['Việc nào giúp bảo vệ môi trường sống của rùa?', ['Xả rác', 'Giữ bãi biển sạch', 'Phá tổ trứng', 'Bật đèn vào tổ'], 1, 'Giữ bãi biển sạch giúp bảo vệ nơi rùa sinh sống và làm tổ.'],
                ],
            ],
            [
                'title' => 'San hô: khu vườn sống dưới đại dương',
                'description' => 'Nhìn kỹ hơn những sinh vật thường bị nhầm là cây hoặc đá.',
                'url' => 'https://oceanexplorer.noaa.gov/ocean-fact/coral-animal/',
                'content' => "San hô là động vật. Nhiều tập đoàn san hô gồm các cá thể nhỏ gọi là polyp sống cùng nhau.\n\nMột số san hô sống cộng sinh với tảo. Tảo nhận nơi ở, còn san hô nhận được nguồn dinh dưỡng từ tảo. Không phải mọi loài san hô đều phụ thuộc vào tảo.\n\nĐừng nhầm hình dáng giống cây với thực vật: những polyp là các động vật sống.",
                'questions' => [
                    ['San hô thuộc nhóm nào?', ['Đá', 'Thực vật', 'Động vật', 'Kim loại'], 2, 'San hô là động vật.'],
                    ['Cá thể nhỏ trong nhiều tập đoàn san hô được gọi là gì?', ['Polyp', 'Krill', 'Hạt', 'Lá'], 0, 'Các cá thể này được gọi là polyp.'],
                    ['Một số san hô sống cộng sinh với sinh vật nào?', ['Chim', 'Thỏ', 'Ong', 'Tảo'], 3, 'Một số san hô sống cộng sinh với tảo.'],
                    ['Tảo có thể nhận lợi ích gì từ mối quan hệ này?', ['Có chân', 'Có nơi ở', 'Có mai', 'Có lông'], 1, 'San hô cung cấp nơi ở cho tảo cộng sinh.'],
                    ['Nhận định nào đúng theo bài?', ['Mọi san hô là cây', 'Polyp là đá', 'Không phải mọi san hô đều phụ thuộc tảo', 'San hô không sống'], 2, 'Chỉ một số san hô sống cộng sinh và nhận dinh dưỡng từ tảo.'],
                ],
            ],
        ];

        DB::transaction(function () use ($topics) {
            foreach ($topics as $topic) {
                $article = Article::firstOrCreate(['title' => $topic['title']], ['content' => $topic['content'], 'source_name' => 'NOAA · nội dung được diễn giải bằng tiếng Việt', 'source_url' => $topic['url']]);
                $set = QuestionSet::firstOrCreate(['article_id' => $article->id, 'title' => $topic['title']], ['description' => $topic['description'], 'status' => 'draft']);
                if (! $set->wasRecentlyCreated) continue;
                foreach ($topic['questions'] as $index => [$content, $options, $correct, $explanation]) {
                    $question = $set->questions()->create(['content' => $content, 'explanation' => $explanation, 'position' => $index + 1]);
                    foreach ($options as $i => $option) $question->options()->create(['content' => $option, 'position' => $i + 1, 'is_correct' => $i === $correct]);
                }
                $set->update(['status' => 'published']);
            }
            $article = Article::firstWhere('title', $topics[0]['title']);
            QuestionSet::firstOrCreate(['article_id' => $article->id, 'title' => 'Bộ nháp đang biên soạn'], ['description' => 'Bộ nháp dùng để kiểm tra quyền truy cập.', 'status' => 'draft']);
        });
    }
}
