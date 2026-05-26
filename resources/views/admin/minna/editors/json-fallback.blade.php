<div class="border border-gray-200 rounded-lg p-4">
    <p class="text-sm text-amber-800 mb-2">Loại "{{ $key }}" có cấu trúc phức tạp. Chỉnh sửa trực tiếp bằng JSON:</p>
    <textarea id="content_raw" rows="15" class="w-full border rounded px-3 py-2 font-mono text-sm"
              placeholder="{}">{{ is_array($content) ? json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (is_string($content) ? $content : '') }}</textarea>
</div>

<textarea name="content" id="content_hidden" class="hidden" rows="1"></textarea>

<script>
document.getElementById('section-form').addEventListener('submit', function(e) {
    const raw = document.getElementById('content_raw');
    const hidden = document.getElementById('content_hidden');
    const val = (raw && raw.value) ? raw.value.trim() : '';
    if (val === '') {
        hidden.value = '';
        return;
    }
    try {
        JSON.parse(val);
        hidden.value = val;
    } catch (err) {
        e.preventDefault();
        alert('JSON không hợp lệ: ' + err.message);
    }
});
</script>
