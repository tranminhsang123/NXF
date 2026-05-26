<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alphabet;

class AlphabetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hiraganaData = [
            ['character' => 'あ', 'romaji' => 'a', 'type' => 'hiragana'],
            ['character' => 'い', 'romaji' => 'i', 'type' => 'hiragana'],
            ['character' => 'う', 'romaji' => 'u', 'type' => 'hiragana'],
            ['character' => 'え', 'romaji' => 'e', 'type' => 'hiragana'],
            ['character' => 'お', 'romaji' => 'o', 'type' => 'hiragana'],
            ['character' => 'か', 'romaji' => 'ka', 'type' => 'hiragana'],
            ['character' => 'き', 'romaji' => 'ki', 'type' => 'hiragana'],
            ['character' => 'く', 'romaji' => 'ku', 'type' => 'hiragana'],
            ['character' => 'け', 'romaji' => 'ke', 'type' => 'hiragana'],
            ['character' => 'こ', 'romaji' => 'ko', 'type' => 'hiragana'],
            ['character' => 'さ', 'romaji' => 'sa', 'type' => 'hiragana'],
            ['character' => 'し', 'romaji' => 'shi', 'type' => 'hiragana'],
            ['character' => 'す', 'romaji' => 'su', 'type' => 'hiragana'],
            ['character' => 'せ', 'romaji' => 'se', 'type' => 'hiragana'],
            ['character' => 'そ', 'romaji' => 'so', 'type' => 'hiragana'],
            ['character' => 'た', 'romaji' => 'ta', 'type' => 'hiragana'],
            ['character' => 'ち', 'romaji' => 'chi', 'type' => 'hiragana'],
            ['character' => 'つ', 'romaji' => 'tsu', 'type' => 'hiragana'],
            ['character' => 'て', 'romaji' => 'te', 'type' => 'hiragana'],
            ['character' => 'と', 'romaji' => 'to', 'type' => 'hiragana'],
            ['character' => 'な', 'romaji' => 'na', 'type' => 'hiragana'],
            ['character' => 'に', 'romaji' => 'ni', 'type' => 'hiragana'],
            ['character' => 'ぬ', 'romaji' => 'nu', 'type' => 'hiragana'],
            ['character' => 'ね', 'romaji' => 'ne', 'type' => 'hiragana'],
            ['character' => 'の', 'romaji' => 'no', 'type' => 'hiragana'],
            ['character' => 'は', 'romaji' => 'ha', 'type' => 'hiragana'],
            ['character' => 'ひ', 'romaji' => 'hi', 'type' => 'hiragana'],
            ['character' => 'ふ', 'romaji' => 'fu', 'type' => 'hiragana'],
            ['character' => 'へ', 'romaji' => 'he', 'type' => 'hiragana'],
            ['character' => 'ほ', 'romaji' => 'ho', 'type' => 'hiragana'],
            ['character' => 'ま', 'romaji' => 'ma', 'type' => 'hiragana'],
            ['character' => 'み', 'romaji' => 'mi', 'type' => 'hiragana'],
            ['character' => 'む', 'romaji' => 'mu', 'type' => 'hiragana'],
            ['character' => 'め', 'romaji' => 'me', 'type' => 'hiragana'],
            ['character' => 'も', 'romaji' => 'mo', 'type' => 'hiragana'],
            ['character' => 'や', 'romaji' => 'ya', 'type' => 'hiragana'],
            ['character' => 'ゆ', 'romaji' => 'yu', 'type' => 'hiragana'],
            ['character' => 'よ', 'romaji' => 'yo', 'type' => 'hiragana'],
            ['character' => 'ら', 'romaji' => 'ra', 'type' => 'hiragana'],
            ['character' => 'り', 'romaji' => 'ri', 'type' => 'hiragana'],
            ['character' => 'る', 'romaji' => 'ru', 'type' => 'hiragana'],
            ['character' => 'れ', 'romaji' => 're', 'type' => 'hiragana'],
            ['character' => 'ろ', 'romaji' => 'ro', 'type' => 'hiragana'],
            ['character' => 'わ', 'romaji' => 'wa', 'type' => 'hiragana'],
            ['character' => 'を', 'romaji' => 'wo', 'type' => 'hiragana'],
            ['character' => 'ん', 'romaji' => 'n', 'type' => 'hiragana'],
        ];

        foreach ($hiraganaData as $data) {
            Alphabet::create($data);
        }

        $katakanaData = [
            ['character' => 'ア', 'romaji' => 'a', 'type' => 'katakana'],
            ['character' => 'イ', 'romaji' => 'i', 'type' => 'katakana'],
            ['character' => 'ウ', 'romaji' => 'u', 'type' => 'katakana'],
            ['character' => 'エ', 'romaji' => 'e', 'type' => 'katakana'],
            ['character' => 'オ', 'romaji' => 'o', 'type' => 'katakana'],
            ['character' => 'カ', 'romaji' => 'ka', 'type' => 'katakana'],
            ['character' => 'キ', 'romaji' => 'ki', 'type' => 'katakana'],
            ['character' => 'ク', 'romaji' => 'ku', 'type' => 'katakana'],
            ['character' => 'ケ', 'romaji' => 'ke', 'type' => 'katakana'],
            ['character' => 'コ', 'romaji' => 'ko', 'type' => 'katakana'],
            ['character' => 'サ', 'romaji' => 'sa', 'type' => 'katakana'],
            ['character' => 'シ', 'romaji' => 'shi', 'type' => 'katakana'],
            ['character' => 'ス', 'romaji' => 'su', 'type' => 'katakana'],
            ['character' => 'セ', 'romaji' => 'se', 'type' => 'katakana'],
            ['character' => 'ソ', 'romaji' => 'so', 'type' => 'katakana'],
            ['character' => 'タ', 'romaji' => 'ta', 'type' => 'katakana'],
            ['character' => 'チ', 'romaji' => 'chi', 'type' => 'katakana'],
            ['character' => 'ツ', 'romaji' => 'tsu', 'type' => 'katakana'],
            ['character' => 'テ', 'romaji' => 'te', 'type' => 'katakana'],
            ['character' => 'ト', 'romaji' => 'to', 'type' => 'katakana'],
            ['character' => 'ナ', 'romaji' => 'na', 'type' => 'katakana'],
            ['character' => 'ニ', 'romaji' => 'ni', 'type' => 'katakana'],
            ['character' => 'ヌ', 'romaji' => 'nu', 'type' => 'katakana'],
            ['character' => 'ネ', 'romaji' => 'ne', 'type' => 'katakana'],
            ['character' => 'ノ', 'romaji' => 'no', 'type' => 'katakana'],
            ['character' => 'ハ', 'romaji' => 'ha', 'type' => 'katakana'],
            ['character' => 'ヒ', 'romaji' => 'hi', 'type' => 'katakana'],
            ['character' => 'フ', 'romaji' => 'fu', 'type' => 'katakana'],
            ['character' => 'ヘ', 'romaji' => 'he', 'type' => 'katakana'],
            ['character' => 'ホ', 'romaji' => 'ho', 'type' => 'katakana'],
            ['character' => 'マ', 'romaji' => 'ma', 'type' => 'katakana'],
            ['character' => 'ミ', 'romaji' => 'mi', 'type' => 'katakana'],
            ['character' => 'ム', 'romaji' => 'mu', 'type' => 'katakana'],
            ['character' => 'メ', 'romaji' => 'me', 'type' => 'katakana'],
            ['character' => 'モ', 'romaji' => 'mo', 'type' => 'katakana'],
            ['character' => 'ヤ', 'romaji' => 'ya', 'type' => 'katakana'],
            ['character' => 'ユ', 'romaji' => 'yu', 'type' => 'katakana'],
            ['character' => 'ヨ', 'romaji' => 'yo', 'type' => 'katakana'],
            ['character' => 'ラ', 'romaji' => 'ra', 'type' => 'katakana'],
            ['character' => 'リ', 'romaji' => 'ri', 'type' => 'katakana'],
            ['character' => 'ル', 'romaji' => 'ru', 'type' => 'katakana'],
            ['character' => 'レ', 'romaji' => 're', 'type' => 'katakana'],
            ['character' => 'ロ', 'romaji' => 'ro', 'type' => 'katakana'],
            ['character' => 'ワ', 'romaji' => 'wa', 'type' => 'katakana'],
            ['character' => 'ヲ', 'romaji' => 'wo', 'type' => 'katakana'],
            ['character' => 'ン', 'romaji' => 'n', 'type' => 'katakana'],
        ];

        foreach ($katakanaData as $data) {
            Alphabet::create($data);
        }
         // BẢNG RŌMAJI CƠ BẢN (GOJŪON – 50 ÂM)
         $gojuon = [
            // あ行
            ['character' => 'a', 'romaji' => 'a', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'i', 'romaji' => 'i', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'u', 'romaji' => 'u', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'e', 'romaji' => 'e', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'o', 'romaji' => 'o', 'type' => 'romaji', 'category' => 'seion'],
            
            // か行
            ['character' => 'ka', 'romaji' => 'ka', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ki', 'romaji' => 'ki', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ku', 'romaji' => 'ku', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ke', 'romaji' => 'ke', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ko', 'romaji' => 'ko', 'type' => 'romaji', 'category' => 'seion'],
            
            // さ行
            ['character' => 'sa', 'romaji' => 'sa', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'shi', 'romaji' => 'shi', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'su', 'romaji' => 'su', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'se', 'romaji' => 'se', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'so', 'romaji' => 'so', 'type' => 'romaji', 'category' => 'seion'],
            
            // た行
            ['character' => 'ta', 'romaji' => 'ta', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'chi', 'romaji' => 'chi', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'tsu', 'romaji' => 'tsu', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'te', 'romaji' => 'te', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'to', 'romaji' => 'to', 'type' => 'romaji', 'category' => 'seion'],
            
            // な行
            ['character' => 'na', 'romaji' => 'na', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ni', 'romaji' => 'ni', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'nu', 'romaji' => 'nu', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ne', 'romaji' => 'ne', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'no', 'romaji' => 'no', 'type' => 'romaji', 'category' => 'seion'],
            
            // は行
            ['character' => 'ha', 'romaji' => 'ha', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'hi', 'romaji' => 'hi', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'fu', 'romaji' => 'fu', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'he', 'romaji' => 'he', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ho', 'romaji' => 'ho', 'type' => 'romaji', 'category' => 'seion'],
            
            // ま行
            ['character' => 'ma', 'romaji' => 'ma', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'mi', 'romaji' => 'mi', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'mu', 'romaji' => 'mu', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'me', 'romaji' => 'me', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'mo', 'romaji' => 'mo', 'type' => 'romaji', 'category' => 'seion'],
            
            // や行
            ['character' => 'ya', 'romaji' => 'ya', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'yu', 'romaji' => 'yu', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'yo', 'romaji' => 'yo', 'type' => 'romaji', 'category' => 'seion'],
            
            // ら行
            ['character' => 'ra', 'romaji' => 'ra', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ri', 'romaji' => 'ri', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ru', 'romaji' => 'ru', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 're', 'romaji' => 're', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'ro', 'romaji' => 'ro', 'type' => 'romaji', 'category' => 'seion'],
            
            // わ行
            ['character' => 'wa', 'romaji' => 'wa', 'type' => 'romaji', 'category' => 'seion'],
            ['character' => 'wo', 'romaji' => 'wo', 'type' => 'romaji', 'category' => 'seion'],
            
            // ん
            ['character' => 'n', 'romaji' => 'n', 'type' => 'romaji', 'category' => 'seion'],
        ];

        // ÂM ĐỤC (Dakuon)
        $dakuon = [
            // が行
            ['character' => 'ga', 'romaji' => 'ga', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'gi', 'romaji' => 'gi', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'gu', 'romaji' => 'gu', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'ge', 'romaji' => 'ge', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'go', 'romaji' => 'go', 'type' => 'romaji', 'category' => 'dakuon'],
            
            // ざ行
            ['character' => 'za', 'romaji' => 'za', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'ji', 'romaji' => 'ji', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'zu', 'romaji' => 'zu', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'ze', 'romaji' => 'ze', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'zo', 'romaji' => 'zo', 'type' => 'romaji', 'category' => 'dakuon'],
            
            // だ行
            ['character' => 'da', 'romaji' => 'da', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'ji', 'romaji' => 'ji', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'zu', 'romaji' => 'zu', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'de', 'romaji' => 'de', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'do', 'romaji' => 'do', 'type' => 'romaji', 'category' => 'dakuon'],
            
            // ば行
            ['character' => 'ba', 'romaji' => 'ba', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'bi', 'romaji' => 'bi', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'bu', 'romaji' => 'bu', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'be', 'romaji' => 'be', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'bo', 'romaji' => 'bo', 'type' => 'romaji', 'category' => 'dakuon'],
            
            // ぱ行
            ['character' => 'pa', 'romaji' => 'pa', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'pi', 'romaji' => 'pi', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'pu', 'romaji' => 'pu', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'pe', 'romaji' => 'pe', 'type' => 'romaji', 'category' => 'dakuon'],
            ['character' => 'po', 'romaji' => 'po', 'type' => 'romaji', 'category' => 'dakuon'],
        ];

        // ÂM GHÉP (Yōon)
        $yoon = [
            // きゃ行
            ['character' => 'kya', 'romaji' => 'kya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'kyu', 'romaji' => 'kyu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'kyo', 'romaji' => 'kyo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // しゃ行
            ['character' => 'sha', 'romaji' => 'sha', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'shu', 'romaji' => 'shu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'sho', 'romaji' => 'sho', 'type' => 'romaji', 'category' => 'yoon'],
            
            // ちゃ行
            ['character' => 'cha', 'romaji' => 'cha', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'chu', 'romaji' => 'chu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'cho', 'romaji' => 'cho', 'type' => 'romaji', 'category' => 'yoon'],
            
            // にゃ行
            ['character' => 'nya', 'romaji' => 'nya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'nyu', 'romaji' => 'nyu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'nyo', 'romaji' => 'nyo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // ひゃ行
            ['character' => 'hya', 'romaji' => 'hya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'hyu', 'romaji' => 'hyu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'hyo', 'romaji' => 'hyo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // みゃ行
            ['character' => 'mya', 'romaji' => 'mya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'myu', 'romaji' => 'myu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'myo', 'romaji' => 'myo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // りゃ行
            ['character' => 'rya', 'romaji' => 'rya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'ryu', 'romaji' => 'ryu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'ryo', 'romaji' => 'ryo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // ぎゃ行
            ['character' => 'gya', 'romaji' => 'gya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'gyu', 'romaji' => 'gyu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'gyo', 'romaji' => 'gyo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // じゃ行
            ['character' => 'ja', 'romaji' => 'ja', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'ju', 'romaji' => 'ju', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'jo', 'romaji' => 'jo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // びゃ行
            ['character' => 'bya', 'romaji' => 'bya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'byu', 'romaji' => 'byu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'byo', 'romaji' => 'byo', 'type' => 'romaji', 'category' => 'yoon'],
            
            // ぴゃ行
            ['character' => 'pya', 'romaji' => 'pya', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'pyu', 'romaji' => 'pyu', 'type' => 'romaji', 'category' => 'yoon'],
            ['character' => 'pyo', 'romaji' => 'pyo', 'type' => 'romaji', 'category' => 'yoon'],
        ];

        // Gộp tất cả Rōmaji
        $romajiData = array_merge($gojuon, $dakuon, $yoon);

        foreach ($romajiData as $data) {
            Alphabet::create($data);
        } 
        
    }
}
