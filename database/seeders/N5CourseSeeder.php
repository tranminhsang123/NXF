<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\N5CourseData;

class N5CourseSeeder extends Seeder
{
    public function run(): void
    {
        $speedMasterN5 = [
            'tuVung' => [
                [
                    'bai' => 'Bài 1',
                    'title' => '家族・人 ① Gia đình, con người',
                    'words' => [
                        ['tu' => 'わたし', 'nghia' => 'Tôi'],
                        ['tu' => 'わたくし', 'nghia' => 'Tôi'],
                        ['tu' => 'あなた', 'nghia' => 'bạn'],
                        ['tu' => '彼', 'nghia' => 'anh ấy'],
                        ['tu' => '彼女', 'nghia' => 'cô ấy'],
                        ['tu' => 'お母さん', 'nghia' => 'mẹ'],
                        ['tu' => '母', 'nghia' => 'mẹ'],
                        ['tu' => 'お父さん', 'nghia' => 'bố'],
                        ['tu' => '父', 'nghia' => 'bố'],
                        ['tu' => '親', 'nghia' => 'bố mẹ'],
                        ['tu' => '両親', 'nghia' => 'bố mẹ'],
                        ['tu' => 'お兄さん', 'nghia' => 'anh trai'],
                        ['tu' => '兄', 'nghia' => 'anh trai'],
                        ['tu' => '(わたしは) 兄がいます', 'nghia' => 'Tôi có anh trai'],
                        ['tu' => 'お姉さん', 'nghia' => 'chị gái'],
                        ['tu' => '姉', 'nghia' => 'chị gái'],
                        ['tu' => '弟', 'nghia' => 'em trai'],
                        ['tu' => '妹', 'nghia' => 'em gái'],
                        ['tu' => '兄弟', 'nghia' => 'anh chị em'],
                        ['tu' => '子ども', 'nghia' => 'con'],
                        ['tu' => '赤ちゃん', 'nghia' => 'bé'],
                        ['tu' => '男', 'nghia' => 'đàn ông'],
                        ['tu' => '女', 'nghia' => 'phụ nữ'],
                        ['tu' => '男の人', 'nghia' => 'con trai'],
                        ['tu' => '女の人', 'nghia' => 'con gái'],
                        ['tu' => 'おじいさん', 'nghia' => 'ông'],
                        ['tu' => 'おばあさん', 'nghia' => 'bà'],
                        ['tu' => 'おじ (さん)', 'nghia' => 'bác trai, chú, cậu'],
                        ['tu' => 'おば (さん)', 'nghia' => 'bác gái, cô, dì'],
                        ['tu' => 'ペット', 'nghia' => 'thú cưng'],
                        ['tu' => '家族', 'nghia' => 'gia đình'],
                        ['tu' => '日本にいます', 'nghia' => 'ở Nhật Bản'],
                        ['tu' => '住みます', 'nghia' => 'sống'],
                        ['tu' => 'いっしょに', 'nghia' => 'cùng'],
                        ['tu' => '彼は日本人です', 'nghia' => 'Anh ấy là người Nhật Bản'],
                        ['tu' => '国', 'nghia' => 'đất nước'],
                        ['tu' => '学校', 'nghia' => 'trường học'],
                        ['tu' => '学生', 'nghia' => 'sinh viên'],
                        ['tu' => '先生', 'nghia' => 'giáo viên'],
                        ['tu' => '会社', 'nghia' => 'công ty'],
                        ['tu' => '社員', 'nghia' => 'nhân viên công ty'],
                        ['tu' => '彼は医者をしています', 'nghia' => 'Anh ấy làm bác sĩ'],
                        ['tu' => '(お) 年は (お) いくつですか', 'nghia' => 'Bạn bao nhiêu tuổi'],
                        ['tu' => '(わたしは) 21歳です', 'nghia' => 'Tôi 21 tuổi'],
                        ['tu' => '元気 (な)', 'nghia' => 'khỏe mạnh'],
                    ],
                ],
                [
                    'bai' => 'Bài 2',
                    'title' => '家族・人 ① Gia đình, con người',
                    'words' => [
                        ['tu' => '人', 'nghia' => 'người'],
                        ['tu' => '男の人', 'nghia' => 'đàn ông'],
                        ['tu' => '女の人', 'nghia' => 'phụ nữ'],
                        ['tu' => '大人', 'nghia' => 'người lớn'],
                        ['tu' => '子ども', 'nghia' => 'trẻ em'],
                        ['tu' => '〜たち', 'nghia' => 'những'],
                        ['tu' => 'わたしたち', 'nghia' => 'chúng tôi'],
                        ['tu' => '子どもたち', 'nghia' => 'lũ trẻ'],
                        ['tu' => '友だち', 'nghia' => 'bạn bè'],
                        ['tu' => 'たくさん', 'nghia' => 'nhiều'],
                        ['tu' => 'みんな', 'nghia' => 'các bạn'],
                        ['tu' => 'みなさん', 'nghia' => 'các quý vị'],
                        ['tu' => '自分', 'nghia' => 'bản thân mình'],
                        ['tu' => '奥さん', 'nghia' => 'vợ'],
                        ['tu' => '学校', 'nghia' => 'trường học'],
                        ['tu' => 'クラス', 'nghia' => 'lớp học'],
                        ['tu' => '大学', 'nghia' => 'trường đại học'],
                        ['tu' => '大学生', 'nghia' => 'sinh viên đại học'],
                        ['tu' => '留学生', 'nghia' => 'lưu học sinh'],
                        ['tu' => '名前', 'nghia' => 'tên'],
                        ['tu' => 'だれ', 'nghia' => 'ai'],
                        ['tu' => '日本語を勉強します', 'nghia' => 'học tiếng Nhật'],
                        ['tu' => '英語を教えます', 'nghia' => 'dạy tiếng Anh'],
                        ['tu' => '工場ではたらきます', 'nghia' => 'làm việc ở nhà máy'],
                        ['tu' => '公園で遊びます', 'nghia' => 'chơi ở công viên'],
                        ['tu' => '海に行きます', 'nghia' => 'ra biển'],
                        ['tu' => '失礼しが', 'nghia' => 'xin lỗi nhưng'],
                    ],
                ],
            ],
            'nguPhap' => [
                [
                    'bai' => 'Bài 1',
                    'title' => 'わたしはタスです。',
                    'grammar_points' => [
                        [
                            'particle' => '~は',
                            'explanation' => 'Trợ từ chỉ chủ ngữ',
                            'examples' => [
                                ['japanese' => 'わたしはタスです。', 'vietnamese' => 'Tôi là Tasu.'],
                                ['japanese' => 'かのじょはかわいい。', 'vietnamese' => 'Cô ấy dễ thương.'],
                                ['japanese' => '北海道はさむい。', 'vietnamese' => 'Hokkaido lạnh.'],
                                ['japanese' => 'わたしは食べます。あなたは。', 'vietnamese' => 'Tôi ăn. Còn bạn?'],
                            ],
                        ],
                        [
                            'particle' => '~です',
                            'explanation' => 'Biểu thị rằng một vật hoặc một người nào đó là cái gì hoặc ai',
                            'examples' => [
                                ['japanese' => '私はタスです。', 'vietnamese' => 'Tôi là Tasu.'],
                                ['japanese' => '学生です。', 'vietnamese' => 'Tôi là sinh viên.'],
                                ['japanese' => 'かれは日本人ではありません。', 'vietnamese' => 'Anh ấy không phải là người Nhật.'],
                                ['japanese' => 'となりの人は女性ですか。', 'vietnamese' => 'Người bên cạnh là phụ nữ phải không?'],
                            ],
                        ],
                        [
                            'particle' => '~から',
                            'explanation' => 'Trợ từ chỉ điểm xuất phát',
                            'examples' => [
                                ['japanese' => 'バンコクから来ました。', 'vietnamese' => 'Tôi đến từ Bangkok.'],
                                ['japanese' => 'ベトナムから来ました。', 'vietnamese' => 'Tôi đến từ Việt Nam.'],
                                ['japanese' => '北海道から はこびました。', 'vietnamese' => 'Tôi đã vận chuyển từ Hokkaido.'],
                                ['japanese' => 'わたしは横浜から ひっこします。', 'vietnamese' => 'Tôi sẽ chuyển nhà từ Yokohama.'],
                            ],
                        ],
                    ],
                ],
                [
                    'bai' => 'Bài 2',
                    'title' => 'マンガと アニメが好きです。',
                    'grammar_points' => [
                        [
                            'particle' => '~と',
                            'explanation' => 'Trợ từ nối danh từ với danh từ',
                            'examples' => [
                                ['japanese' => 'マンガとアニメ。', 'vietnamese' => 'Manga và anime.'],
                                ['japanese' => '休みは土よう日と日よう日です。', 'vietnamese' => 'Ngày nghỉ là thứ Bảy và Chủ nhật.'],
                                ['japanese' => 'おさけとりょうりです。どうぞ。', 'vietnamese' => 'Rượu và món ăn. Xin mời.'],
                            ],
                        ],
                        [
                            'particle' => '~がすき・きらい',
                            'explanation' => 'Cách nói thích / ghét ai đó',
                            'examples' => [
                                ['japanese' => 'マンガとアニメが大好きです。', 'vietnamese' => 'Tôi rất thích manga và anime.'],
                                ['japanese' => 'あかときいろが好きです。', 'vietnamese' => 'Tôi thích màu đỏ và màu vàng.'],
                                ['japanese' => 'わたしはおふろが好きではありません。', 'vietnamese' => 'Tôi không thích tắm bồn.'],
                                ['japanese' => 'あなたはわたしが嫌いですか。', 'vietnamese' => 'Bạn có ghét tôi không?'],
                            ],
                        ],
                        [
                            'particle' => '~を',
                            'explanation' => 'Trợ từ chỉ đối tượng của hành động',
                            'examples' => [
                                ['japanese' => 'マンガを読みます。', 'vietnamese' => 'Tôi đọc manga.'],
                                ['japanese' => 'パソコンを買います。', 'vietnamese' => 'Tôi mua máy tính.'],
                                ['japanese' => '日本語をべんきょうします。', 'vietnamese' => 'Tôi học tiếng Nhật.'],
                                ['japanese' => 'こんや何をしますか。', 'vietnamese' => 'Tối nay bạn sẽ làm gì?'],
                            ],
                        ],
                    ],
                ],
            ],
            'docHieu' => [
                [
                    'bai' => 'Mondai 1',
                    'title' => '短文 - Đoạn văn ngắn',
                    'passage' => '先月、わたしも、母も、旅行に行きました。わたしは四国に行って、美術館を見ました。母は、北海道できれいな花を見ました。わたしは、おみやげに 絵はがきをあげ、母からおいしいおかしをもらいました。',
                    'question' => '「わたし」は母に何をあげましたか。',
                    'options' => [
                        ['text' => '旅行', 'romaji' => 'Ryokō', 'meaning' => 'Trip'],
                        ['text' => '花', 'romaji' => 'Hana', 'meaning' => 'Flower'],
                        ['text' => 'おかし', 'romaji' => 'Okashi', 'meaning' => 'Sweets'],
                        ['text' => '絵はがき', 'romaji' => 'E-hagaki', 'meaning' => 'Postcard'],
                    ],
                    'correct_answer' => 3,
                    'explanation' => 'Trong đoạn văn có viết: わたしは、おみやげに 絵はがきをあげ (Tôi đã tặng bưu thiếp làm quà lưu niệm)',
                ],
                [
                    'bai' => 'Mondai 2',
                    'title' => 'ルームメートの ミンへさんからのメモです。',
                    'passage' => 'アルさんへ
                    さきに学校に行きます。きょうしつで会いましょう。
                    電気をけして出かけてください。
                    先生にかりた本をわすれないでくださね!

                    ミンへ',
                    'question' => 'アルさんは出かける まえに何をしますか。',
                    'options' => [
                        ['text' => '学校に行きます。', 'romaji' => 'Gakkō ni ikimasu.', 'meaning' => 'Đi đến trường.'],
                        ['text' => 'ミンへさんと会います。', 'romaji' => 'Min-he-san to aimasu.', 'meaning' => 'Gặp Min-hye-san.'],
                        ['text' => '電気をけします。', 'romaji' => 'Denki o keshimasu.', 'meaning' => 'Tắt đèn.'],
                        ['text' => '先生に かりた本を読みます。', 'romaji' => 'Sensei ni karita hon o yomimasu.', 'meaning' => 'Đọc cuốn sách mượn từ cô giáo.'],
                    ],
                    'correct_answer' => 2,
                    'explanation' => 'Trong memo có viết: 電気をけして出かけてください (Vui lòng tắt đèn rồi đi), vậy trước khi đi Aru-san phải tắt đèn.',
                ],
            ],
            'ngheHieu' => [
            ],
        ];

        $luyenDoc = [
            [
                'bai' => 'Bài 1 - Mondai 1-1',
                'title' => 'Dokkai 55+ N5',
                'passage' => 'わたしは 1か月前に インドから 日本に ( )。友だちの家にいましたが昨日 アパートに引っ越しました。今日プレゼントを持って となりの人に挨拶に行きました。となりの人が「最近引っ越しの挨拶に来る人が少なくなった。」と 言いました。',
                'questions' => [
                    [
                        'question_number' => '問1',
                        'question' => '( )の中に いちばん よいものを入れなさい。',
                        'options' => [
                            ['text' => '来ます', 'romaji' => 'kimasu', 'meaning' => 'sẽ đến'],
                            ['text' => '来ません', 'romaji' => 'kimasen', 'meaning' => 'sẽ không đến'],
                            ['text' => '来ました', 'romaji' => 'kimashita', 'meaning' => 'đã đến'],
                            ['text' => '来ませんでした', 'romaji' => 'kimasen deshita', 'meaning' => 'đã không đến'],
                        ],
                        'correct_answer' => 2,
                        'explanation' => 'Trong câu có "1か月前に" (cách đây 1 tháng) - đây là dấu hiệu của quá khứ, nên cần dùng 来ました (đã đến)',
                    ],
                    [
                        'question_number' => '問2',
                        'question' => 'この人は今どこに住んでいますか。',
                        'options' => [
                            ['text' => 'インド', 'romaji' => 'Indo', 'meaning' => 'Ấn Độ'],
                            ['text' => '友だちの家', 'romaji' => 'tomodachi no ie', 'meaning' => 'nhà bạn'],
                            ['text' => 'となりの家', 'romaji' => 'tonari no ie', 'meaning' => 'nhà bên cạnh'],
                            ['text' => 'アパート', 'romaji' => 'apāto', 'meaning' => 'căn hộ'],
                        ],
                        'correct_answer' => 3,
                        'explanation' => 'Trong đoạn văn có viết: "昨日 アパートに引っ越しました" (hôm qua đã chuyển đến căn hộ), vậy hiện tại người này đang sống ở căn hộ.',
                    ],
                ],
            ],
            [
                'bai' => 'Bài 2 - Mondai 1-2',
                'title' => 'Dokkai 55+ N5',
                'passage' => 'わたしはメイ・スミスです。わたしはカナダ人 ( )。今日本に住んでいます。仕事は英語の先生です。しゅみは旅行と本を読むことです。スポーツはしませんが見るのはとても好きです。',
                'questions' => [
                    [
                        'question_number' => '問1',
                        'question' => '( )の中に いちばん よいものを入れなさい。',
                        'options' => [
                            ['text' => 'です', 'romaji' => 'desu', 'meaning' => 'là'],
                            ['text' => 'ではありません', 'romaji' => 'dewa arimasen', 'meaning' => 'không phải là'],
                            ['text' => 'でした', 'romaji' => 'deshita', 'meaning' => 'đã là'],
                            ['text' => 'ではありませんでした', 'romaji' => 'dewa arimasen deshita', 'meaning' => 'đã không phải là'],
                        ],
                        'correct_answer' => 0,
                        'explanation' => 'Câu này diễn tả thực tế hiện tại "Tôi là người Canada", nên dùng です (là - hiện tại khẳng định)',
                    ],
                    [
                        'question_number' => '問2',
                        'question' => 'メイさんの しゅみでないのはどれですか。',
                        'options' => [
                            ['text' => '本を読むこと', 'romaji' => 'hon o yomu koto', 'meaning' => 'Đọc sách'],
                            ['text' => '旅行に行くこと', 'romaji' => 'ryokou ni iku koto', 'meaning' => 'Đi du lịch'],
                            ['text' => 'スポーツをすること', 'romaji' => 'supootsu o suru koto', 'meaning' => 'Chơi thể thao'],
                            ['text' => 'スポーツを見ること', 'romaji' => 'supootsu o miru koto', 'meaning' => 'Xem thể thao'],
                        ],
                        'correct_answer' => 2,
                        'explanation' => 'Trong đoạn văn có viết: "しゅみは旅行と本を読むことです" (sở thích là du lịch và đọc sách) và "スポーツはしませんが見るのはとても好きです" (không chơi thể thao nhưng rất thích xem), vậy chơi thể thao KHÔNG phải là sở thích của Mei.',
                    ],
                ],
            ],
        ];

        $marugotoN5 = [
            [
                'bai' => 'Bài 1',
                'title' => '主語の省略 Lược bỏ chủ ngữ',
                'tuVung' => [
                    ['tu' => '大学', 'romaji' => 'Daigaku', 'nghia' => 'trường đại học'],
                    ['tu' => '留学生', 'romaji' => 'Ryūgakusei', 'nghia' => 'lưu học sinh, du học sinh'],
                    ['tu' => 'きょねん', 'romaji' => 'Kyonen', 'nghia' => 'năm ngoái'],
                ],
                'nguPhap' => [
                    [
                        'particle' => 'N1はN2です',
                        'explanation' => 'Là mẫu câu được sử dụng khi giới thiệu về tên, quốc tịch hoặc nghề nghiệp v.v của N1「は」trong 「N1は」 là trợ từ chỉ chủ ngữ.',
                        'vietnamese_meaning' => 'N1 là N2',
                        'examples' => [
                            ['japanese' => 'かのじょうは ベトナム人です。', 'vietnamese' => 'Cô ấy là người Việt Nam.'],
                            ['japanese' => '父はいしゃです。', 'vietnamese' => 'Bố tôi là bác sĩ.'],
                        ],
                    ],
                    [
                        'particle' => '主語の省略',
                        'explanation' => 'Chủ ngữ thường được lược bỏ khi nó được làm rõ ràng.',
                        'vietnamese_meaning' => 'Lược bỏ chủ ngữ',
                        'examples' => [
                            ['japanese' => 'かれはカルロスです。(かれは) わたしの友だちです。', 'vietnamese' => 'Anh ấy là Calros. (Anh ấy) là bạn của tôi.'],
                        ],
                    ],
                    [
                        'particle' => 'N1のN2',
                        'explanation' => 'Danh từ (N2) bổ nghĩa cho danh từ khác (N1). Trợ từ 「の」 chỉ các ý nghĩa như sở hữu, sở thuộc về, phạm vi, nội dung cụ thể v.v.. Hãy ghi nhớ với thứ tự "danh từ bổ nghĩa => danh từ được bổ nghĩa".',
                        'vietnamese_meaning' => 'N2 (của) N1',
                        'examples' => [
                            ['japanese' => 'わたしの家', 'vietnamese' => 'Nhà tôi'],
                            ['japanese' => '大学のとしょかん', 'vietnamese' => 'Thư viện trường đại học.'],
                            ['japanese' => '日本語のじゅぎょう', 'vietnamese' => 'Giờ học tiếng Nhật'],
                            ['japanese' => '東京のホテル', 'vietnamese' => 'Khách sạn ở Tokyo'],
                        ],
                    ],
                    [
                        'particle' => 'N[場所] に',
                        'explanation' => 'Trợ từ「に」 chỉ nơi chốn (địa điểm) như 「日本に来ます (sang Nhật)」 「駅に着きます(đến nhà ga)」 「ソファーに すわります (ngồi vào ghế sô pha.)].',
                        'vietnamese_meaning' => 'ở N [Nơi chốn]',
                        'examples' => [
                            ['japanese' => '日本に来ます', 'vietnamese' => 'sang Nhật'],
                            ['japanese' => '駅に着きます', 'vietnamese' => 'đến nhà ga'],
                            ['japanese' => 'ソファーに すわります', 'vietnamese' => 'ngồi vào ghế sô pha'],
                        ],
                    ],
                ],
            ],
            [
                'bai' => 'Bài 2',
                'title' => '存在文 Câu tồn tại',
                'tuVung' => [
                    ['tu' => '近くに', 'romaji' => 'chikaku ni', 'nghia' => 'gần'],
                    ['tu' => '大きい', 'romaji' => 'ookii', 'nghia' => 'to'],
                    ['tu' => 'いけ', 'romaji' => 'ike', 'nghia' => 'ao'],
                    ['tu' => 'さかな', 'romaji' => 'sakana', 'nghia' => 'cá'],
                    ['tu' => 'たくさん', 'romaji' => 'takusan', 'nghia' => 'nhiều'],
                ],
                'nguPhap' => [
                    [
                        'particle' => 'N1にN2が あります。',
                        'explanation' => '「Nがあります」 biểu thị sự tồn tại của N, và 「N2」 là trợ từ chỉ chủ ngữ. Động từ 「あります」 chỉ sự tồn tại của những vật không phải động vật.',
                        'vietnamese_meaning' => 'Ở N1 có N2',
                        'examples' => [
                            ['japanese' => '町にとしょかんが あります。', 'vietnamese' => 'Ở thành phố có thư viện.'],
                            ['japanese' => '公園に いけがあります。', 'vietnamese' => 'Ở công viên có cái ao.'],
                        ],
                    ],
                    [
                        'particle' => 'N1にN2が います。',
                        'explanation' => 'Động từ 「あります」 chỉ sự tồn tại của những vật không phải động vật, còn động từ 「います」 chỉ sự tồn tại của sinh vật. Trên ngữ pháp tiếng Nhật, danh từ không phân biệt dạng số ít và số nhiều, vì vậy khi nói 「公園に人がいます」 chúng ta sẽ không thể biết được công viên sẽ có một người hay nhiều người.',
                        'vietnamese_meaning' => 'ở N1 có N2',
                        'examples' => [
                            ['japanese' => 'としょかんに学生がおおぜいいます。', 'vietnamese' => 'Ở thư viện có nhiều sinh viên.'],
                            ['japanese' => 'いま、教室に 青木先生がいます。', 'vietnamese' => 'Bây giờ ở phòng học có thầy Aoki.'],
                        ],
                    ],
                    [
                        'particle' => 'Nの中に',
                        'explanation' => '「の中に」 chỉ nơi chốn bên trong N',
                        'vietnamese_meaning' => 'Bên trong N',
                        'examples' => [
                            ['japanese' => '車の中に子どもがいます。', 'vietnamese' => 'Trong ô tô có trẻ em.'],
                            ['japanese' => 'かばんの中に本があります。', 'vietnamese' => 'Trong cặp có quyển sách.'],
                            ['japanese' => '机の上に本があります。', 'vietnamese' => 'Trên bàn có quyển sách.'],
                            ['japanese' => 'いすの下にかばんがあります。', 'vietnamese' => 'Dưới ghế có cái cặp.'],
                        ],
                    ],
                ],
            ],
        ];

        $koredeDaijoubu = [
        ];

        $gokakuDekiru = [
        ];

        $tankiMasterN5 = [
        ];

        // Insert Speed Master N5 data
        $order = 1;
        
        // Speed Master N5 - Từ vựng
        foreach ($speedMasterN5['tuVung'] as $bai) {
            N5CourseData::create([
                'section_type' => 'speed_master_n5',
                'section_key' => 'tuVung',
                'bai' => $bai['bai'],
                'title' => $bai['title'],
                'content' => $bai['words'],
                'order' => $order++,
            ]);
        }

        // Speed Master N5 - Ngữ pháp
        foreach ($speedMasterN5['nguPhap'] as $bai) {
            N5CourseData::create([
                'section_type' => 'speed_master_n5',
                'section_key' => 'nguPhap',
                'bai' => $bai['bai'],
                'title' => $bai['title'],
                'content' => $bai['grammar_points'],
                'order' => $order++,
            ]);
        }

        // Speed Master N5 - Đọc hiểu
        foreach ($speedMasterN5['docHieu'] as $mondai) {
            N5CourseData::create([
                'section_type' => 'speed_master_n5',
                'section_key' => 'docHieu',
                'bai' => $mondai['bai'],
                'title' => $mondai['title'],
                'content' => [
                    'passage' => $mondai['passage'],
                    'question' => $mondai['question'],
                    'options' => $mondai['options'],
                    'correct_answer' => $mondai['correct_answer'],
                    'explanation' => $mondai['explanation'],
                ],
                'order' => $order++,
            ]);
        }

        // Luyện đọc
        foreach ($luyenDoc as $bai) {
            N5CourseData::create([
                'section_type' => 'luyen_doc',
                'section_key' => null,
                'bai' => $bai['bai'],
                'title' => $bai['title'],
                'content' => [
                    'passage' => $bai['passage'],
                    'questions' => $bai['questions'],
                ],
                'order' => $order++,
            ]);
        }

        // Marugoto N5
        foreach ($marugotoN5 as $bai) {
            N5CourseData::create([
                'section_type' => 'marugoto_n5',
                'section_key' => null,
                'bai' => $bai['bai'],
                'title' => $bai['title'],
                'content' => [
                    'tuVung' => $bai['tuVung'],
                    'nguPhap' => $bai['nguPhap'],
                ],
                'order' => $order++,
            ]);
        }

        $this->command->info('Đã nạp dữ liệu N5 Course vào database thành công!');
    }
}

