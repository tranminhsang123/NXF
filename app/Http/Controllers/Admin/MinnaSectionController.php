<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MinnaSection;
use Illuminate\Http\Request;

class MinnaSectionController extends Controller
{
    /**
     * Form chỉnh sửa nội dung section (từ vựng, ngữ pháp, ...)
     */
    public function edit(MinnaSection $minnaSection)
    {
        $minnaSection->load('lesson');
        return view('admin.minna.section-edit', compact('minnaSection'));
    }

    /**
     * Cập nhật nội dung section
     */
    public function update(Request $request, MinnaSection $minnaSection)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable',
            'media_url' => 'nullable|url|max:500',
        ]);

        $minnaSection->title = $request->filled('title') ? $request->title : $minnaSection->title;
        $minnaSection->media_url = $request->media_url;

        $contentInput = $request->content;
        if (is_array($contentInput)) {
            $minnaSection->content = $this->normalizeContentFromForm($contentInput, $minnaSection->key);
        } else {
            $contentRaw = trim((string) ($contentInput ?? ''));
            if ($contentRaw === '') {
                $minnaSection->content = null;
            } else {
                $decoded = json_decode($contentRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withErrors(['content' => 'JSON không hợp lệ: ' . json_last_error_msg()])->withInput();
                }
                $minnaSection->content = $decoded;
            }
        }

        $minnaSection->save();

        return redirect()
            ->route('admin.minna.show', $minnaSection->lesson)
            ->with('success', 'Nội dung "' . $minnaSection->title . '" đã được cập nhật thành công!');
    }

    /**
     * Chuẩn hóa dữ liệu từ form thành cấu trúc lưu DB
     */
    private function normalizeContentFromForm(array $input, string $sectionKey): ?array
    {
        if ($sectionKey === 'tu-vung') {
            $result = [];
            $cats = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];
            foreach ($cats as $cat) {
                $items = $input[$cat] ?? [];
                if (!is_array($items)) continue;
                $filtered = [];
                foreach ($items as $row) {
                    if (!is_array($row)) continue;
                    $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);
                    $main = $row['tu_vung'] ?? $row['jp'] ?? '';
                    $nghia = $row['nghia'] ?? '';
                    if ($main !== '' || $nghia !== '') {
                        $filtered[] = array_filter($row, fn($v) => $v !== '' && $v !== null);
                    }
                }
                if (!empty($filtered)) $result[$cat] = $filtered;
            }
            return empty($result) ? null : $result;
        }

        if ($sectionKey === 'luyen-doc') {
            $sentences = $input['sentences'] ?? [];
            if (!is_array($sentences)) return null;
            $sentences = array_values(array_filter(array_map('trim', $sentences)));
            return empty($sentences) ? null : ['sentences' => $sentences];
        }

        if ($sectionKey === 'hoi-thoai') {
            $dialogue = $input['dialogue'] ?? [];
            if (!is_array($dialogue)) return null;
            $filtered = [];
            foreach ($dialogue as $line) {
                if (!is_array($line)) continue;
                $jp = trim($line['jp'] ?? '');
                if ($jp !== '' || trim($line['speaker'] ?? '') !== '') {
                    $filtered[] = array_filter([
                        'speaker' => trim($line['speaker'] ?? ''),
                        'jp' => $jp,
                        'romaji' => trim($line['romaji'] ?? '') ?: null,
                    ], fn($v) => $v !== '');
                }
            }
            return empty($filtered) ? null : ['dialogue' => $filtered];
        }

        if ($sectionKey === 'han-tu') {
            $filtered = [];
            foreach ($input as $key => $row) {
                if (!is_array($row) || !is_numeric($key)) continue;
                $kanji = trim($row['kanji'] ?? '');
                if ($kanji === '') continue;
                $item = [
                    'kanji' => $kanji,
                    'han_viet' => trim($row['han_viet'] ?? '') ?: null,
                    'nghia_vi' => trim($row['nghia_vi'] ?? '') ?: null,
                    'tu_vung' => trim($row['tu_vung'] ?? '') ?: null,
                ];
                $on = trim($row['onyomi'] ?? '');
                if ($on !== '') $item['onyomi'] = array_map('trim', explode(',', $on));
                $kun = trim($row['kunyomi'] ?? '');
                if ($kun !== '') $item['kunyomi'] = array_map('trim', explode(',', $kun));
                $filtered[] = array_filter($item, fn($v) => $v !== '' && $v !== null);
            }
            return empty($filtered) ? null : array_values($filtered);
        }

        if ($sectionKey === 'ngu-phap') {
            return $this->normalizeNguPhap($input);
        }

        return $input;
    }

    /**
     * Chuẩn hóa dữ liệu ngữ pháp từ form
     */
    private function normalizeNguPhap(array $input): ?array
    {
        $result = [];
        foreach ($input as $key => $row) {
            if (!is_array($row) || !is_numeric($key)) continue;
            $title = trim($row['title'] ?? '');
            $patternText = trim($row['pattern_text'] ?? '');
            $explainText = trim($row['explain_text'] ?? '');
            $notesText = trim($row['notes_text'] ?? '');
            $examplesText = trim($row['examples_text'] ?? '');
            if ($title === '' && $patternText === '' && $explainText === '' && $examplesText === '') continue;

            $grammar = [];
            if ($title !== '') $grammar['title'] = $title;

            // Parse pattern: "key: value" lines → object, else single string
            if ($patternText !== '') {
                $lines = preg_split('/\r\n|\r|\n/', $patternText);
                $hasColon = false;
                $parsed = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    if (str_contains($line, ':')) {
                        $hasColon = true;
                        $pos = strpos($line, ':');
                        $k = trim(substr($line, 0, $pos));
                        $parsed[$k] = trim(substr($line, $pos + 1));
                    } elseif (!$hasColon && empty($parsed)) {
                        $grammar['pattern'] = $line;
                        break;
                    }
                }
                if ($hasColon && !empty($parsed)) $grammar['pattern'] = $parsed;
                elseif (!isset($grammar['pattern']) && !empty($lines)) $grammar['pattern'] = trim($lines[0]);
            }

            // Parse explain: each line → array item. "key: val" → associative
            if ($explainText !== '') {
                $lines = preg_split('/\r\n|\r|\n/', $explainText);
                $explainArr = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    if (str_contains($line, ':') && preg_match('/^([a-z0-9_]+)\s*:\s*(.+)$/i', $line, $m)) {
                        $explainArr[trim($m[1])] = trim($m[2]);
                    } else {
                        $explainArr[] = $line;
                    }
                }
                $grammar['explain'] = $explainArr;
            }

            // Parse notes: each line → array
            if ($notesText !== '') {
                $grammar['notes'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $notesText))));
            }

            // Parse examples: "jp|nghia" per line
            if ($examplesText !== '') {
                $examples = [];
                foreach (preg_split('/\r\n|\r|\n/', $examplesText) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $parts = array_map('trim', explode('|', $line, 2));
                    if (($parts[0] ?? '') !== '') $examples[] = ['jp' => $parts[0], 'nghia' => $parts[1] ?? ''];
                }
                if (!empty($examples)) $grammar['examples'] = $examples;
            }

            $result[] = $grammar;
        }
        return empty($result) ? null : $result;
    }
}
