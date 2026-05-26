<div class="mt-6 border border-amber-200 rounded-lg p-4 bg-amber-50 text-amber-950">
    <p class="text-sm font-medium">Chưa có form từng trường cho tổ hợp <strong>Loại section</strong> + <strong>Section key</strong> này.</p>
    <p class="text-xs mt-1 text-amber-900/90">Nhập nội dung dưới dạng <strong>JSON</strong> (mảng hoặc object). Lưu sẽ kiểm tra JSON hợp lệ. Các ô form khác đang tắt sẽ không gửi lên server.</p>
    <label for="content_json" class="sr-only">Nội dung JSON</label>
    <textarea id="content_json" name="content_json" rows="14" spellcheck="false"
              class="mt-3 w-full font-mono text-sm border border-amber-300 rounded-lg px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-amber-400 focus:border-amber-400">{{ $jsonSeed ?? '' }}</textarea>
</div>
