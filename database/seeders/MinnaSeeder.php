<?php

namespace Database\Seeders;

use App\Support\Cache\FlashcardCache;
use App\Support\Cache\MinnaCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MinnaSeeder extends Seeder
{
    /**
     * Seed the application's database with Minna no Nihongo lessons.
     */
    public function run(): void
    {
        // Chuẩn bị dữ liệu 50 bài, mỗi bài có 5 phần.
        // CHƯA ghi DB. Khi có bảng, chỉ cần bỏ comment các lệnh insert bên dưới.

        $sectionCatalog = [
            ['key' => 'tu-vung',         'title' => 'Phần 1: Từ vựng'],
            ['key' => 'ngu-phap',        'title' => 'Phần 2: Ngữ pháp'],
            ['key' => 'luyen-doc',       'title' => 'Phần 3: Luyện đọc'],
            ['key' => 'hoi-thoai',       'title' => 'Phần 4: Hội thoại'],
            ['key' => 'han-tu',          'title' => 'Phần 5: Hán tự'],
        ];

        $lessons = [];
        $lessonTitles = [
            1 => 'はじめまして',
            2 => 'これから お世話に なります',
            3 => 'これを ください',
            4 => 'そちらは 何時までですか',
            5 => 'この電車は 甲子園へ 行きますか',
        ];
        $sections = [];

        for ($i = 1; $i <= 50; $i++) {
            $titlePrefix = 'Bài ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);
            $title = isset($lessonTitles[$i]) ? $titlePrefix . ' ' . $lessonTitles[$i] : $titlePrefix;
            $lessons[] = [
                'number' => $i,
                'title' => $title,
                'description' => null,
            ];

            foreach ($sectionCatalog as $index => $sectionDef) {
                // Nội dung mẫu theo từng phần
                $content = null;
                if ($sectionDef['key'] === 'tu-vung') {
                    if ($i === 1) {
                        // Dữ liệu thật cho Bài 1
                        $content = [
                            'vocab' => [
                                ['tu_vung' => 'わたし', 'han_tu' => '私', 'am_han' => null, 'phat_am' => null, 'nghia' => 'tôi'],
                                ['tu_vung' => 'あなた', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'anh / chị / ông / bà'],
                                ['tu_vung' => 'あのひと', 'han_tu' => 'あの人', 'am_han' => 'NHÂN', 'phat_am' => null, 'nghia' => 'người kia, người đó'],
                                ['tu_vung' => 'あのかた', 'han_tu' => 'あの方', 'am_han' => 'PHƯƠNG', 'phat_am' => null, 'nghia' => 'vị kia (cách nói lịch sự của あのひと)'],
                                ['tu_vung' => '～さん', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'anh, chị, ông, bà'],
                                ['tu_vung' => '～ちゃん', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'hậu tố thêm vào sau tên của trẻ em (thay cho ～さん)'],
                                ['tu_vung' => '～じん', 'han_tu' => '～人', 'am_han' => 'NHÂN', 'phat_am' => null, 'nghia' => 'người (nước)~ (ví dụ: 「アメリカじん」: người Mỹ)'],
                                ['tu_vung' => 'せんせい', 'han_tu' => '先生', 'am_han' => 'TIÊN SINH', 'phat_am' => null, 'nghia' => 'thầy / cô'],
                                ['tu_vung' => 'きょうし', 'han_tu' => '教師', 'am_han' => 'GIÁO SƯ', 'phat_am' => null, 'nghia' => 'giáo viên'],
                                ['tu_vung' => 'がくせい', 'han_tu' => '学生', 'am_han' => 'HỌC SINH', 'phat_am' => null, 'nghia' => 'học sinh, sinh viên'],
                                ['tu_vung' => 'かいしゃいん', 'han_tu' => '会社員', 'am_han' => 'HỘI XÃ VIÊN', 'phat_am' => null, 'nghia' => 'nhân viên công ty'],
                                ['tu_vung' => 'しゃいん', 'han_tu' => '社員', 'am_han' => 'XÃ VIÊN', 'phat_am' => null, 'nghia' => 'nhân viên (công ty ~, ví dụ: ＩＭＣのしゃいん)'],
                                ['tu_vung' => 'ぎんこういん', 'han_tu' => '銀行員', 'am_han' => 'NGÂN HÀNH VIÊN', 'phat_am' => null, 'nghia' => 'nhân viên ngân hàng'],
                                ['tu_vung' => 'いしゃ', 'han_tu' => '医者', 'am_han' => 'Y GIẢ', 'phat_am' => null, 'nghia' => 'bác sĩ'],
                                ['tu_vung' => 'けんきゅうしゃ', 'han_tu' => '研究者', 'am_han' => 'NGHIÊN CỨU GIẢ', 'phat_am' => null, 'nghia' => 'nhà nghiên cứu'],
                                ['tu_vung' => 'だいがく', 'han_tu' => '大学', 'am_han' => 'ĐẠI HỌC', 'phat_am' => null, 'nghia' => 'đại học, trường đại học'],
                                ['tu_vung' => 'びょういん', 'han_tu' => '病院', 'am_han' => 'BỆNH VIỆN', 'phat_am' => null, 'nghia' => 'bệnh viện'],
                                ['tu_vung' => 'だれ（どなた）', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'ai (どなた là cách nói lịch sự của だれ)'],
                                ['tu_vung' => '～さい', 'han_tu' => '～歳', 'am_han' => 'NHẤT TUẾ', 'phat_am' => null, 'nghia' => '~ tuổi'],
                                ['tu_vung' => 'なんさい（おいくつ）', 'han_tu' => '何歳', 'am_han' => 'HÀ TUẾ', 'phat_am' => null, 'nghia' => 'mấy tuổi / bao nhiêu tuổi (おいくつ là cách nói lịch sự của なんさい)'],
                                ['tu_vung' => 'はい', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'vâng, dạ'],
                                ['tu_vung' => 'いいえ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'không'],
                            ],
                            'mau_cau' => [
                                ['jp' => 'はじめまして。', 'nghia' => 'Rất hân hạnh được gặp anh/chị'],
                                ['jp' => '～からきました。', 'nghia' => '(Tôi) đến từ ~'],
                                ['jp' => 'どうぞよろしく[おねがいします]。', 'nghia' => 'Rất vui khi được làm quen'],
                                ['jp' => 'しつれいですが', 'nghia' => 'Xin lỗi, (dùng khi muốn hỏi)'],
                                ['jp' => 'おなまえは？', 'nghia' => 'Tên anh/chị là gì?'],
                                ['jp' => 'こちらは～さんです。', 'nghia' => 'Đây là anh/chị/ông/bà ~'],
                            ],
                            'countries' => [
                                ['tu_vung' => 'アメリカ', 'nghia' => 'Mỹ'],
                                ['tu_vung' => 'イギリス', 'nghia' => 'Anh'],
                                ['tu_vung' => 'インド', 'nghia' => 'Ấn Độ'],
                                ['tu_vung' => 'インドネシア', 'nghia' => 'Indonesia'],
                                ['tu_vung' => 'かんこく', 'nghia' => 'Hàn Quốc'],
                                ['tu_vung' => 'タイ', 'nghia' => 'Thái Lan'],
                                ['tu_vung' => 'ちゅうごく', 'nghia' => 'Trung Quốc'],
                                ['tu_vung' => 'ドイツ', 'nghia' => 'Đức'],
                                ['tu_vung' => 'にほん（にっぽん）', 'nghia' => 'Nhật Bản'],
                                ['tu_vung' => 'ブラジル', 'nghia' => 'Braxin'],
                            ],
                            'proper_nouns' => [
                                ['tu_vung' => 'ＩＭＣ／パワーでんき／ブラジルエアー', 'nghia' => 'tên công ty (giả định)'],
                                ['tu_vung' => 'ＡＫＣ', 'nghia' => 'tên một tổ chức (giả định)'],
                                ['tu_vung' => 'こうべびょういん', 'nghia' => 'tên một bệnh viện (giả định)'],
                                ['tu_vung' => 'さくらだいがく', 'nghia' => 'Đại học Sakura (giả định)'],
                                ['tu_vung' => 'ふじだいがく', 'nghia' => 'Đại học Phú Sĩ (giả định)'],
                            ],
                        ];
                    } elseif ($i === 2) {
                        // Dữ liệu thật cho Bài 2
                        $content = [
                            'vocab' => [
                                ['tu_vung' => 'これ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cái này, đây (vật ở gần người nói)'],
                                ['tu_vung' => 'それ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cái đó, đó (vật ở gần người nghe)'],
                                ['tu_vung' => 'あれ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cái kia, kia (vật ở xa cả người nói và người nghe)'],
                                ['tu_vung' => 'この～', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '~ này'],
                                ['tu_vung' => 'その～', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '~ đó'],
                                ['tu_vung' => 'あの～', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '~ kia'],
                                ['tu_vung' => 'ほん', 'han_tu' => '本', 'am_han' => 'BẢN/BỔN', 'phat_am' => null, 'nghia' => 'sách'],
                                ['tu_vung' => 'じしょ', 'han_tu' => '辞書', 'am_han' => 'TỪ THƯ', 'phat_am' => null, 'nghia' => 'từ điển'],
                                ['tu_vung' => 'ざっし', 'han_tu' => '雑誌', 'am_han' => 'TẠP CHÍ', 'phat_am' => null, 'nghia' => 'tạp chí'],
                                ['tu_vung' => 'しんぶん', 'han_tu' => '新聞', 'am_han' => 'TÂN VĂN', 'phat_am' => null, 'nghia' => 'báo'],
                                ['tu_vung' => 'ノート', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'vở'],
                                ['tu_vung' => 'てちょう', 'han_tu' => '手帳', 'am_han' => 'THỦ TRƯỜNG', 'phat_am' => null, 'nghia' => 'sổ tay'],
                                ['tu_vung' => 'めいし', 'han_tu' => '名刺', 'am_han' => 'DANH THÍCH', 'phat_am' => null, 'nghia' => 'danh thiếp'],
                                ['tu_vung' => 'カード', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'thẻ, cạc'],
                                ['tu_vung' => 'えんぴつ', 'han_tu' => '鉛筆', 'am_han' => 'DUYÊN BÚT', 'phat_am' => null, 'nghia' => 'bút chì'],
                                ['tu_vung' => 'ボールペン', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bút bi'],
                                ['tu_vung' => 'シャープペンシル', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bút chì kim, bút chì bấm'],
                                ['tu_vung' => 'かぎ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'chìa khóa'],
                                ['tu_vung' => 'とけい', 'han_tu' => '時計', 'am_han' => 'THỜI KẾ', 'phat_am' => null, 'nghia' => 'đồng hồ'],
                                ['tu_vung' => 'かさ', 'han_tu' => '傘', 'am_han' => 'TÁN', 'phat_am' => null, 'nghia' => 'ô, dù'],
                                ['tu_vung' => 'かばん', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cặp sách, túi sách'],
                                ['tu_vung' => 'CD', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'đĩa CD'],
                                ['tu_vung' => 'テレビ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'tivi'],
                                ['tu_vung' => 'ラジオ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'radio'],
                                ['tu_vung' => 'カメラ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'máy ảnh'],
                                ['tu_vung' => 'コンピューター', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'máy vi tính'],
                                ['tu_vung' => 'くるま', 'han_tu' => '車', 'am_han' => 'XA', 'phat_am' => null, 'nghia' => 'ô tô, xe hơi'],
                                ['tu_vung' => 'つくえ', 'han_tu' => '机', 'am_han' => 'KỶ', 'phat_am' => null, 'nghia' => 'cái bàn'],
                                ['tu_vung' => 'いす', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cái ghế'],
                                ['tu_vung' => 'チョコレート', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'socola'],
                                ['tu_vung' => 'コーヒー', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cà phê'],
                                ['tu_vung' => '[お]みやげ', 'han_tu' => '[お]土産', 'am_han' => 'THỔ SẢN', 'phat_am' => null, 'nghia' => 'quà (mua khi đi xa về hoặc mang đi thăm nhà người nào đó)'],
                                ['tu_vung' => 'えいご', 'han_tu' => '英語', 'am_han' => 'ANH NGỮ', 'phat_am' => null, 'nghia' => 'tiếng Anh'],
                                ['tu_vung' => 'にほんご', 'han_tu' => '日本語', 'am_han' => 'NHẬT BẢN NGỮ', 'phat_am' => null, 'nghia' => 'tiếng Nhật'],
                                ['tu_vung' => '～ご', 'han_tu' => '～語', 'am_han' => 'NGỮ', 'phat_am' => null, 'nghia' => 'tiếng ~'],
                                ['tu_vung' => 'なに', 'han_tu' => '何', 'am_han' => 'HÀ', 'phat_am' => null, 'nghia' => 'cái gì'],
                                ['tu_vung' => 'そう', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'đúng rồi'],
                            ],
                            'mau_cau' => [
                                ['jp' => 'あのう', 'nghia' => 'à, ờ (dùng để biểu thị sự ngại ngùng, do dự)'],
                                ['jp' => 'えっ', 'nghia' => 'hả?'],
                                ['jp' => 'どうぞ', 'nghia' => 'xin mời (dùng khi mời ai đó cái gì)'],
                                ['jp' => '[どうも]ありがとうございます', 'nghia' => 'xin chân thành cảm ơn'],
                                ['jp' => 'そうですか', 'nghia' => 'thế à, vậy à'],
                                ['jp' => 'ちがいます', 'nghia' => 'không phải, không đúng, sai rồi'],
                                ['jp' => 'あ', 'nghia' => 'ôi! (dùng khi nhận ra điều gì)'],
                            ],
                            'cau' => [
                                ['jp' => 'これからおせわになります', 'nghia' => 'Từ nay tôi rất mong sự giúp đỡ của anh/chị'],
                                ['jp' => 'こちらこそ[どうぞ]よろしく[おねがいします]', 'nghia' => 'Chính tôi mới phải xin ông giúp đỡ cho'],
                            ],
                        ];
                    } elseif ($i === 3) {
                        // Dữ liệu thật cho Bài 3
                        $content = [
                            'vocab' => [
                                ['tu_vung' => 'ここ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'chỗ này, đây'],
                                ['tu_vung' => 'そこ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'chỗ đó, đó'],
                                ['tu_vung' => 'あそこ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'chỗ kia, kia'],
                                ['tu_vung' => 'どこ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'chỗ nào, đâu'],
                                ['tu_vung' => 'こちら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'phía này, đằng này, chỗ này, đây'],
                                ['tu_vung' => 'そちら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'phía đó, đằng đó, chỗ đó, đó'],
                                ['tu_vung' => 'あちら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'phía kia, đằng kia, chỗ kia, kia'],
                                ['tu_vung' => 'どちら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'phía nào, đằng nào, chỗ nào, đâu'],
                                ['tu_vung' => 'きょうしつ', 'han_tu' => '教室', 'am_han' => 'GIÁO THẤT', 'phat_am' => null, 'nghia' => 'lớp học, phòng học'],
                                ['tu_vung' => 'しょくどう', 'han_tu' => '食堂', 'am_han' => 'THỰC ĐƯỜNG', 'phat_am' => null, 'nghia' => 'nhà ăn'],
                                ['tu_vung' => 'じむしょ', 'han_tu' => '事務所', 'am_han' => 'SỰ VỤ SỞ', 'phat_am' => null, 'nghia' => 'văn phòng'],
                                ['tu_vung' => 'かいぎしつ', 'han_tu' => '会議室', 'am_han' => 'HỘI NGHỊ THẤT', 'phat_am' => null, 'nghia' => 'phòng họp'],
                                ['tu_vung' => 'うけつけ', 'han_tu' => '受付', 'am_han' => 'THỤ PHÓ', 'phat_am' => null, 'nghia' => 'bộ phận tiếp tân, phòng thường trực'],
                                ['tu_vung' => 'ロビー', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'hành lang, đại sảnh'],
                                ['tu_vung' => 'へや', 'han_tu' => '部屋', 'am_han' => 'BỘ ỐC', 'phat_am' => null, 'nghia' => 'căn phòng'],
                                ['tu_vung' => 'トイレ（おてあらい）', 'han_tu' => '（お手洗い）', 'am_han' => 'THỦ TẨY', 'phat_am' => null, 'nghia' => 'nhà vệ sinh, phòng vệ sinh, toalet'],
                                ['tu_vung' => 'かいだん', 'han_tu' => '階段', 'am_han' => 'GIAI ĐOẠN', 'phat_am' => null, 'nghia' => 'cầu thang'],
                                ['tu_vung' => 'エレベーター', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'thang máy'],
                                ['tu_vung' => 'エスカレーター', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'thang cuốn'],
                                ['tu_vung' => 'じどうはんばいき', 'han_tu' => '自動販売機', 'am_han' => 'TỰ ĐỘNG PHIẾN MẠI CƠ', 'phat_am' => null, 'nghia' => 'máy bán hàng tự động'],
                                ['tu_vung' => 'でんわ', 'han_tu' => '電話', 'am_han' => 'ĐIỆN THOẠI', 'phat_am' => null, 'nghia' => 'máy điện thoại, điện thoại'],
                                ['tu_vung' => '[お]くに', 'han_tu' => '[お]国', 'am_han' => 'QUỐC', 'phat_am' => null, 'nghia' => 'đất nước (của anh/chị)'],
                                ['tu_vung' => 'かいしゃ', 'han_tu' => '会社', 'am_han' => 'HỘI XÃ', 'phat_am' => null, 'nghia' => 'công ty'],
                                ['tu_vung' => 'うち', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'nhà'],
                                ['tu_vung' => 'くつ', 'han_tu' => '靴', 'am_han' => 'NGOA', 'phat_am' => null, 'nghia' => 'giày'],
                                ['tu_vung' => 'ネクタイ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'cà vạt'],
                                ['tu_vung' => 'ワイン', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'rượu vang'],
                                ['tu_vung' => 'うりば', 'han_tu' => '売り場', 'am_han' => 'MẠI TRƯỜNG', 'phat_am' => null, 'nghia' => 'quầy bán (trong một cửa hàng bách hóa)'],
                                ['tu_vung' => 'ちか', 'han_tu' => '地下', 'am_han' => 'ĐỊA HẠ', 'phat_am' => null, 'nghia' => 'tầng hầm, dưới mặt đất'],
                                ['tu_vung' => '～かい（～がい）', 'han_tu' => '～階', 'am_han' => 'GIAI', 'phat_am' => null, 'nghia' => 'tầng thứ ~'],
                                ['tu_vung' => 'なんがい', 'han_tu' => '何階', 'am_han' => 'HÀ GIAI', 'phat_am' => null, 'nghia' => 'tầng mấy'],
                                ['tu_vung' => '～えん', 'han_tu' => '～円', 'am_han' => 'VIÊN', 'phat_am' => null, 'nghia' => '~ yên'],
                                ['tu_vung' => 'いくら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bao nhiêu tiền'],
                                ['tu_vung' => 'ひゃく', 'han_tu' => '百', 'am_han' => 'BÁCH', 'phat_am' => null, 'nghia' => 'trăm'],
                                ['tu_vung' => 'せん', 'han_tu' => '千', 'am_han' => 'THIÊN', 'phat_am' => null, 'nghia' => 'nghìn'],
                                ['tu_vung' => 'まん', 'han_tu' => '万', 'am_han' => 'VẠN', 'phat_am' => null, 'nghia' => 'mười nghìn, vạn'],
                            ],
                            'mau_cau' => [
                                ['jp' => 'すみません', 'nghia' => 'Xin lỗi'],
                                ['jp' => '～を ください', 'nghia' => 'Cho tôi [~]'],
                                ['jp' => '～どうも', 'nghia' => 'Cảm ơn'],
                            ],
                            'cau' => [
                                ['jp' => 'いらっしゃいませ', 'nghia' => 'Xin chào quý khách, mời quý khách vào'],
                                ['jp' => '[～を]みせてください', 'nghia' => 'Cho tôi xem [~]'],
                                ['jp' => 'じゃ', 'nghia' => 'Thế thì, vậy thì'],
                                ['jp' => '[～を]ください', 'nghia' => 'Cho tôi [~]'],
                            ],
                            'places' => [
                                ['tu_vung' => 'イタリア', 'nghia' => 'Ý'],
                                ['tu_vung' => 'スイス', 'nghia' => 'Thụy Sĩ'],
                                ['tu_vung' => 'フランス', 'nghia' => 'Pháp'],
                                ['tu_vung' => 'ジャカルタ', 'nghia' => 'Gia-các-ta'],
                                ['tu_vung' => 'バンコク', 'nghia' => 'Băng-cốc'],
                                ['tu_vung' => 'ベルリン', 'nghia' => 'Béc-lin'],
                                ['tu_vung' => '新大阪', 'nghia' => 'tên một nhà ga ở Osaka'],
                            ],
                        ];
                    } elseif ($i === 4) {
                        // Dữ liệu thật cho Bài 4
                        $content = [
                            'vocab' => [
                                ['tu_vung' => 'おきます', 'han_tu' => '起きます', 'am_han' => 'KHỞI', 'phat_am' => null, 'nghia' => 'dậy, thức dậy'],
                                ['tu_vung' => 'ねます', 'han_tu' => '寝ます', 'am_han' => 'TẨM', 'phat_am' => null, 'nghia' => 'ngủ, đi ngủ'],
                                ['tu_vung' => 'はたらきます', 'han_tu' => '働きます', 'am_han' => 'ĐỘNG', 'phat_am' => null, 'nghia' => 'làm việc'],
                                ['tu_vung' => 'やすみます', 'han_tu' => '休みます', 'am_han' => 'HƯU', 'phat_am' => null, 'nghia' => 'nghỉ, nghỉ ngơi'],
                                ['tu_vung' => 'べんきょうします', 'han_tu' => '勉強します', 'am_han' => 'MIỄN CƯỜNG', 'phat_am' => null, 'nghia' => 'học'],
                                ['tu_vung' => 'おわります', 'han_tu' => '終わります', 'am_han' => 'CHUNG', 'phat_am' => null, 'nghia' => 'hết, kết thúc, xong'],
                                ['tu_vung' => 'デパート', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bách hóa'],
                                ['tu_vung' => 'ぎんこう', 'han_tu' => '銀行', 'am_han' => 'NGÂN HÀNH', 'phat_am' => null, 'nghia' => 'ngân hàng'],
                                ['tu_vung' => 'ゆうびんきょく', 'han_tu' => '郵便局', 'am_han' => 'BƯU TIỆN CỤC', 'phat_am' => null, 'nghia' => 'bưu điện'],
                                ['tu_vung' => 'としょかん', 'han_tu' => '図書館', 'am_han' => 'ĐỒ THƯ QUÁN', 'phat_am' => null, 'nghia' => 'thư viện'],
                                ['tu_vung' => 'びじゅつかん', 'han_tu' => '美術館', 'am_han' => 'MỸ THUẬT QUÁN', 'phat_am' => null, 'nghia' => 'bảo tàng mỹ thuật'],
                                ['tu_vung' => 'いま', 'han_tu' => '今', 'am_han' => 'KIM', 'phat_am' => null, 'nghia' => 'bây giờ'],
                                ['tu_vung' => '〜じ', 'han_tu' => '〜時', 'am_han' => 'THỜI', 'phat_am' => null, 'nghia' => '- giờ'],
                                ['tu_vung' => '〜ふん (〜ぷん)', 'han_tu' => '〜分', 'am_han' => 'PHÂN', 'phat_am' => null, 'nghia' => '- phút'],
                                ['tu_vung' => 'はん', 'han_tu' => '半', 'am_han' => 'BÁN', 'phat_am' => null, 'nghia' => 'rưỡi, nửa'],
                                ['tu_vung' => 'なんじ', 'han_tu' => '何時', 'am_han' => 'HÀ THỜI', 'phat_am' => null, 'nghia' => 'mấy giờ'],
                                ['tu_vung' => 'なんぷん', 'han_tu' => '何分', 'am_han' => 'HÀ PHÂN', 'phat_am' => null, 'nghia' => 'mấy phút'],
                                ['tu_vung' => 'ごぜん', 'han_tu' => '午前', 'am_han' => 'NGỌ TIỀN', 'phat_am' => null, 'nghia' => 'sáng, trước 12 giờ trưa'],
                                ['tu_vung' => 'ごご', 'han_tu' => '午後', 'am_han' => 'NGỌ HẬU', 'phat_am' => null, 'nghia' => 'chiều, sau 12 giờ trưa'],
                                ['tu_vung' => 'あさ', 'han_tu' => '朝', 'am_han' => 'TRIỀU', 'phat_am' => null, 'nghia' => 'buổi sáng, sáng'],
                                ['tu_vung' => 'ひる', 'han_tu' => '昼', 'am_han' => 'TRÚ', 'phat_am' => null, 'nghia' => 'buổi trưa, trưa'],
                                ['tu_vung' => 'ばん (よる)', 'han_tu' => '晩(夜)', 'am_han' => 'VÃN(DẠ)', 'phat_am' => null, 'nghia' => 'buổi tối, tối'],
                                ['tu_vung' => 'おととい', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'hôm kia'],
                                ['tu_vung' => 'きのう', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'hôm qua'],
                                ['tu_vung' => 'きょう', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'hôm nay'],
                                ['tu_vung' => 'あした', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'ngày mai'],
                                ['tu_vung' => 'あさって', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'ngày kia'],
                                ['tu_vung' => 'けさ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'sáng nay'],
                                ['tu_vung' => 'こんばん', 'han_tu' => '今晩', 'am_han' => 'KIM VÃN', 'phat_am' => null, 'nghia' => 'tối nay'],
                                ['tu_vung' => 'やすみ', 'han_tu' => '休み', 'am_han' => 'HƯU', 'phat_am' => null, 'nghia' => 'nghỉ, nghỉ phép, ngày nghỉ'],
                                ['tu_vung' => 'ひるやすみ', 'han_tu' => '昼休み', 'am_han' => 'TRÚ HƯU', 'phat_am' => null, 'nghia' => 'nghỉ trưa'],
                                ['tu_vung' => 'しけん', 'han_tu' => '試験', 'am_han' => 'THÍ NGHIỆM', 'phat_am' => null, 'nghia' => 'thi, kỳ thi, kiểm tra'],
                                ['tu_vung' => 'かいぎ', 'han_tu' => '会議', 'am_han' => 'HỘI NGHỊ', 'phat_am' => null, 'nghia' => 'cuộc họp, hội nghị ( → しま す: tổ chức cuộc họp, hội nghị)'],
                                ['tu_vung' => 'えいが', 'han_tu' => '映画', 'am_han' => 'ẢNH HỌA', 'phat_am' => null, 'nghia' => 'phim, điện ảnh'],
                                ['tu_vung' => 'まいあさ', 'han_tu' => '毎朝', 'am_han' => 'MỖI TRIỀU', 'phat_am' => null, 'nghia' => 'hàng sáng, mỗi sáng'],
                                ['tu_vung' => 'まいばん', 'han_tu' => '毎晩', 'am_han' => 'MỖI VÃN', 'phat_am' => null, 'nghia' => 'hàng tối, mỗi tối'],
                                ['tu_vung' => 'まいにち', 'han_tu' => '毎日', 'am_han' => 'MỖI NHẬT', 'phat_am' => null, 'nghia' => 'hàng ngày, mỗi ngày'],
                                ['tu_vung' => 'げつようび', 'han_tu' => '月曜日', 'am_han' => 'NGUYỆT DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ hai'],
                                ['tu_vung' => 'かようび', 'han_tu' => '火曜日', 'am_han' => 'HỎA DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ ba'],
                                ['tu_vung' => 'すいようび', 'han_tu' => '水曜日', 'am_han' => 'THỦY DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ tư'],
                                ['tu_vung' => 'もくようび', 'han_tu' => '木曜日', 'am_han' => 'MỘC DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ năm'],
                                ['tu_vung' => 'きんようび', 'han_tu' => '金曜日', 'am_han' => 'KIM DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ sáu'],
                                ['tu_vung' => 'どようび', 'han_tu' => '土曜日', 'am_han' => 'THỔ DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ bảy'],
                                ['tu_vung' => 'にちようび', 'han_tu' => '日曜日', 'am_han' => 'NHẬT DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'chủ nhật'],
                                ['tu_vung' => 'なんようび', 'han_tu' => '何曜日', 'am_han' => 'HÀ DIỆU NHẬT', 'phat_am' => null, 'nghia' => 'thứ mấy'],
                                ['tu_vung' => '〜から', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '∼ từ'],
                                ['tu_vung' => '〜まで', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '∼ đến'],
                                ['tu_vung' => '〜と', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => '∼ và (dùng để nối hai danh từ)'],
                                ['tu_vung' => 'たいへんですね', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'Anh/chị vất vả quá.'],
                                ['tu_vung' => 'ばんごう', 'han_tu' => '番号', 'am_han' => null, 'phat_am' => null, 'nghia' => 'số (số điện thoại, số phòng)'],
                                ['tu_vung' => 'なんばん', 'han_tu' => '何番', 'am_han' => null, 'phat_am' => null, 'nghia' => 'số bao nhiêu, số mấy'],
                                ['tu_vung' => 'そちら', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'ông/bà, phía ông/phía bà'],
                                ['tu_vung' => 'ニューヨーク', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'New York'],
                                ['tu_vung' => 'ペキン', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'Bắc Kinh'],
                                ['tu_vung' => 'ロサンゼルス', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'Los Angeles'],
                                ['tu_vung' => 'ロンドン', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'Luân Đôn'],
                                ['tu_vung' => 'あすか', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'tên giả định của một nhà hàng Nhật'],
                                ['tu_vung' => 'アップルぎんこう', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'ngân hàng Apple (giả định)'],
                                ['tu_vung' => 'みどりとしょかん', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'thư viện Midori (giả định)'],
                                ['tu_vung' => 'やまとびじゅつかん', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bảo tàng mỹ thuật Yamato (giả định)'],
                            ],
                        ];
                    } elseif ($i === 5) {
                        // Dữ liệu thật cho Bài 5
                        $content = [
                            'vocab' => [
                                ['tu_vung' => 'いきます', 'han_tu' => '行きます', 'am_han' => 'HÀNH', 'phat_am' => null, 'nghia' => 'đi'],
                                ['tu_vung' => 'きます', 'han_tu' => '来ます', 'am_han' => 'LAI', 'phat_am' => null, 'nghia' => 'đến'],
                                ['tu_vung' => 'かえります', 'han_tu' => '帰ります', 'am_han' => 'QUY', 'phat_am' => null, 'nghia' => 'về'],
                                ['tu_vung' => 'がっこう', 'han_tu' => '学校', 'am_han' => 'HỌC HIỆU', 'phat_am' => null, 'nghia' => 'trường học'],
                                ['tu_vung' => 'スーパー', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'siêu thị'],
                                ['tu_vung' => 'えき', 'han_tu' => '駅', 'am_han' => 'DỊCH', 'phat_am' => null, 'nghia' => 'ga, nhà ga'],
                                ['tu_vung' => 'ひこうき', 'han_tu' => '飛行機', 'am_han' => 'PHI HÀNH CƠ', 'phat_am' => null, 'nghia' => 'máy bay'],
                                ['tu_vung' => 'ふね', 'han_tu' => '船', 'am_han' => 'THUYỀN', 'phat_am' => null, 'nghia' => 'thuyền, tàu thủy'],
                                ['tu_vung' => 'でんしゃ', 'han_tu' => '電車', 'am_han' => 'ĐIỆN XA', 'phat_am' => null, 'nghia' => 'tàu điện'],
                                ['tu_vung' => 'ちかてつ', 'han_tu' => '地下鉄', 'am_han' => 'ĐỊA HẠ THIẾT', 'phat_am' => null, 'nghia' => 'tàu điện ngầm'],
                                ['tu_vung' => 'しんかんせん', 'han_tu' => '新幹線', 'am_han' => 'TÂN CÁN TUYẾN', 'phat_am' => null, 'nghia' => 'tàu Shinkansen (tàu điện siêu tốc của Nhật)'],
                                ['tu_vung' => 'バス', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'xe buýt'],
                                ['tu_vung' => 'タクシー', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'tắc-xi'],
                                ['tu_vung' => 'じてんしゃ', 'han_tu' => '自転車', 'am_han' => 'TỰ CHUYỂN XA', 'phat_am' => null, 'nghia' => 'xe đạp'],
                                ['tu_vung' => 'あるいて', 'han_tu' => '歩いて', 'am_han' => 'BỘ', 'phat_am' => null, 'nghia' => 'đi bộ'],
                                ['tu_vung' => 'ひと', 'han_tu' => '人', 'am_han' => 'NHÂN', 'phat_am' => null, 'nghia' => 'người'],
                                ['tu_vung' => 'ともだち', 'han_tu' => '友達', 'am_han' => 'HỮU ĐẠT', 'phat_am' => null, 'nghia' => 'bạn, bạn bè'],
                                ['tu_vung' => 'かれ', 'han_tu' => '彼', 'am_han' => 'BỈ', 'phat_am' => null, 'nghia' => 'anh ấy, bạn trai'],
                                ['tu_vung' => 'かのじょ', 'han_tu' => '彼女', 'am_han' => 'BỈ NỮ', 'phat_am' => null, 'nghia' => 'chị ấy, bạn gái'],
                                ['tu_vung' => 'かぞく', 'han_tu' => '家族', 'am_han' => 'GIA TỘC', 'phat_am' => null, 'nghia' => 'gia đình'],
                                ['tu_vung' => 'ひとりで', 'han_tu' => '一人で', 'am_han' => 'NHẤT NHÂN', 'phat_am' => null, 'nghia' => 'một mình'],
                                ['tu_vung' => 'せんしゅう', 'han_tu' => '先週', 'am_han' => 'TIÊN CHU', 'phat_am' => null, 'nghia' => 'tuần trước'],
                                ['tu_vung' => 'こんしゅう', 'han_tu' => '今週', 'am_han' => 'KIM CHU', 'phat_am' => null, 'nghia' => 'tuần này'],
                                ['tu_vung' => 'らいしゅう', 'han_tu' => '来週', 'am_han' => 'LAI CHU', 'phat_am' => null, 'nghia' => 'tuần sau'],
                                ['tu_vung' => 'せんげつ', 'han_tu' => '先月', 'am_han' => 'TIÊN NGUYỆT', 'phat_am' => null, 'nghia' => 'tháng trước'],
                                ['tu_vung' => 'こんげつ', 'han_tu' => '今月', 'am_han' => 'KIM NGUYỆT', 'phat_am' => null, 'nghia' => 'tháng này'],
                                ['tu_vung' => 'らいげつ', 'han_tu' => '来月', 'am_han' => 'LAI NGUYỆT', 'phat_am' => null, 'nghia' => 'tháng sau'],
                                ['tu_vung' => 'きょねん', 'han_tu' => '去年', 'am_han' => 'KHỨ NIÊN', 'phat_am' => null, 'nghia' => 'năm ngoái'],
                                ['tu_vung' => 'ことし', 'han_tu' => '今年', 'am_han' => 'KIM NIÊN', 'phat_am' => null, 'nghia' => 'năm nay'],
                                ['tu_vung' => 'らいねん', 'han_tu' => '来年', 'am_han' => 'LAI NIÊN', 'phat_am' => null, 'nghia' => 'năm sau'],
                                ['tu_vung' => '〜ねん', 'han_tu' => '〜年', 'am_han' => 'NIÊN', 'phat_am' => null, 'nghia' => 'năm -'],
                                ['tu_vung' => 'なんねん', 'han_tu' => '何年', 'am_han' => 'HÀ NIÊN', 'phat_am' => null, 'nghia' => 'mấy năm'],
                                ['tu_vung' => '〜がつ', 'han_tu' => '〜月', 'am_han' => 'NGUYỆT', 'phat_am' => null, 'nghia' => 'tháng -'],
                                ['tu_vung' => 'なんがつ', 'han_tu' => '何月', 'am_han' => 'HÀ NGUYỆT', 'phat_am' => null, 'nghia' => 'tháng mấy'],
                                ['tu_vung' => 'ついたち', 'han_tu' => '1日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 1'],
                                ['tu_vung' => 'ふつか', 'han_tu' => '2日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 2, 2 ngày'],
                                ['tu_vung' => 'みっか', 'han_tu' => '3日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 3, 3 ngày'],
                                ['tu_vung' => 'よっか', 'han_tu' => '4日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 4, 4 ngày'],
                                ['tu_vung' => 'いつか', 'han_tu' => '5日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 5, 5 ngày'],
                                ['tu_vung' => 'むいか', 'han_tu' => '6日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 6, 6 ngày'],
                                ['tu_vung' => 'なのか', 'han_tu' => '7日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 7, 7 ngày'],
                                ['tu_vung' => 'ようか', 'han_tu' => '8日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 8, 8 ngày'],
                                ['tu_vung' => 'ここのか', 'han_tu' => '9日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 9, 9 ngày'],
                                ['tu_vung' => 'とおか', 'han_tu' => '10日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày mồng 10, 10 ngày'],
                                ['tu_vung' => 'じゅうよっか', 'han_tu' => '14日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày 14, 14 ngày'],
                                ['tu_vung' => 'はつか', 'han_tu' => '20日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày 20, 20 ngày'],
                                ['tu_vung' => 'にじゅうよっか', 'han_tu' => '24日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày 24, 24 ngày'],
                                ['tu_vung' => '〜にち', 'han_tu' => '〜日', 'am_han' => 'NHẬT', 'phat_am' => null, 'nghia' => 'ngày - , - ngày'],
                                ['tu_vung' => 'なんにち', 'han_tu' => '何日', 'am_han' => 'HÀ NHẬT', 'phat_am' => null, 'nghia' => 'ngày mấy, mấy ngày, bao nhiêu ngày'],
                                ['tu_vung' => 'いつ', 'han_tu' => null, 'am_han' => null, 'phat_am' => null, 'nghia' => 'bao giờ, khi nào'],
                                ['tu_vung' => 'たんじょうび', 'han_tu' => '誕生日', 'am_han' => 'ĐẢN SINH NHẬT', 'phat_am' => null, 'nghia' => 'sinh nhật'],
                            ],
                            'mau_cau' => [
                                ['jp' => 'そうですね。', 'nghia' => 'Ừ, nhỉ.'],
                                ['jp' => 'どうもありがとうございました。', 'nghia' => 'Xin cảm ơn anh/chị rất nhiều.'],
                                ['jp' => 'どういたしまして', 'nghia' => 'Không có gì.'],
                            ],
                            'rail' => [
                                ['tu_vung' => 'いちばんせん', 'han_tu' => '一番線', 'nghia' => 'sân ga số -'],
                                ['tu_vung' => 'つぎの', 'han_tu' => '次の', 'nghia' => 'tiếp theo'],
                                ['tu_vung' => 'ふつう', 'han_tu' => '普通', 'nghia' => 'tàu thường (dừng cả ở các ga lẻ)'],
                                ['tu_vung' => 'きゅうこう', 'han_tu' => '急行', 'nghia' => 'tàu tốc hành'],
                                ['tu_vung' => 'とっきゅう', 'han_tu' => '特急', 'nghia' => 'tàu tốc hành đặc biệt'],
                            ],
                            'places' => [
                                ['tu_vung' => '甲子園', 'nghia' => 'tên một khu phố ở Osaka'],
                                ['tu_vung' => '大阪城', 'nghia' => 'Lâu đài Osaka, một lâu đài nổi tiếng ở Osaka'],
                            ],
                        ];
                    } else {
                        // Mẫu trống cho các bài khác
                        $content = [
                            'vocab' => [
                                [
                                    'tu_vung' => '',
                                    'han_tu' => '',
                                    'am_han' => '',
                                    'phat_am' => '',
                                    'nghia' => '',
                                ],
                            ],
                        ];
                    }
                } elseif ($sectionDef['key'] === 'ngu-phap') {
                    // Ngữ pháp: chỉ tạo tiêu đề placeholder, nội dung sẽ bổ sung sau
                    if ($i === 1) {
                        $content = [
                            [
                                'title' => 'Phần 1: Khẳng định và phủ định của một danh từ',
                                'pattern' => [
                                    'affirm' => 'N です (N desu)',
                                    'negate' => 'N ではありません (N dewa arimasen) ／ N じゃありません (N ja arimasen)'
                                ],
                                'explain' => [
                                    'affirm' => 'là ∼',
                                    'negate' => 'không phải là ∼'
                                ],
                                'notes' => [
                                    'です đi cùng danh từ để làm vị ngữ, vừa mang nghĩa phán đoán/khẳng định vừa thể hiện lịch sự.',
                                    'じゃありません dùng nhiều trong hội thoại. ではありません trang trọng (bài phát biểu, văn viết).'
                                ],
                                'examples' => [
                                    ['jp' => 'がくせいです。', 'nghia' => 'Là học sinh.'],
                                    ['jp' => 'がくせいじゃありません。', 'nghia' => 'Không phải là học sinh.'],
                                    ['jp' => 'やまだです。', 'nghia' => 'Là Yamada.'],
                                    ['jp' => 'やまだじゃありません。', 'nghia' => 'Không phải là Yamada.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 2: Trợ từ は (wa)',
                                'pattern' => '∼ は ∼',
                                'explain' => [
                                    'は đặt sau danh từ, biến danh từ đó thành chủ đề của câu.',
                                    'Dùng để giới thiệu đề tài hoặc phân cách chủ ngữ và vị ngữ.',
                                    'Lưu ý: は đọc là wa (わ) khi làm trợ từ.'
                                ],
                                'examples' => [
                                    ['jp' => 'わたしは はたちです。', 'nghia' => 'Tôi 20 tuổi.'],
                                    ['jp' => 'わたしは がくせいじゃありません。', 'nghia' => 'Tôi không phải là học sinh.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3: Câu nghi vấn',
                                'pattern' => [
                                    'base' => '∼ は N ですか。',
                                    'neg_q' => '∼ は N じゃありませんか。'
                                ],
                                'explain' => [
                                    'meaning' => '∼ phải không?',
                                    'guide' => 'Thêm か ở cuối câu để tạo câu hỏi; cuối câu thường lên giọng.'
                                ],
                                'examples' => [
                                    ['jp' => 'あなたは がくせいですか。', 'nghia' => 'Bạn là học sinh phải không?'],
                                    ['jp' => 'たなかさんは いしゃじゃありませんか。', 'nghia' => 'Anh Tanaka không phải là bác sĩ à?'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3-1: Câu hỏi xác nhận',
                                'pattern' => [
                                    'ask' => 'A: ∼ は N ですか。',
                                    'ans_yes' => 'B: はい、N です。',
                                    'ans_no' => 'B: いいえ、N じゃありません。N1 です。'
                                ],
                                'explain' => 'Hỏi xác nhận đúng/sai. Trả lời phải bắt đầu bằng はい (vâng) hoặc いいえ (không).',
                                'examples' => [
                                    ['jp' => 'A: シュミットさんは ドイツじんですか。 B: はい、ドイツじんです。', 'nghia' => 'A: Anh Schmidt là người Đức à? B: Vâng, là người Đức.'],
                                    ['jp' => 'A: あなたは じゅうはっさいですか。 B: いいえ、わたしは はたちです。', 'nghia' => 'A: Bạn 18 tuổi à? B: Không, tôi 20 tuổi.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3-2: Câu hỏi với nghi vấn từ',
                                'pattern' => [
                                    'ask' => 'A: ∼ は NVT ですか。',
                                    'ans' => 'B: N です。'
                                ],
                                'explain' => [
                                    'Nghi vấn từ thay cho nội dung cần hỏi (ai/cái gì/ở đâu/mấy tuổi...).',
                                    'Câu trả lời trả lời trực tiếp, không dùng はい/いいえ.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: あの かたは どなたですか。 B: やまださんです。', 'nghia' => 'A: Vị kia là ai? B: Là anh Yamada.'],
                                    ['jp' => 'A: あなたは なんさいですか。 B: わたしは はたちです。', 'nghia' => 'A: Bạn mấy tuổi? B: Tôi 20 tuổi.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 4: Trợ từ も',
                                'pattern' => '∼ も ∼',
                                'explain' => [
                                    'meaning' => 'Cũng ∼',
                                    'guide1' => 'も dùng khi yếu tố lặp lại so với câu trước, thay cho は.',
                                    'guide2' => 'Nếu yếu tố lặp lại bị lược bỏ (hiểu ngầm) thì も cũng lược bỏ.'
                                ],
                                'examples' => [
                                    ['jp' => 'たなかさんは じゅうはっさいです。やまださんも じゅうはっさいですか。/ いいえ、やまださんは はたちです。', 'nghia' => 'Anh Tanaka 18 tuổi. Anh Yamada cũng 18 tuổi à? Không, Yamada 20 tuổi.'],
                                    ['jp' => 'わたしは がくせいじゃありません。はらださんも がくせいじゃありません。', 'nghia' => 'Tôi không phải là học sinh. Anh Harada cũng không phải là học sinh.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 5: Trợ từ の',
                                'pattern' => 'N1 の N2',
                                'explain' => [
                                    'meaning' => 'của ...',
                                    'guide1' => 'の dùng để nối hai danh từ.',
                                    'guide2' => 'N2 là ý chính; N1 bổ nghĩa cho N2.',
                                    'guide3' => 'Trong bài này, N1 biểu thị nơi sở thuộc (thuộc về) của N2.'
                                ],
                                'examples' => [
                                    ['jp' => 'ふじだいがくの がくせいです。', 'nghia' => 'Là học sinh của trường Đại học Fuji.'],
                                    ['jp' => 'さくらだいがくの せんせいです。', 'nghia' => 'Là giáo viên của trường Đại học Sakura.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 6: Hỏi tuổi',
                                'pattern' => '∼ は （お）いくつですか ／ なんさいですか',
                                'explain' => [
                                    'meaning' => '∼ bao nhiêu tuổi?',
                                    'guide1' => 'Dùng なんさい (nansai) hoặc おいくつ (oikutsu) để hỏi tuổi.',
                                    'guide2' => 'おいくつ lịch sự hơn なんさい.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: やまだせんせいは おいくつですか。 B: やまだせんせいは よんじゅっさいです。', 'nghia' => 'A: Thầy Yamada bao nhiêu tuổi (lịch sự)? B: Thầy 40 tuổi.'],
                                    ['jp' => 'A: あなたは なんさいですか。 B: にじゅういっさいです。', 'nghia' => 'A: Bạn mấy tuổi? B: 21 tuổi.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 7: 〜さん, 〜ちゃん',
                                'pattern' => 'N さん／N ちゃん',
                                'explain' => [
                                    'さん đứng sau họ hoặc tên người nghe/người thứ ba. Không dùng sau tên chính mình.',
                                    'Với trẻ em dùng ちゃん (thân mật) thay cho さん.',
                                    'ちゃん có thể dùng cho cả bé trai và bé gái (không như くん truyền thống cho bé trai).'
                                ],
                                'examples' => [
                                    ['jp' => 'あの かたは ワットさんです。', 'nghia' => 'Vị kia là anh Watt.'],
                                    ['jp' => 'A: タワポンさんは がくせいですか。 B: はい、がくせいです。', 'nghia' => 'A: Anh Tawapon là học sinh phải không? B: Vâng, là học sinh.'],
                                ],
                            ],
                        ];
                    } elseif ($i === 2) {
                        // Bài 2 - Ngữ pháp
                        $content = [
                            [
                                'title' => 'Phần 1: Câu hỏi xác nhận 〜 は N ですか',
                                'pattern' => [
                                    'ask' => 'A: 〜 は N ですか。',
                                    'ans_yes' => 'B: はい、N です。／はい、そうです。',
                                    'ans_no' => 'B: いいえ、N じゃありません。／いいえ、N1 です。／いいえ、ちがいます。'
                                ],
                                'explain' => [
                                    'Có thể trả lời “はい、そうです。” thay cho “はい、N です。”',
                                    'Có thể trả lời “いいえ、ちがいます。” hoặc “いいえ、N1 です。” thay cho “いいえ、N じゃありません。”'
                                ],
                                'examples' => [
                                    ['jp' => 'A: これは しんぶんですか。 B: はい、そうです。', 'nghia' => 'A: Đây là báo à? B: Vâng, đúng vậy.'],
                                    ['jp' => 'A: それは シャープペンシルですか。 B: いいえ、ちがいます。', 'nghia' => 'A: Đó là bút chì kim à? B: Không, không phải.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 2: Câu hỏi lựa chọn 〜 か、〜 か',
                                'pattern' => [
                                    'ask' => 'A: 〜 は N1 ですか、N2 ですか。',
                                    'answer' => 'B: N1 です。／N2 です。'
                                ],
                                'explain' => [
                                    'Dạng câu hỏi lựa chọn giữa N1 hay N2.',
                                    'Câu trả lời chọn một phương án, không dùng はい／いいえ.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: これは しんぶんですか、ざっしですか。 B: しんぶんです。', 'nghia' => 'A: Đây là báo hay tạp chí? B: Là báo.'],
                                    ['jp' => 'A: あなたは じゅうはっさいですか、はたちですか。 B: わたしは はたちです。', 'nghia' => 'A: Bạn 18 hay 20 tuổi? B: Tôi 20 tuổi.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3: これ・それ・あれ và この・その・あの',
                                'classify' => [
                                    'pronouns' => [
                                        ['form' => 'これ', 'meaning' => 'cái này, đây', 'usage' => 'Chỉ dùng cho vật, gần người nói.'],
                                        ['form' => 'それ', 'meaning' => 'cái đó, đó', 'usage' => 'Chỉ dùng cho vật, xa người nói, gần người nghe.'],
                                        ['form' => 'あれ', 'meaning' => 'cái kia, kia', 'usage' => 'Chỉ dùng cho vật, xa cả hai phía.']
                                    ],
                                    'modifiers' => [
                                        ['form' => 'この N', 'meaning' => 'N này', 'usage' => 'Dùng cho người/vật, luôn đi với danh từ, gần người nói.'],
                                        ['form' => 'その N', 'meaning' => 'N đó', 'usage' => 'Dùng cho người/vật, luôn đi với danh từ, gần người nghe.'],
                                        ['form' => 'あの N', 'meaning' => 'N kia', 'usage' => 'Dùng cho người/vật, luôn đi với danh từ, xa cả hai.']
                                    ]
                                ],
                                'examples' => [
                                    ['jp' => 'これは かさです。', 'nghia' => 'Đây là cái ô.'],
                                    ['jp' => 'あれは たなかさんの くるまです。', 'nghia' => 'Kia là ô tô của anh Tanaka.'],
                                    ['jp' => 'あの かたは やまだせんせいです。', 'nghia' => 'Vị kia là giáo viên Yamada.'],
                                    ['jp' => 'この ほんは にほんごの ほんじゃありません。', 'nghia' => 'Quyển sách này không phải sách tiếng Nhật.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 4: N2 の N1 - Nghi vấn từ なん',
                                'pattern' => [
                                    'ask' => 'A: 〜 は なん の N1 ですか。',
                                    'ans' => 'B: 〜 は N2 の N1 です。'
                                ],
                                'explain' => [
                                    'meaning_q' => 'N1 về cái gì?',
                                    'meaning_a' => 'N1 về N2',
                                    'guide1' => 'なんの N1: hỏi về tính chất/chủ đề/loại hình của N1.',
                                    'guide2' => 'N2 thường là từ chỉ tính chất/chủng loại.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: これは なん の ほんですか。 B: にほんごの ほんです。', 'nghia' => 'A: Đây là sách về gì? B: Sách về tiếng Nhật.'],
                                    ['jp' => 'やまださんは おとこの せんせいです。', 'nghia' => 'Anh Yamada là giáo viên nam.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 5: N2 の N1 - Nghi vấn từ だれ',
                                'pattern' => [
                                    'ask' => 'A: 〜 は だれ の N1 ですか。',
                                    'ans' => 'B: 〜 は N2 の N1 です。'
                                ],
                                'explain' => [
                                    'meaning_q' => 'N1 của ai?',
                                    'meaning_a' => 'N1 của N2',
                                    'guide1' => 'だれ の dùng để hỏi về sở hữu.',
                                    'guide2' => 'N2 thường là từ chỉ người (người sở hữu).'
                                ],
                                'examples' => [
                                    ['jp' => 'A: これは だれ の ほんですか。 B: わたしの ほんです。', 'nghia' => 'A: Đây là sách của ai? B: Của tôi.'],
                                    ['jp' => 'あれは たなかさんの じしょです。', 'nghia' => 'Kia là từ điển của anh Tanaka.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 6: N2 の N1 - Lược bỏ N1',
                                'pattern' => [
                                    'base' => 'N2 の N1 です。',
                                    'omit_thing' => '○ N2 の です。 (khi N1 là danh từ chỉ vật)',
                                    'not_person' => '× N2 の です。 (khi N1 là danh từ chỉ người)'
                                ],
                                'explain' => [
                                    'meaning' => 'Của N2 (sở hữu/tính chất).',
                                    'guide1' => 'Có thể lược N1 khi N1 đã xuất hiện trước đó và là danh từ chỉ vật.',
                                    'guide2' => 'Trợ từ の KHÔNG thay cho danh từ chỉ người.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: あれは だれ の ほんですか。 B: ミラーさんの です。', 'nghia' => 'A: Kia là sách của ai? B: Của anh Miller.'],
                                    ['jp' => 'A: このかばんは あなたの ですか。 B: いいえ、わたしのじゃ ありません。', 'nghia' => 'A: Cái cặp này là của bạn? B: Không, không phải của tôi.'],
                                    ['jp' => 'A: ミラーさんは IMC の しゃいんですか。 B: はい、IMC の しゃいんです。', 'nghia' => 'A: Anh Miller là nhân viên IMC chứ? B: Vâng, là nhân viên IMC. (Không thể nói “IMC の です”.)'],
                                ],
                            ],
                            [
                                'title' => 'Phần 7: そうですか',
                                'pattern' => 'そうですか',
                                'explain' => [
                                    'meaning' => 'Thế à/ Vậy à.',
                                    'guide1' => 'Dùng khi người nói tiếp nhận thông tin và bày tỏ đã hiểu.',
                                    'guide2' => 'Tuy có か nhưng không phải câu hỏi; là câu cảm thán/xác nhận.',
                                    'guide3' => 'Thường hạ giọng cuối câu (không lên giọng như câu hỏi).'
                                ],
                                'examples' => [
                                    ['jp' => 'A: この かさ は あなたのですか。 B: いいえ、ちがいます。さとうさんの です。 A: そうですか。', 'nghia' => 'A: Cái ô này là của bạn à? B: Không, là của cô Sato. A: Thế à.'],
                                ],
                            ],
                        ];
                    } elseif ($i === 3) {
                        // Bài 3 - Ngữ pháp
                        $content = [
                            [
                                'title' => 'Phần 1: ここ、そこ、あそこ - Chỗ này, chỗ đó, chỗ kia',
                                'classify' => [
                                    'normal' => [
                                        ['form' => 'ここ・そこ・あそこ', 'meaning' => 'Nơi này/chỗ này; nơi đó/chỗ đó; nơi kia/chỗ kia', 'usage' => 'Dùng để chỉ nơi chốn.'],
                                    ],
                                    'polite' => [
                                        ['form' => 'こちら・そちら・あちら', 'meaning' => 'Đằng này/phía này; đằng đó/phía đó; đằng kia/phía kia', 'usage' => 'Cách nói lịch sự của ここ/そこ/あそこ; cũng dùng chỉ phương hướng.'],
                                    ],
                                    'question' => [
                                        ['form' => 'どこ／どちら', 'meaning' => 'Ở đâu? / Ở đằng nào/phía nào?', 'usage' => 'どちら là cách nói lịch sự của どこ.'],
                                    ],
                                ],
                                'examples' => [
                                    ['jp' => 'ここは かいぎしつです。', 'nghia' => 'Đây là phòng họp.'],
                                    ['jp' => 'あちらは びょういんです。', 'nghia' => 'Đằng kia là bệnh viện.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 2: Mẫu câu chỉ nơi chốn',
                                'patterns' => [
                                    ['form' => 'ここは [Địa điểm] です。', 'meaning' => 'Nơi này là [Địa điểm].'],
                                    ['form' => '[Địa điểm] は ここ です。', 'meaning' => '[Địa điểm] là ở đây/chỗ này.'],
                                    ['form' => 'どこ', 'meaning' => '[Địa điểm] ở đâu?'],
                                ],
                                'examples' => [
                                    ['jp' => 'ここは わたしの うちです。', 'nghia' => 'Đây là nhà của tôi.'],
                                    ['jp' => 'わたしの うちは ここ です。', 'nghia' => 'Nhà của tôi là ở đây.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3: Câu hỏi với nghi vấn từ chỉ nơi chốn',
                                'patterns' => [
                                    ['form' => 'A: 〜 は どこですか。 B: 〜 は ここ／そこ／あそこです。', 'meaning' => '∼ ở đâu?'],
                                    ['form' => 'A: 〜 は どちらですか。 B: 〜 は こちら／そちら／あちらです。', 'meaning' => '∼ ở đằng nào? (lịch sự)'],
                                ],
                                'examples' => [
                                    ['jp' => 'A: かいだんは どこですか。 B: そこです。', 'nghia' => 'A: Cầu thang ở đâu? B: Ở đó.'],
                                    ['jp' => 'A: すみません、うけつけは どちらですか。 B: あちらです。', 'nghia' => 'A: Xin lỗi, quầy lễ tân ở đằng nào? B: Ở đằng kia.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 4: Nghi vấn từ どちら',
                                'pattern' => 'A: 〜 は どちらですか。 B: 〜 は N です。',
                                'explain' => [
                                    'Hỏi về ∼ ở đâu/đằng nào (lịch sự).',
                                    'Ý nghĩa chính: 1) hỏi nơi chốn; 2) hỏi phương hướng; 3) hỏi tên nước, trường, công ty (tên riêng).',
                                    'Khi hỏi tên nước/đơn vị, câu trả lời thường là tên riêng tương ứng.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: おくには どちらですか。 B: わたしの くには ベトナムです。', 'nghia' => 'A: Đất nước của bạn là nước nào? B: Việt Nam.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 5: Hệ thống đại từ chỉ thị こそあど',
                                'table' => [
                                    'headers' => ['Phân loại','Nhóm こ (Gần người nói)','Nhóm そ (Gần người nghe)','Nhóm あ (Xa cả 2)','Nhóm ど (Nghi vấn)'],
                                    'rows' => [
                                        ['Đồ vật','これ (cái này)','それ (cái đó)','あれ (cái kia)','どれ (cái nào)'],
                                        ['Đồ vật/Người','この N (N này)','その N (N đó)','あの N (N kia)','どの N (N nào)'],
                                        ['Địa điểm','ここ (ở đây)','そこ (ở đó)','あそこ (ở kia)','どこ (ở đâu)'],
                                        ['Phương hướng/Địa điểm (lịch sự)','こちら (đằng này)','そちら (đằng đó)','あちら (đằng kia)','どちら (đằng nào)'],
                                    ]
                                ],
                            ],
                            [
                                'title' => 'Phần 6: N2 の N1 - Nghi vấn từ どこ',
                                'pattern' => [
                                    'ask' => 'A: 〜 は どこ の N ですか。',
                                    'ans' => 'B: 〜 は N1 の N です。'
                                ],
                                'explain' => [
                                    'meaning' => 'Hỏi xuất xứ của N (N là của/ở đâu).',
                                    'guide1' => 'どこ の N: hỏi về nơi chốn/xuất xứ.',
                                    'guide2' => 'N1 thường là quốc gia/vùng miền.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: これは どこ の ワインですか。 B: フランスの ワインです。', 'nghia' => 'A: Đây là rượu vang của nước nào? B: Của Pháp.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 7: Hỏi và đếm tầng, tòa nhà',
                                'pattern' => 'A: 〜 は なんがいですか。 B: 〜 は 〜 がい／かい です。',
                                'explain' => [
                                    'meaning' => 'Hỏi tầng mấy.',
                                    'guide1' => 'Tầng trệt đếm là いっかい (1F).',
                                    'guide2' => 'Tầng hầm dùng ちか trước số tầng (ví dụ: ちかいっかい).'
                                ],
                                'examples' => [
                                    ['jp' => 'A: とけいうりばは なんがいですか。 B: さんがいです。', 'nghia' => 'A: Quầy đồng hồ ở tầng mấy? B: Tầng 3.'],
                                    ['jp' => 'A: ほんやは なんがいですか。 B: ちか にかいです。', 'nghia' => 'A: Hiệu sách ở tầng mấy? B: Tầng hầm 2.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 8: Hỏi giá cả',
                                'pattern' => 'A: 〜 は いくらですか。 B: 〜 は 〜 えん（／ドル／ドン）です。',
                                'explain' => [
                                    'meaning' => 'いくら: giá bao nhiêu?',
                                    'guide1' => 'Khi trả lời, dùng số đếm + đơn vị tiền tệ.',
                                    'guide2' => 'VD: 80.000 đồng → はちまん ドン'
                                ],
                                'examples' => [
                                    ['jp' => 'A: この くつは いくらですか。 B: はっぴゃくえんです。', 'nghia' => 'A: Đôi giày này bao nhiêu? B: 800 yên.'],
                                ],
                            ],
                        ];
                    } elseif ($i === 4) {
                        // Bài 4 - Ngữ pháp
                        $content = [
                            [
                                'title' => 'Phần 1: Học đếm giờ, đếm phút',
                                'patterns' => [
                                    ['form' => '〜じ (ji)', 'meaning' => 'cách đếm giờ'],
                                    ['form' => '〜ふん／〜ぷん (fun/pun)', 'meaning' => 'cách đếm phút'],
                                    ['form' => 'なんじ／なんぷん', 'meaning' => 'mấy giờ? / mấy phút?'],
                                ],
                                'notes' => [
                                    'はん: ∼ giờ rưỡi (にじはん: 2 giờ rưỡi).',
                                    'Đọc đặc biệt: いちじ, しちじ, くじ (giờ); いっぷん, ろっぷん, はっぷん, じゅっぷん/じっぷん (phút).'
                                ],
                                'examples' => [
                                    ['jp' => 'A: いま なんじ なんぷんですか。 B: しちじ さんじゅっぷんです。しちじ はんです。', 'nghia' => 'A: Bây giờ mấy giờ mấy phút? B: 7 giờ 30 phút. 7 giờ rưỡi.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 2: Động từ chia ở quá khứ, hiện tại, tương lai',
                                'table' => [
                                    'headers' => ['Thể','Quá khứ','Hiện tại & Tương lai'],
                                    'rows' => [
                                        ['Khẳng định (KĐ)','Vました','Vます'],
                                        ['Phủ định (PĐ)','Vませんでした','Vません'],
                                    ]
                                ],
                                'explain' => [
                                    'Thể Vます dùng cho hiện tại (thói quen/sự thật) và tương lai.',
                                    'Thể Vました dùng cho hành động đã xảy ra và kết thúc trong quá khứ.'
                                ],
                                'examples' => [
                                    ['jp' => 'はたらきます。', 'nghia' => 'Làm việc. (HT/TL - khẳng định)'],
                                    ['jp' => 'べんきょうしません。', 'nghia' => 'Không học. (HT/TL - phủ định)'],
                                    ['jp' => 'はたらきました。', 'nghia' => 'Đã làm việc. (QK - khẳng định)'],
                                    ['jp' => 'べんきょうしませんでした。', 'nghia' => 'Đã không học. (QK - phủ định)'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3: Trợ từ に',
                                'pattern' => '[Thời điểm xác định] に V',
                                'explain' => [
                                    'に: lúc ... (chỉ thời điểm xác định của hành động).',
                                    'Dùng với danh từ thời gian có con số (giờ/ngày/tháng/năm cụ thể).',
                                    'Không dùng với thời gian không có con số cụ thể: hôm qua, ngày mai, sáng nay, mỗi ngày, thứ bảy...'
                                ],
                                'examples' => [
                                    ['jp' => 'A: まいあさ なんじ に おきますか。 B: 6じ に おきます。', 'nghia' => 'A: Hàng sáng dậy lúc mấy giờ? B: 6 giờ.'],
                                    ['jp' => 'A: まいばん なんじ に ねますか。 B: 11じ に ねます。', 'nghia' => 'A: Hàng tối ngủ lúc mấy giờ? B: 11 giờ.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 4: 〜から 〜まで (Kara... Made) - Từ 〜 đến 〜',
                                'pattern' => [
                                    'noun_end' => '[N] は 〜 から 〜 まで です。',
                                    'verb_end' => '[N] は 〜 から 〜 まで V。'
                                ],
                                'explain' => [
                                    'から: từ; まで: đến.',
                                    'から biểu thị điểm bắt đầu, まで biểu thị điểm kết thúc (thời gian hoặc địa điểm).',
                                    'から và まで có thể dùng riêng, không nhất thiết đi kèm động từ.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: あなたは まいしゅう なんようびから なんようびまで べんきょうしますか。 B: げつようびから きんようびまで べんきょうします。', 'nghia' => 'A: Bạn học từ thứ mấy đến thứ mấy hàng tuần? B: Từ thứ Hai đến thứ Sáu.'],
                                    ['jp' => 'ぎんこうは 9じから です。', 'nghia' => 'Ngân hàng là từ 9 giờ.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 5: N1 と N2 − N1 và N2',
                                'pattern' => 'N1 と N2',
                                'explain' => [
                                    'Trợ từ と nối hai danh từ đồng cách mang nghĩa "và" (liệt kê đầy đủ).'
                                ],
                                'examples' => [
                                    ['jp' => 'ぎんこうの やすみは どようび と にちようびです。', 'nghia' => 'Ngày nghỉ của ngân hàng là thứ Bảy và Chủ Nhật.'],
                                    ['jp' => 'A: としょかんは なんがいですか。 B: 3がい と 4がいです。', 'nghia' => 'A: Thư viện ở tầng mấy? B: Tầng 3 và tầng 4.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 6: 〜ね (ne) - 〜 nhỉ',
                                'pattern' => '... ね。',
                                'explain' => [
                                    'ね ở cuối câu thể hiện kỳ vọng sự đồng ý của người nghe, hoặc xác nhận/nhắc lại thông tin cả hai đã biết.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: まいにち 10じまで はたらきます。 B: たいへんですね。', 'nghia' => 'A: Ngày nào tôi cũng làm đến 10 giờ. B: Vất vả nhỉ.'],
                                    ['jp' => 'B: 871 の 6813 ですね。', 'nghia' => '871 6813 nhỉ. (xác nhận số điện thoại)'],
                                ],
                            ],
                            [
                                'title' => 'Phần 7: Hỏi số điện thoại',
                                'pattern' => 'A: 〜 は なんばん ですか。 B: 〜 は 〜 です。',
                                'explain' => [
                                    'なんばん: số mấy?',
                                    'Đọc số điện thoại theo từng số; với số dài, tách thành cụm bằng の giữa các cụm.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: としょかんの でんわばんごうは なんばんですか。 B: ゼロはちの ろくななはちの ななはちきゅうごです。', 'nghia' => 'A: Số điện thoại của thư viện là số mấy? B: 08-678-7895.'],
                                ],
                            ],
                        ];
                    } elseif ($i === 5) {
                        // Bài 5 - Ngữ pháp
                        $content = [
                            [
                                'title' => 'Phần 1: Động từ đi, đến, trở về (いきます・きます・かえります)',
                                'table' => [
                                    'headers' => ['Động từ','Ý nghĩa','Cách dùng'],
                                    'rows' => [
                                        ['いきます (ikimasu)','đi','Chỉ hành động rời khỏi vị trí người nói.'],
                                        ['きます (kimasu)','đến','Dùng tại nơi người nói đang có mặt; di chuyển tới vị trí người nói.'],
                                        ['かえります (kaerimasu)','về','Trở về nơi thân thuộc: nhà, quê, đất nước.'],
                                    ]
                                ],
                            ],
                            [
                                'title' => 'Phần 2: Trợ từ へ (e) - Chỉ phương hướng',
                                'pattern' => '[Địa điểm] へ いきます／きます／かえります',
                                'explain' => [
                                    'へ chỉ phương hướng (đích đến) của hành động di chuyển.',
                                    'Khi là trợ từ, へ đọc là “e”. Thường đi với いきます・きます・かえります.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: きのう どこ へ いきましたか。 B: としょかん へ いきました。', 'nghia' => 'A: Hôm qua đã đi đâu? B: Đã đi thư viện.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 3: Trợ từ も trong nhấn mạnh phủ định',
                                'pattern' => 'A: 〜 どこへ Vますか。 B: いいえ、どこ(へ) も Vません。',
                                'explain' => [
                                    'Dùng も với nghi vấn từ (どこ／だれ／いつ...) để nhấn mạnh “cũng không ...”.',
                                    'Trong trường hợp này, も thay cho へ/は/が.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: こんしゅうの にちようび どこへ いきますか。 B: いいえ、どこ も いきません。', 'nghia' => 'A: Chủ nhật này đi đâu? B: Không, tôi cũng không đi đâu.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 4: Phương tiện で (de)',
                                'pattern' => '[Phương tiện] で いきます／きます／かえります',
                                'explain' => [
                                    'なんで: bằng gì/bằng cách nào?',
                                    'で chỉ phương tiện/cách thức. Với あるいて (đi bộ) thì không dùng で.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: まいにち なんで がっこうへ いきますか。 B: じてんしゃ で いきます。', 'nghia' => 'A: Hàng ngày đi học bằng gì? B: Bằng xe đạp.'],
                                    ['jp' => 'A: こんばん なんで うち へ かえりますか。 B: あるいて かえります。', 'nghia' => 'A: Tối nay về nhà bằng gì? B: Đi bộ về.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 5: Trợ từ と (to) - Cùng với',
                                'pattern' => '[Người] と いきます／きます／かえります',
                                'explain' => [
                                    'だれと: cùng với ai?',
                                    'と chỉ hành động cùng với người/động vật. Nếu một mình dùng ひとりで.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: こんしゅうの にちようび、だれ と こうえんへ いきますか。 B: かぞく と いきます。', 'nghia' => 'A: Chủ nhật này đi công viên với ai? B: Đi cùng gia đình.'],
                                    ['jp' => 'A: まいにち だれ と がっこうへ いきますか。 B: ひとりで いきます。', 'nghia' => 'A: Hàng ngày đi học với ai? B: Đi một mình.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 6: Trạng từ chỉ thời gian (ôn tập)',
                                'table' => [
                                    'headers' => ['Phân loại','Quá khứ','Hiện tại','Tương lai'],
                                    'rows' => [
                                        ['Ngày','おととい, きのう','きょう, いま','あした, あさって'],
                                        ['Tuần','せんせんしゅう, せんしゅう','こんしゅう','らいしゅう, さらいしゅう'],
                                        ['Tháng','せんせんげつ, せんげつ','こんげつ','らいげつ, さらいげつ'],
                                        ['Năm','おととし, きょねん','ことし','らいねん, さらいねん'],
                                    ]
                                ],
                            ],
                            [
                                'title' => 'Phần 7: Đếm ngày, đếm tháng (ôn tập)',
                                'patterns' => [
                                    ['form' => '〜がつ (tháng)', 'meaning' => 'Cách đếm tháng'],
                                    ['form' => '〜にち (ngày)', 'meaning' => 'Cách đếm ngày'],
                                    ['form' => 'なんがつ／なんにち', 'meaning' => 'Tháng mấy? / Ngày mấy?']
                                ],
                                'notes' => [
                                    'Đọc đặc biệt: しがつ, しちがつ, くがつ; ついたち, ふつか, みっか, よっか ... とうか, はつか.',
                                    'Thứ tự khi nói: Năm/Tháng/Ngày/Thứ.'
                                ],
                            ],
                            [
                                'title' => 'Phần 8: Nghi vấn từ hỏi thời gian いつ',
                                'pattern' => 'A: 〜 は いつ Vますか。 B: 〜 は [Thời gian] Vます。',
                                'explain' => [
                                    'いつ: khi nào/bao giờ?',
                                    'Lưu ý: いつ không đi kèm に.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: あなたの たんじょうび は いつ ですか。 B: しちがつ はつかです。', 'nghia' => 'A: Sinh nhật của bạn khi nào? B: Ngày 20 tháng 7.'],
                                    ['jp' => 'A: いつ にほんへ いきますか。 B: ことしの しがつ に にほんへ いきます。', 'nghia' => 'A: Khi nào đi Nhật? B: Tháng 4 năm nay.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 9: Trợ từ 〜よ (yo) - Đấy/ấy/cơ',
                                'pattern' => '... よ。',
                                'explain' => [
                                    'よ nhấn mạnh thông tin mới với người nghe.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: この でんしゃは こうしえんへ いきますか。 B: いいえ、いきません。つぎの 「ふつう」 です よ。', 'nghia' => 'A: Tàu này đi Koshien không? B: Không, tàu tiếp theo là tàu thường đấy.'],
                                ],
                            ],
                            [
                                'title' => 'Phần 10: そうですね - Thế nhỉ, vậy nhỉ',
                                'pattern' => 'そうですね',
                                'explain' => [
                                    'Biểu thị đồng ý/đồng cảm với điều đối phương nói (thông tin cả hai biết).',
                                    'Phân biệt với そうですか (Bài 2) dùng khi tiếp nhận thông tin mới.'
                                ],
                                'examples' => [
                                    ['jp' => 'A: あしたは やすみです ね。 B: あ、そうですね。', 'nghia' => 'A: Ngày mai là ngày nghỉ nhỉ. B: À, đúng rồi.'],
                                ],
                            ],
                        ];
                    } else {
                        $content = [];
                    }
                } elseif ($sectionDef['key'] === 'luyen-doc') {
                    if ($i === 1) {
                        $content = [
                            'sentences' => [
                                'わたしは マイク・ミラーです。',
                                'サントスさんは 学生じゃありません。',
                                'ミラーさんは 会社員ですか。',
                                'サントスさんも 会社員です。',
                            ],
                        ];
                    } elseif ($i === 2) {
                        $content = [
                            'sentences' => [
                                'これは 辞書です。',
                                'それは わたしの 傘です。',
                                'この 本は わたしのです。',
                            ],
                        ];
                    } elseif ($i === 3) {
                        $content = [
                            'sentences' => [
                                'ここは 食堂です。',
                                'エレベーターは あそこです。',
                            ],
                        ];
                    } elseif ($i === 4) {
                        $content = [
                            'sentences' => [
                                '今 4時5分です。',
                                'わたしは 毎朝 6時に 起きます。',
                                'わたしは きのう 勉強しました。',
                            ],
                        ];
                    } elseif ($i === 5) {
                        $content = [
                            'sentences' => [
                                'わたしは 京都へ 行きます。',
                                'わたしは タクシーで うちへ 帰ります。',
                                'わたしは 家族と 日本へ 来ました。',
                            ],
                        ];
                    } else {
                        $content = [];
                    }
                } elseif ($sectionDef['key'] === 'hoi-thoai') {
                    if ($i === 1) {
                        $content = [
                            'dialogue' => [
                                ['speaker' => '佐藤', 'romaji' => 'Satō', 'jp' => 'おはよう ございます。'],
                                ['speaker' => '山田', 'romaji' => 'Yamada', 'jp' => 'おはよう ございます。'],
                                ['speaker' => '佐藤', 'romaji' => 'Satō', 'jp' => '佐藤さん、こちらは マイク・ミラーさんです。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => '初めまして。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'マイク・ミラーです。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'アメリカから 来ました。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'どうぞ よろしく。'],
                                ['speaker' => '佐藤', 'romaji' => 'Satō', 'jp' => '佐藤けい子です。'],
                                ['speaker' => '佐藤', 'romaji' => 'Satō', 'jp' => 'どうぞ よろしく。'],
                            ],
                        ];
                    } elseif ($i === 2) {
                        $content = [
                            'dialogue' => [
                                ['speaker' => '山田一郎', 'romaji' => 'Yamada Ichirō', 'jp' => 'はい。どなたですか。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => '408の サントスです。'],
                                ['speaker' => '—', 'romaji' => null, 'jp' => '-----------'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'こんにちは。サントスです。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'これから お世話に なります。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'どうぞ よろしく お願いします。'],
                                ['speaker' => '山田一郎', 'romaji' => 'Yamada Ichirō', 'jp' => 'こちらこそ よろしく お願いします。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'あのう、これ、コーヒーです。どうぞ。'],
                                ['speaker' => '山田一郎', 'romaji' => 'Yamada Ichirō', 'jp' => 'どうも ありがとう ございます。'],
                            ],
                        ];
                    } elseif ($i === 3) {
                        $content = [
                            'dialogue' => [
                                ['speaker' => '店員A', 'romaji' => 'Tenin A', 'jp' => 'いらっしゃいませ。'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'すみません。ワインうりばは どこですか。'],
                                ['speaker' => '店員A', 'romaji' => 'Tenin A', 'jp' => 'ちか1かいで ございます。'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'どうも'],
                                ['speaker' => '—', 'romaji' => null, 'jp' => '-----------'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'すみません。その ワインを みせて ください。'],
                                ['speaker' => '店員B', 'romaji' => 'Tenin B', 'jp' => 'はい、どうぞ。'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'これは どこの ワインですか。'],
                                ['speaker' => '店員B', 'romaji' => 'Tenin B', 'jp' => '日本のです。'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'いくらですか。'],
                                ['speaker' => '店員B', 'romaji' => 'Tenin B', 'jp' => '2,500えんです。'],
                                ['speaker' => 'マリア', 'romaji' => 'Maria', 'jp' => 'じゃ、これを ください。'],
                            ],
                        ];
                    } elseif ($i === 4) {
                        $content = [
                            'dialogue' => [
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'すみません。「あすか」の 電話番号は 何番ですか。'],
                                ['speaker' => '佐藤', 'romaji' => 'Satō', 'jp' => '「あすか」ですか。5275の 2725です。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'どうも ありがとう ございます。'],
                                ['speaker' => '—', 'romaji' => null, 'jp' => '-----------'],
                                ['speaker' => '店の 人', 'romaji' => 'Mise no hito', 'jp' => '「あすか」です。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'すみません。そちらは 何時までですか。'],
                                ['speaker' => '店の 人', 'romaji' => 'Mise no hito', 'jp' => '10じまでです。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => '休みは 何曜日ですか。'],
                                ['speaker' => '店の 人', 'romaji' => 'Mise no hito', 'jp' => '日曜日です。'],
                                ['speaker' => 'ミラー', 'romaji' => 'Miraa', 'jp' => 'そうですか。どうも。'],
                            ],
                        ];
                    } elseif ($i === 5) {
                        $content = [
                            'dialogue' => [
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'すみません。甲子園までいくらですか。'],
                                ['speaker' => '女の人', 'romaji' => 'Onna no hito', 'jp' => '350円です。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => '350円ですね。ありがとうございました。'],
                                ['speaker' => '女の人', 'romaji' => 'Onna no hito', 'jp' => 'どういたしまして。'],
                                ['speaker' => '—', 'romaji' => null, 'jp' => '-----------'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'すみません。甲子園は何番線ですか。'],
                                ['speaker' => '駅員', 'romaji' => 'Ekiin', 'jp' => '5番線です。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'どうも。'],
                                ['speaker' => '—', 'romaji' => null, 'jp' => '-----------'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'あのう、この電車は甲子園へ行きますか。'],
                                ['speaker' => '男の人', 'romaji' => 'Otoko no hito', 'jp' => 'いいえ、次の「普通」ですよ。'],
                                ['speaker' => 'サントス', 'romaji' => 'Santosu', 'jp' => 'そうですか。どうも。'],
                            ],
                        ];
                    } else {
                        $content = [];
                    }
                } elseif ($sectionDef['key'] === 'han-tu') {
                    // Hán tự (Kanji) - cấu trúc 3 phần như ảnh: ký tự + nghĩa + cách đọc
                    if ($i === 1) {
                        // Dữ liệu hán tự cho Bài 1
                        $content = [
                            [
                                'kanji' => '人',
                                'stroke_asset' => null,
                                'han_viet' => 'NHÂN',
                                'nghia_vi' => 'người',
                                'tu_vung' => 'あのひと',
                                'kunyomi' => ['ひと'],
                                'onyomi' => ['ジン', 'ニン'],
                            ],
                            [
                                'kanji' => '方',
                                'stroke_asset' => null,
                                'han_viet' => 'PHƯƠNG',
                                'nghia_vi' => 'phương hướng; vị (cách nói lịch sự)',
                                'tu_vung' => 'あのかた',
                                'kunyomi' => ['かた'],
                                'onyomi' => ['ホウ'],
                            ],
                            [
                                'kanji' => '人',
                                'stroke_asset' => null,
                                'han_viet' => 'NHÂN',
                                'nghia_vi' => 'người (nước)',
                                'tu_vung' => '～じん',
                                'kunyomi' => ['ひと'],
                                'onyomi' => ['ジン', 'ニン'],
                            ],
                            [
                                'kanji' => '先生',
                                'stroke_asset' => null,
                                'han_viet' => 'TIÊN SINH',
                                'nghia_vi' => 'thầy / cô',
                                'tu_vung' => 'せんせい',
                                'kunyomi' => [],
                                'onyomi' => ['センセイ'],
                            ],
                            [
                                'kanji' => '教師',
                                'stroke_asset' => null,
                                'han_viet' => 'GIÁO SƯ',
                                'nghia_vi' => 'giáo viên',
                                'tu_vung' => 'きょうし',
                                'kunyomi' => [],
                                'onyomi' => ['キョウシ'],
                            ],
                            [
                                'kanji' => '学生',
                                'stroke_asset' => null,
                                'han_viet' => 'HỌC SINH',
                                'nghia_vi' => 'học sinh, sinh viên',
                                'tu_vung' => 'がくせい',
                                'kunyomi' => [],
                                'onyomi' => ['ガクセイ'],
                            ],
                            [
                                'kanji' => '会社員',
                                'stroke_asset' => null,
                                'han_viet' => 'HỘI XÃ VIÊN',
                                'nghia_vi' => 'nhân viên công ty',
                                'tu_vung' => 'かいしゃいん',
                                'kunyomi' => [],
                                'onyomi' => ['カイシャイン'],
                            ],
                            [
                                'kanji' => '社員',
                                'stroke_asset' => null,
                                'han_viet' => 'XÃ VIÊN',
                                'nghia_vi' => 'nhân viên (công ty)',
                                'tu_vung' => 'しゃいん',
                                'kunyomi' => [],
                                'onyomi' => ['シャイン'],
                            ],
                            [
                                'kanji' => '銀行員',
                                'stroke_asset' => null,
                                'han_viet' => 'NGÂN HÀNG VIÊN',
                                'nghia_vi' => 'nhân viên ngân hàng',
                                'tu_vung' => 'ぎんこういん',
                                'kunyomi' => [],
                                'onyomi' => ['ギンコウイン'],
                            ],
                            [
                                'kanji' => '医者',
                                'stroke_asset' => null,
                                'han_viet' => 'Y GIẢ',
                                'nghia_vi' => 'bác sĩ',
                                'tu_vung' => 'いしゃ',
                                'kunyomi' => [],
                                'onyomi' => ['イシャ'],
                            ],
                            [
                                'kanji' => '研究者',
                                'stroke_asset' => null,
                                'han_viet' => 'NGHIÊN CỨU GIẢ',
                                'nghia_vi' => 'nhà nghiên cứu',
                                'tu_vung' => 'けんきゅうしゃ',
                                'kunyomi' => [],
                                'onyomi' => ['ケンキュウシャ'],
                            ],
                            [
                                'kanji' => '大学',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐẠI HỌC',
                                'nghia_vi' => 'đại học, trường đại học',
                                'tu_vung' => 'だいがく',
                                'kunyomi' => [],
                                'onyomi' => ['ダイガク'],
                            ],
                            [
                                'kanji' => '病院',
                                'stroke_asset' => null,
                                'han_viet' => 'BỆNH VIỆN',
                                'nghia_vi' => 'bệnh viện',
                                'tu_vung' => 'びょういん',
                                'kunyomi' => [],
                                'onyomi' => ['ビョウイン'],
                            ],
                            [
                                'kanji' => '歳',
                                'stroke_asset' => null,
                                'han_viet' => 'TUẾ',
                                'nghia_vi' => 'tuổi',
                                'tu_vung' => '～さい',
                                'kunyomi' => [],
                                'onyomi' => ['サイ', 'セイ'],
                            ],
                            [
                                'kanji' => '何歳',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ TUẾ',
                                'nghia_vi' => 'mấy tuổi / bao nhiêu tuổi',
                                'tu_vung' => 'なんさい（おいくつ）',
                                'kunyomi' => [],
                                'onyomi' => ['ナンサイ'],
                            ],
                        ];
                    } elseif ($i === 2) {
                        // Dữ liệu hán tự cho Bài 2
                        $content = [
                            [
                                'kanji' => '本',
                                'stroke_asset' => null,
                                'han_viet' => 'BẢN/BỔN',
                                'nghia_vi' => 'sách',
                                'tu_vung' => 'ほん',
                                'kunyomi' => ['もと'],
                                'onyomi' => ['ホン'],
                            ],
                            [
                                'kanji' => '辞書',
                                'stroke_asset' => null,
                                'han_viet' => 'TỪ THƯ',
                                'nghia_vi' => 'từ điển',
                                'tu_vung' => 'じしょ',
                                'kunyomi' => [],
                                'onyomi' => ['ジショ'],
                            ],
                            [
                                'kanji' => '雑誌',
                                'stroke_asset' => null,
                                'han_viet' => 'TẠP CHÍ',
                                'nghia_vi' => 'tạp chí',
                                'tu_vung' => 'ざっし',
                                'kunyomi' => [],
                                'onyomi' => ['ザッシ'],
                            ],
                            [
                                'kanji' => '新聞',
                                'stroke_asset' => null,
                                'han_viet' => 'TÂN VĂN',
                                'nghia_vi' => 'báo',
                                'tu_vung' => 'しんぶん',
                                'kunyomi' => [],
                                'onyomi' => ['シンブン'],
                            ],
                            [
                                'kanji' => '手帳',
                                'stroke_asset' => null,
                                'han_viet' => 'THỦ TRƯỜNG',
                                'nghia_vi' => 'sổ tay',
                                'tu_vung' => 'てちょう',
                                'kunyomi' => ['て'],
                                'onyomi' => ['ショウ', 'チョウ'],
                            ],
                            [
                                'kanji' => '名刺',
                                'stroke_asset' => null,
                                'han_viet' => 'DANH THÍCH',
                                'nghia_vi' => 'danh thiếp',
                                'tu_vung' => 'めいし',
                                'kunyomi' => ['な'],
                                'onyomi' => ['メイ', 'シ'],
                            ],
                            [
                                'kanji' => '鉛筆',
                                'stroke_asset' => null,
                                'han_viet' => 'DUYÊN BÚT',
                                'nghia_vi' => 'bút chì',
                                'tu_vung' => 'えんぴつ',
                                'kunyomi' => [],
                                'onyomi' => ['エン', 'ヒツ'],
                            ],
                            [
                                'kanji' => '時計',
                                'stroke_asset' => null,
                                'han_viet' => 'THỜI KẾ',
                                'nghia_vi' => 'đồng hồ',
                                'tu_vung' => 'とけい',
                                'kunyomi' => ['とき'],
                                'onyomi' => ['ジ', 'ケイ'],
                            ],
                            [
                                'kanji' => '傘',
                                'stroke_asset' => null,
                                'han_viet' => 'TÁN',
                                'nghia_vi' => 'ô, dù',
                                'tu_vung' => 'かさ',
                                'kunyomi' => ['かさ'],
                                'onyomi' => ['サン'],
                            ],
                            [
                                'kanji' => '車',
                                'stroke_asset' => null,
                                'han_viet' => 'XA',
                                'nghia_vi' => 'ô tô, xe hơi',
                                'tu_vung' => 'くるま',
                                'kunyomi' => ['くるま'],
                                'onyomi' => ['シャ'],
                            ],
                            [
                                'kanji' => '机',
                                'stroke_asset' => null,
                                'han_viet' => 'KỶ',
                                'nghia_vi' => 'cái bàn',
                                'tu_vung' => 'つくえ',
                                'kunyomi' => ['つくえ'],
                                'onyomi' => ['キ'],
                            ],
                            [
                                'kanji' => '土産',
                                'stroke_asset' => null,
                                'han_viet' => 'THỔ SẢN',
                                'nghia_vi' => 'quà (mua khi đi xa về hoặc mang đi thăm nhà người nào đó)',
                                'tu_vung' => '[お]みやげ',
                                'kunyomi' => ['みやげ'],
                                'onyomi' => ['ド', 'サン'],
                            ],
                            [
                                'kanji' => '英語',
                                'stroke_asset' => null,
                                'han_viet' => 'ANH NGỮ',
                                'nghia_vi' => 'tiếng Anh',
                                'tu_vung' => 'えいご',
                                'kunyomi' => [],
                                'onyomi' => ['エイ', 'ゴ'],
                            ],
                            [
                                'kanji' => '日本語',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT BẢN NGỮ',
                                'nghia_vi' => 'tiếng Nhật',
                                'tu_vung' => 'にほんご',
                                'kunyomi' => [],
                                'onyomi' => ['ニチ', 'ホン', 'ゴ'],
                            ],
                            [
                                'kanji' => '語',
                                'stroke_asset' => null,
                                'han_viet' => 'NGỮ',
                                'nghia_vi' => 'tiếng',
                                'tu_vung' => '～ご',
                                'kunyomi' => ['かたる', 'かたらう'],
                                'onyomi' => ['ゴ'],
                            ],
                            [
                                'kanji' => '何',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ',
                                'nghia_vi' => 'cái gì',
                                'tu_vung' => 'なに',
                                'kunyomi' => ['なに', 'なん'],
                                'onyomi' => ['カ'],
                            ],
                        ];
                    } elseif ($i === 3) {
                        // Dữ liệu hán tự cho Bài 3
                        $content = [
                            [
                                'kanji' => '教室',
                                'stroke_asset' => null,
                                'han_viet' => 'GIÁO THẤT',
                                'nghia_vi' => 'lớp học, phòng học',
                                'tu_vung' => 'きょうしつ',
                                'kunyomi' => [],
                                'onyomi' => ['キョウシツ'],
                            ],
                            [
                                'kanji' => '食堂',
                                'stroke_asset' => null,
                                'han_viet' => 'THỰC ĐƯỜNG',
                                'nghia_vi' => 'nhà ăn',
                                'tu_vung' => 'しょくどう',
                                'kunyomi' => [],
                                'onyomi' => ['ショクドウ'],
                            ],
                            [
                                'kanji' => '事務所',
                                'stroke_asset' => null,
                                'han_viet' => 'SỰ VỤ SỞ',
                                'nghia_vi' => 'văn phòng',
                                'tu_vung' => 'じむしょ',
                                'kunyomi' => [],
                                'onyomi' => ['ジムショ'],
                            ],
                            [
                                'kanji' => '会議室',
                                'stroke_asset' => null,
                                'han_viet' => 'HỘI NGHỊ THẤT',
                                'nghia_vi' => 'phòng họp',
                                'tu_vung' => 'かいぎしつ',
                                'kunyomi' => [],
                                'onyomi' => ['カイギシツ'],
                            ],
                            [
                                'kanji' => '受付',
                                'stroke_asset' => null,
                                'han_viet' => 'THỤ PHÓ',
                                'nghia_vi' => 'bộ phận tiếp tân, phòng thường trực',
                                'tu_vung' => 'うけつけ',
                                'kunyomi' => ['うける'],
                                'onyomi' => ['ジュ', 'フ'],
                            ],
                            [
                                'kanji' => '部屋',
                                'stroke_asset' => null,
                                'han_viet' => 'BỘ ỐC',
                                'nghia_vi' => 'căn phòng',
                                'tu_vung' => 'へや',
                                'kunyomi' => ['へや'],
                                'onyomi' => ['ブ', 'ヤ'],
                            ],
                            [
                                'kanji' => '手洗い',
                                'stroke_asset' => null,
                                'han_viet' => 'THỦ TẨY',
                                'nghia_vi' => 'nhà vệ sinh, phòng vệ sinh, toalet',
                                'tu_vung' => 'トイレ（おてあらい）',
                                'kunyomi' => ['てあらい'],
                                'onyomi' => ['シュ', 'セン'],
                            ],
                            [
                                'kanji' => '階段',
                                'stroke_asset' => null,
                                'han_viet' => 'GIAI ĐOẠN',
                                'nghia_vi' => 'cầu thang',
                                'tu_vung' => 'かいだん',
                                'kunyomi' => [],
                                'onyomi' => ['カイダン'],
                            ],
                            [
                                'kanji' => '自動販売機',
                                'stroke_asset' => null,
                                'han_viet' => 'TỰ ĐỘNG PHIẾN MẠI CƠ',
                                'nghia_vi' => 'máy bán hàng tự động',
                                'tu_vung' => 'じどうはんばいき',
                                'kunyomi' => [],
                                'onyomi' => ['ジドウハンバイキ'],
                            ],
                            [
                                'kanji' => '電話',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐIỆN THOẠI',
                                'nghia_vi' => 'máy điện thoại, điện thoại',
                                'tu_vung' => 'でんわ',
                                'kunyomi' => [],
                                'onyomi' => ['デンワ'],
                            ],
                            [
                                'kanji' => '国',
                                'stroke_asset' => null,
                                'han_viet' => 'QUỐC',
                                'nghia_vi' => 'đất nước (của anh/chị)',
                                'tu_vung' => '[お]くに',
                                'kunyomi' => ['くに'],
                                'onyomi' => ['コク'],
                            ],
                            [
                                'kanji' => '会社',
                                'stroke_asset' => null,
                                'han_viet' => 'HỘI XÃ',
                                'nghia_vi' => 'công ty',
                                'tu_vung' => 'かいしゃ',
                                'kunyomi' => [],
                                'onyomi' => ['カイシャ'],
                            ],
                            [
                                'kanji' => '靴',
                                'stroke_asset' => null,
                                'han_viet' => 'NGOA',
                                'nghia_vi' => 'giày',
                                'tu_vung' => 'くつ',
                                'kunyomi' => ['くつ'],
                                'onyomi' => ['カ'],
                            ],
                            [
                                'kanji' => '売り場',
                                'stroke_asset' => null,
                                'han_viet' => 'MẠI TRƯỜNG',
                                'nghia_vi' => 'quầy bán (trong một cửa hàng bách hóa)',
                                'tu_vung' => 'うりば',
                                'kunyomi' => ['うる', 'うり'],
                                'onyomi' => ['バイ', 'ジョウ'],
                            ],
                            [
                                'kanji' => '地下',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐỊA HẠ',
                                'nghia_vi' => 'tầng hầm, dưới mặt đất',
                                'tu_vung' => 'ちか',
                                'kunyomi' => [],
                                'onyomi' => ['チ', 'カ'],
                            ],
                            [
                                'kanji' => '階',
                                'stroke_asset' => null,
                                'han_viet' => 'GIAI',
                                'nghia_vi' => 'tầng thứ',
                                'tu_vung' => '～かい（～がい）',
                                'kunyomi' => [],
                                'onyomi' => ['カイ', 'ガイ'],
                            ],
                            [
                                'kanji' => '何階',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ GIAI',
                                'nghia_vi' => 'tầng mấy',
                                'tu_vung' => 'なんがい',
                                'kunyomi' => [],
                                'onyomi' => ['ナンガイ'],
                            ],
                            [
                                'kanji' => '円',
                                'stroke_asset' => null,
                                'han_viet' => 'VIÊN',
                                'nghia_vi' => 'yên',
                                'tu_vung' => '～えん',
                                'kunyomi' => [],
                                'onyomi' => ['エン'],
                            ],
                            [
                                'kanji' => '百',
                                'stroke_asset' => null,
                                'han_viet' => 'BÁCH',
                                'nghia_vi' => 'trăm',
                                'tu_vung' => 'ひゃく',
                                'kunyomi' => [],
                                'onyomi' => ['ヒャク'],
                            ],
                            [
                                'kanji' => '千',
                                'stroke_asset' => null,
                                'han_viet' => 'THIÊN',
                                'nghia_vi' => 'nghìn',
                                'tu_vung' => 'せん',
                                'kunyomi' => [],
                                'onyomi' => ['セン'],
                            ],
                            [
                                'kanji' => '万',
                                'stroke_asset' => null,
                                'han_viet' => 'VẠN',
                                'nghia_vi' => 'mười nghìn, vạn',
                                'tu_vung' => 'まん',
                                'kunyomi' => [],
                                'onyomi' => ['マン', 'バン'],
                            ],
                        ];
                    } elseif ($i === 4) {
                        // Dữ liệu hán tự cho Bài 4
                        $content = [
                            [
                                'kanji' => '起きます',
                                'stroke_asset' => null,
                                'han_viet' => 'KHỞI',
                                'nghia_vi' => 'dậy, thức dậy',
                                'tu_vung' => 'おきます',
                                'kunyomi' => ['おきる'],
                                'onyomi' => ['キ'],
                            ],
                            [
                                'kanji' => '寝ます',
                                'stroke_asset' => null,
                                'han_viet' => 'TẨM',
                                'nghia_vi' => 'ngủ, đi ngủ',
                                'tu_vung' => 'ねます',
                                'kunyomi' => ['ねる'],
                                'onyomi' => ['シン'],
                            ],
                            [
                                'kanji' => '働きます',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐỘNG',
                                'nghia_vi' => 'làm việc',
                                'tu_vung' => 'はたらきます',
                                'kunyomi' => ['はたらく'],
                                'onyomi' => ['ドウ'],
                            ],
                            [
                                'kanji' => '休みます',
                                'stroke_asset' => null,
                                'han_viet' => 'HƯU',
                                'nghia_vi' => 'nghỉ, nghỉ ngơi',
                                'tu_vung' => 'やすみます',
                                'kunyomi' => ['やすむ', 'やすめる'],
                                'onyomi' => ['キュウ'],
                            ],
                            [
                                'kanji' => '勉強します',
                                'stroke_asset' => null,
                                'han_viet' => 'MIỄN CƯỜNG',
                                'nghia_vi' => 'học',
                                'tu_vung' => 'べんきょうします',
                                'kunyomi' => [],
                                'onyomi' => ['ベン', 'キョウ'],
                            ],
                            [
                                'kanji' => '終わります',
                                'stroke_asset' => null,
                                'han_viet' => 'CHUNG',
                                'nghia_vi' => 'hết, kết thúc, xong',
                                'tu_vung' => 'おわります',
                                'kunyomi' => ['おわる'],
                                'onyomi' => ['シュウ'],
                            ],
                            [
                                'kanji' => '銀行',
                                'stroke_asset' => null,
                                'han_viet' => 'NGÂN HÀNH',
                                'nghia_vi' => 'ngân hàng',
                                'tu_vung' => 'ぎんこう',
                                'kunyomi' => [],
                                'onyomi' => ['ギン', 'コウ'],
                            ],
                            [
                                'kanji' => '郵便局',
                                'stroke_asset' => null,
                                'han_viet' => 'BƯU TIỆN CỤC',
                                'nghia_vi' => 'bưu điện',
                                'tu_vung' => 'ゆうびんきょく',
                                'kunyomi' => [],
                                'onyomi' => ['ユウ', 'ビン', 'キョク'],
                            ],
                            [
                                'kanji' => '図書館',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐỒ THƯ QUÁN',
                                'nghia_vi' => 'thư viện',
                                'tu_vung' => 'としょかん',
                                'kunyomi' => [],
                                'onyomi' => ['ト', 'ショ', 'カン'],
                            ],
                            [
                                'kanji' => '美術館',
                                'stroke_asset' => null,
                                'han_viet' => 'MỸ THUẬT QUÁN',
                                'nghia_vi' => 'bảo tàng mỹ thuật',
                                'tu_vung' => 'びじゅつかん',
                                'kunyomi' => [],
                                'onyomi' => ['ビ', 'ジュツ', 'カン'],
                            ],
                            [
                                'kanji' => '今',
                                'stroke_asset' => null,
                                'han_viet' => 'KIM',
                                'nghia_vi' => 'bây giờ',
                                'tu_vung' => 'いま',
                                'kunyomi' => ['いま'],
                                'onyomi' => ['コン', 'キン'],
                            ],
                            [
                                'kanji' => '時',
                                'stroke_asset' => null,
                                'han_viet' => 'THỜI',
                                'nghia_vi' => 'giờ',
                                'tu_vung' => '～じ',
                                'kunyomi' => ['とき'],
                                'onyomi' => ['ジ'],
                            ],
                            [
                                'kanji' => '分',
                                'stroke_asset' => null,
                                'han_viet' => 'PHÂN',
                                'nghia_vi' => 'phút',
                                'tu_vung' => '～ふん (～ぷん)',
                                'kunyomi' => ['わかる', 'わける'],
                                'onyomi' => ['ブン', 'フン', 'ブ'],
                            ],
                            [
                                'kanji' => '半',
                                'stroke_asset' => null,
                                'han_viet' => 'BÁN',
                                'nghia_vi' => 'rưỡi, nửa',
                                'tu_vung' => 'はん',
                                'kunyomi' => ['なかば'],
                                'onyomi' => ['ハン'],
                            ],
                            [
                                'kanji' => '何時',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ THỜI',
                                'nghia_vi' => 'mấy giờ',
                                'tu_vung' => 'なんじ',
                                'kunyomi' => [],
                                'onyomi' => ['ナンジ'],
                            ],
                            [
                                'kanji' => '何分',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ PHÂN',
                                'nghia_vi' => 'mấy phút',
                                'tu_vung' => 'なんぷん',
                                'kunyomi' => [],
                                'onyomi' => ['ナンプン'],
                            ],
                            [
                                'kanji' => '午前',
                                'stroke_asset' => null,
                                'han_viet' => 'NGỌ TIỀN',
                                'nghia_vi' => 'sáng, trước 12 giờ trưa',
                                'tu_vung' => 'ごぜん',
                                'kunyomi' => [],
                                'onyomi' => ['ゴゼン'],
                            ],
                            [
                                'kanji' => '午後',
                                'stroke_asset' => null,
                                'han_viet' => 'NGỌ HẬU',
                                'nghia_vi' => 'chiều, sau 12 giờ trưa',
                                'tu_vung' => 'ごご',
                                'kunyomi' => [],
                                'onyomi' => ['ゴゴ'],
                            ],
                            [
                                'kanji' => '朝',
                                'stroke_asset' => null,
                                'han_viet' => 'TRIỀU',
                                'nghia_vi' => 'buổi sáng, sáng',
                                'tu_vung' => 'あさ',
                                'kunyomi' => ['あさ'],
                                'onyomi' => ['チョウ'],
                            ],
                            [
                                'kanji' => '昼',
                                'stroke_asset' => null,
                                'han_viet' => 'TRÚ',
                                'nghia_vi' => 'buổi trưa, trưa',
                                'tu_vung' => 'ひる',
                                'kunyomi' => ['ひる'],
                                'onyomi' => ['チュウ'],
                            ],
                            [
                                'kanji' => '晩',
                                'stroke_asset' => null,
                                'han_viet' => 'VÃN',
                                'nghia_vi' => 'buổi tối, tối',
                                'tu_vung' => 'ばん (よる)',
                                'kunyomi' => ['ばん'],
                                'onyomi' => ['バン'],
                            ],
                            [
                                'kanji' => '夜',
                                'stroke_asset' => null,
                                'han_viet' => 'DẠ',
                                'nghia_vi' => 'buổi tối, tối',
                                'tu_vung' => 'よる',
                                'kunyomi' => ['よる', 'よ'],
                                'onyomi' => ['ヤ'],
                            ],
                            [
                                'kanji' => '今晩',
                                'stroke_asset' => null,
                                'han_viet' => 'KIM VÃN',
                                'nghia_vi' => 'tối nay',
                                'tu_vung' => 'こんばん',
                                'kunyomi' => [],
                                'onyomi' => ['コンバン'],
                            ],
                            [
                                'kanji' => '休み',
                                'stroke_asset' => null,
                                'han_viet' => 'HƯU',
                                'nghia_vi' => 'nghỉ, nghỉ phép, ngày nghỉ',
                                'tu_vung' => 'やすみ',
                                'kunyomi' => ['やすむ', 'やすめる'],
                                'onyomi' => ['キュウ'],
                            ],
                            [
                                'kanji' => '昼休み',
                                'stroke_asset' => null,
                                'han_viet' => 'TRÚ HƯU',
                                'nghia_vi' => 'nghỉ trưa',
                                'tu_vung' => 'ひるやすみ',
                                'kunyomi' => [],
                                'onyomi' => ['チュウキュウ'],
                            ],
                            [
                                'kanji' => '試験',
                                'stroke_asset' => null,
                                'han_viet' => 'THÍ NGHIỆM',
                                'nghia_vi' => 'thi, kỳ thi, kiểm tra',
                                'tu_vung' => 'しけん',
                                'kunyomi' => [],
                                'onyomi' => ['シケン'],
                            ],
                            [
                                'kanji' => '会議',
                                'stroke_asset' => null,
                                'han_viet' => 'HỘI NGHỊ',
                                'nghia_vi' => 'cuộc họp, hội nghị',
                                'tu_vung' => 'かいぎ',
                                'kunyomi' => [],
                                'onyomi' => ['カイギ'],
                            ],
                            [
                                'kanji' => '映画',
                                'stroke_asset' => null,
                                'han_viet' => 'ẢNH HỌA',
                                'nghia_vi' => 'phim, điện ảnh',
                                'tu_vung' => 'えいが',
                                'kunyomi' => [],
                                'onyomi' => ['エイ', 'ガ'],
                            ],
                            [
                                'kanji' => '毎朝',
                                'stroke_asset' => null,
                                'han_viet' => 'MỖI TRIỀU',
                                'nghia_vi' => 'hàng sáng, mỗi sáng',
                                'tu_vung' => 'まいあさ',
                                'kunyomi' => [],
                                'onyomi' => ['マイアサ'],
                            ],
                            [
                                'kanji' => '毎晩',
                                'stroke_asset' => null,
                                'han_viet' => 'MỖI VÃN',
                                'nghia_vi' => 'hàng tối, mỗi tối',
                                'tu_vung' => 'まいばん',
                                'kunyomi' => [],
                                'onyomi' => ['マイバン'],
                            ],
                            [
                                'kanji' => '毎日',
                                'stroke_asset' => null,
                                'han_viet' => 'MỖI NHẬT',
                                'nghia_vi' => 'hàng ngày, mỗi ngày',
                                'tu_vung' => 'まいにち',
                                'kunyomi' => [],
                                'onyomi' => ['マイニチ'],
                            ],
                            [
                                'kanji' => '月曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'NGUYỆT DIỆU NHẬT',
                                'nghia_vi' => 'thứ hai',
                                'tu_vung' => 'げつようび',
                                'kunyomi' => [],
                                'onyomi' => ['ゲツヨウビ'],
                            ],
                            [
                                'kanji' => '火曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'HỎA DIỆU NHẬT',
                                'nghia_vi' => 'thứ ba',
                                'tu_vung' => 'かようび',
                                'kunyomi' => [],
                                'onyomi' => ['カヨウビ'],
                            ],
                            [
                                'kanji' => '水曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'THỦY DIỆU NHẬT',
                                'nghia_vi' => 'thứ tư',
                                'tu_vung' => 'すいようび',
                                'kunyomi' => [],
                                'onyomi' => ['スイヨウビ'],
                            ],
                            [
                                'kanji' => '木曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'MỘC DIỆU NHẬT',
                                'nghia_vi' => 'thứ năm',
                                'tu_vung' => 'もくようび',
                                'kunyomi' => [],
                                'onyomi' => ['モクヨウビ'],
                            ],
                            [
                                'kanji' => '金曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'KIM DIỆU NHẬT',
                                'nghia_vi' => 'thứ sáu',
                                'tu_vung' => 'きんようび',
                                'kunyomi' => [],
                                'onyomi' => ['キンヨウビ'],
                            ],
                            [
                                'kanji' => '土曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'THỔ DIỆU NHẬT',
                                'nghia_vi' => 'thứ bảy',
                                'tu_vung' => 'どようび',
                                'kunyomi' => [],
                                'onyomi' => ['ドヨウビ'],
                            ],
                            [
                                'kanji' => '日曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT DIỆU NHẬT',
                                'nghia_vi' => 'chủ nhật',
                                'tu_vung' => 'にちようび',
                                'kunyomi' => [],
                                'onyomi' => ['ニチヨウビ'],
                            ],
                            [
                                'kanji' => '何曜日',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ DIỆU NHẬT',
                                'nghia_vi' => 'thứ mấy',
                                'tu_vung' => 'なんようび',
                                'kunyomi' => [],
                                'onyomi' => ['ナンヨウビ'],
                            ],
                        ];
                    } elseif ($i === 5) {
                        // Dữ liệu hán tự cho Bài 5
                        $content = [
                            [
                                'kanji' => '行きます',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀNH',
                                'nghia_vi' => 'đi',
                                'tu_vung' => 'いきます',
                                'kunyomi' => ['いく', 'ゆく', 'おこなう'],
                                'onyomi' => ['コウ', 'ギョウ', 'アン'],
                            ],
                            [
                                'kanji' => '来ます',
                                'stroke_asset' => null,
                                'han_viet' => 'LAI',
                                'nghia_vi' => 'đến',
                                'tu_vung' => 'きます',
                                'kunyomi' => ['くる'],
                                'onyomi' => ['ライ', 'タイ'],
                            ],
                            [
                                'kanji' => '帰ります',
                                'stroke_asset' => null,
                                'han_viet' => 'QUY',
                                'nghia_vi' => 'về',
                                'tu_vung' => 'かえります',
                                'kunyomi' => ['かえる', 'かえす'],
                                'onyomi' => ['キ'],
                            ],
                            [
                                'kanji' => '学校',
                                'stroke_asset' => null,
                                'han_viet' => 'HỌC HIỆU',
                                'nghia_vi' => 'trường học',
                                'tu_vung' => 'がっこう',
                                'kunyomi' => [],
                                'onyomi' => ['ガッコウ'],
                            ],
                            [
                                'kanji' => '駅',
                                'stroke_asset' => null,
                                'han_viet' => 'DỊCH',
                                'nghia_vi' => 'ga, nhà ga',
                                'tu_vung' => 'えき',
                                'kunyomi' => [],
                                'onyomi' => ['エキ'],
                            ],
                            [
                                'kanji' => '飛行機',
                                'stroke_asset' => null,
                                'han_viet' => 'PHI HÀNH CƠ',
                                'nghia_vi' => 'máy bay',
                                'tu_vung' => 'ひこうき',
                                'kunyomi' => [],
                                'onyomi' => ['ヒコウキ'],
                            ],
                            [
                                'kanji' => '船',
                                'stroke_asset' => null,
                                'han_viet' => 'THUYỀN',
                                'nghia_vi' => 'thuyền, tàu thủy',
                                'tu_vung' => 'ふね',
                                'kunyomi' => ['ふね', 'ふな'],
                                'onyomi' => ['セン'],
                            ],
                            [
                                'kanji' => '電車',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐIỆN XA',
                                'nghia_vi' => 'tàu điện',
                                'tu_vung' => 'でんしゃ',
                                'kunyomi' => [],
                                'onyomi' => ['デンシャ'],
                            ],
                            [
                                'kanji' => '地下鉄',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐỊA HẠ THIẾT',
                                'nghia_vi' => 'tàu điện ngầm',
                                'tu_vung' => 'ちかてつ',
                                'kunyomi' => [],
                                'onyomi' => ['チカテツ'],
                            ],
                            [
                                'kanji' => '新幹線',
                                'stroke_asset' => null,
                                'han_viet' => 'TÂN CÁN TUYẾN',
                                'nghia_vi' => 'tàu Shinkansen (tàu điện siêu tốc của Nhật)',
                                'tu_vung' => 'しんかんせん',
                                'kunyomi' => [],
                                'onyomi' => ['シンカンセン'],
                            ],
                            [
                                'kanji' => '自転車',
                                'stroke_asset' => null,
                                'han_viet' => 'TỰ CHUYỂN XA',
                                'nghia_vi' => 'xe đạp',
                                'tu_vung' => 'じてんしゃ',
                                'kunyomi' => [],
                                'onyomi' => ['ジテンシャ'],
                            ],
                            [
                                'kanji' => '歩いて',
                                'stroke_asset' => null,
                                'han_viet' => 'BỘ',
                                'nghia_vi' => 'đi bộ',
                                'tu_vung' => 'あるいて',
                                'kunyomi' => ['あるく', 'あゆむ'],
                                'onyomi' => ['ホ', 'ブ', 'フ'],
                            ],
                            [
                                'kanji' => '人',
                                'stroke_asset' => null,
                                'han_viet' => 'NHÂN',
                                'nghia_vi' => 'người',
                                'tu_vung' => 'ひと',
                                'kunyomi' => ['ひと'],
                                'onyomi' => ['ジン', 'ニン'],
                            ],
                            [
                                'kanji' => '友達',
                                'stroke_asset' => null,
                                'han_viet' => 'HỮU ĐẠT',
                                'nghia_vi' => 'bạn, bạn bè',
                                'tu_vung' => 'ともだち',
                                'kunyomi' => ['とも'],
                                'onyomi' => ['ユウ'],
                            ],
                            [
                                'kanji' => '彼',
                                'stroke_asset' => null,
                                'han_viet' => 'BỈ',
                                'nghia_vi' => 'anh ấy, bạn trai',
                                'tu_vung' => 'かれ',
                                'kunyomi' => ['かれ'],
                                'onyomi' => ['ヒ'],
                            ],
                            [
                                'kanji' => '彼女',
                                'stroke_asset' => null,
                                'han_viet' => 'BỈ NỮ',
                                'nghia_vi' => 'chị ấy, bạn gái',
                                'tu_vung' => 'かのじょ',
                                'kunyomi' => ['かの'],
                                'onyomi' => ['ヒ', 'ジョ'],
                            ],
                            [
                                'kanji' => '家族',
                                'stroke_asset' => null,
                                'han_viet' => 'GIA TỘC',
                                'nghia_vi' => 'gia đình',
                                'tu_vung' => 'かぞく',
                                'kunyomi' => ['やから'],
                                'onyomi' => ['カゾク'],
                            ],
                            [
                                'kanji' => '一人で',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẤT NHÂN',
                                'nghia_vi' => 'một mình',
                                'tu_vung' => 'ひとりで',
                                'kunyomi' => ['ひとり', 'ひと'],
                                'onyomi' => ['イチ', 'ニン'],
                            ],
                            [
                                'kanji' => '先週',
                                'stroke_asset' => null,
                                'han_viet' => 'TIÊN CHU',
                                'nghia_vi' => 'tuần trước',
                                'tu_vung' => 'せんしゅう',
                                'kunyomi' => ['さき'],
                                'onyomi' => ['センシュウ'],
                            ],
                            [
                                'kanji' => '今週',
                                'stroke_asset' => null,
                                'han_viet' => 'KIM CHU',
                                'nghia_vi' => 'tuần này',
                                'tu_vung' => 'こんしゅう',
                                'kunyomi' => [],
                                'onyomi' => ['コンシュウ'],
                            ],
                            [
                                'kanji' => '来週',
                                'stroke_asset' => null,
                                'han_viet' => 'LAI CHU',
                                'nghia_vi' => 'tuần sau',
                                'tu_vung' => 'らいしゅう',
                                'kunyomi' => [],
                                'onyomi' => ['ライシュウ'],
                            ],
                            [
                                'kanji' => '先月',
                                'stroke_asset' => null,
                                'han_viet' => 'TIÊN NGUYỆT',
                                'nghia_vi' => 'tháng trước',
                                'tu_vung' => 'せんげつ',
                                'kunyomi' => ['さき'],
                                'onyomi' => ['センゲツ'],
                            ],
                            [
                                'kanji' => '今月',
                                'stroke_asset' => null,
                                'han_viet' => 'KIM NGUYỆT',
                                'nghia_vi' => 'tháng này',
                                'tu_vung' => 'こんげつ',
                                'kunyomi' => [],
                                'onyomi' => ['コンゲツ'],
                            ],
                            [
                                'kanji' => '来月',
                                'stroke_asset' => null,
                                'han_viet' => 'LAI NGUYỆT',
                                'nghia_vi' => 'tháng sau',
                                'tu_vung' => 'らいげつ',
                                'kunyomi' => [],
                                'onyomi' => ['ライゲツ'],
                            ],
                            [
                                'kanji' => '去年',
                                'stroke_asset' => null,
                                'han_viet' => 'KHỨ NIÊN',
                                'nghia_vi' => 'năm ngoái',
                                'tu_vung' => 'きょねん',
                                'kunyomi' => [],
                                'onyomi' => ['キョネン'],
                            ],
                            [
                                'kanji' => '来年',
                                'stroke_asset' => null,
                                'han_viet' => 'LAI NIÊN',
                                'nghia_vi' => 'năm sau',
                                'tu_vung' => 'らいねん',
                                'kunyomi' => [],
                                'onyomi' => ['ライネン'],
                            ],
                            [
                                'kanji' => '年',
                                'stroke_asset' => null,
                                'han_viet' => 'NIÊN',
                                'nghia_vi' => 'năm',
                                'tu_vung' => '～ねん',
                                'kunyomi' => ['とし'],
                                'onyomi' => ['ネン'],
                            ],
                            [
                                'kanji' => '何年',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ NIÊN',
                                'nghia_vi' => 'mấy năm',
                                'tu_vung' => 'なんねん',
                                'kunyomi' => [],
                                'onyomi' => ['ナンネン'],
                            ],
                            [
                                'kanji' => '月',
                                'stroke_asset' => null,
                                'han_viet' => 'NGUYỆT',
                                'nghia_vi' => 'tháng',
                                'tu_vung' => '～がつ',
                                'kunyomi' => ['つき'],
                                'onyomi' => ['ゲツ', 'ガツ'],
                            ],
                            [
                                'kanji' => '何月',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ NGUYỆT',
                                'nghia_vi' => 'tháng mấy',
                                'tu_vung' => 'なんがつ',
                                'kunyomi' => [],
                                'onyomi' => ['ナンガツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 1',
                                'tu_vung' => 'ついたち',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 2, 2 ngày',
                                'tu_vung' => 'ふつか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 3, 3 ngày',
                                'tu_vung' => 'みっか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 4, 4 ngày',
                                'tu_vung' => 'よっか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 5, 5 ngày',
                                'tu_vung' => 'いつか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 6, 6 ngày',
                                'tu_vung' => 'むいか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 7, 7 ngày',
                                'tu_vung' => 'なのか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 8, 8 ngày',
                                'tu_vung' => 'ようか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 9, 9 ngày',
                                'tu_vung' => 'ここのか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày mồng 10, 10 ngày',
                                'tu_vung' => 'とおか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày 14, 14 ngày',
                                'tu_vung' => 'じゅうよっか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày 20, 20 ngày',
                                'tu_vung' => 'はつか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày 24, 24 ngày',
                                'tu_vung' => 'にじゅうよっか',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '日',
                                'stroke_asset' => null,
                                'han_viet' => 'NHẬT',
                                'nghia_vi' => 'ngày',
                                'tu_vung' => '～にち',
                                'kunyomi' => ['ひ', 'か'],
                                'onyomi' => ['ニチ', 'ジツ'],
                            ],
                            [
                                'kanji' => '何日',
                                'stroke_asset' => null,
                                'han_viet' => 'HÀ NHẬT',
                                'nghia_vi' => 'ngày mấy, mấy ngày, bao nhiêu ngày',
                                'tu_vung' => 'なんにち',
                                'kunyomi' => [],
                                'onyomi' => ['ナンニチ'],
                            ],
                            [
                                'kanji' => '誕生日',
                                'stroke_asset' => null,
                                'han_viet' => 'ĐẢN SINH NHẬT',
                                'nghia_vi' => 'sinh nhật',
                                'tu_vung' => 'たんじょうび',
                                'kunyomi' => [],
                                'onyomi' => ['タンジョウビ'],
                            ],
                        ];
                    } else {
                        // Mẫu trống cho các bài khác
                        $content = [
                            [
                                'kanji' => '',
                                'stroke_asset' => null,
                                'han_viet' => '',
                                'nghia_vi' => '',
                                'tu_vung' => '',
                                'kunyomi' => [],
                                'onyomi' => [],
                            ],
                        ];
                    }
                }

                $sections[] = [
                    'lesson_number' => $i,        // tạm thời dùng số bài để liên kết
                    'order_index' => $index + 1,
                    'key' => $sectionDef['key'],
                    'title' => $sectionDef['title'],
                    'content' => $content,
                    'media_url' => null,
                ];
            }
        }

        // Đảm bảo chạy lại seeder không tạo bản ghi trùng
        Schema::disableForeignKeyConstraints();
        DB::table('minna_sections')->truncate();
        DB::table('minna_lessons')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () use ($lessons, $sections) {
            $now = now();

            DB::table('minna_lessons')->insert(array_map(function ($lesson) use ($now) {
                return $lesson + ['created_at' => $now, 'updated_at' => $now];
            }, $lessons));

            $lessonIdMap = DB::table('minna_lessons')->pluck('id', 'number')->toArray();

            $sectionRows = array_map(function ($section) use ($now, $lessonIdMap) {
                $lessonId = $lessonIdMap[$section['lesson_number']] ?? null;

                if (! $lessonId) {
                    return null;
                }

                return [
                    'lesson_id' => $lessonId,
                    'order_index' => $section['order_index'],
                    'key' => $section['key'],
                    'title' => $section['title'],
                    'content' => $section['content'] !== null
                        ? json_encode($section['content'], JSON_UNESCAPED_UNICODE)
                        : null,
                    'media_url' => $section['media_url'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $sections);

            $sectionRows = array_values(array_filter($sectionRows));

            if (! empty($sectionRows)) {
                DB::table('minna_sections')->insert($sectionRows);
            }
        });

        MinnaCache::flushAll();
        FlashcardCache::invalidate();

        // Tạm thời để sẵn dữ liệu trong biến phục vụ debug nếu cần
        // dump(['lessons' => count($lessons), 'sections' => count($sections)]);
    }
}


