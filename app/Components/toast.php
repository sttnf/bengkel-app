<?php if (!empty($_SESSION['toasts'])): ?>
    <div class="fixed z-50 w-full bottom-4 px-4 space-y-2 sm:w-auto sm:bottom-4 sm:right-4 sm:left-auto sm:top-auto">
        <?php foreach ($_SESSION['toasts'] as $toast): ?>
            <?php
            $type = array_key_first($toast);
            $data = $toast[$type];

            $title = $data['title'] ?? '';
            $message = $data['message'] ?? (is_string($data) ? $data : '');

            $bg = [
                'success' => 'bg-green-50 border-green-200 text-green-800',
                'error' => 'bg-red-50 border-red-200 text-red-800',
                'info' => 'bg-primary-50 border-primary-200 text-primary-800',
                'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800'
            ][$type] ?? 'bg-gray-50 border-gray-200 text-gray-800';

            $icon = [
                'success' => 'check-circle text-green-500',
                'error' => 'x-circle text-red-500',
                'info' => 'info text-primary-500',
                'warning' => 'alert-triangle text-yellow-500'
            ][$type] ?? 'bell text-gray-500';
            ?>
            <div class="toast w-full sm:max-w-sm sm:w-auto rounded-lg shadow-lg border p-4 <?= $bg ?>">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="<?= explode(' ', $icon)[0] ?>"
                           class="h-5 w-5 mt-0.5 <?= explode(' ', $icon)[1] ?>"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <?php if ($title): ?>
                            <p class="text-sm font-semibold"><?= htmlspecialchars($title) ?></p>
                        <?php endif; ?>
                        <p class="text-sm"><?= htmlspecialchars($message) ?></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" class="inline-flex rounded-md p-1.5 text-gray-500 hover:bg-gray-100"
                                onclick="this.closest('.toast').remove()">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        setTimeout(() => {
            document.querySelectorAll('.toast').forEach(el => el.remove());
        }, 3000);
    </script>
    <?php unset($_SESSION['toasts']); ?>
<?php endif; ?>
