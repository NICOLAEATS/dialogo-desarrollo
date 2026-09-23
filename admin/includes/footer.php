<?php
$flash = flash();
?>
<!-- ===== Main Content End ===== -->
      </div>
      <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <?php if ($flash) : ?>
    <!-- Notificacion flash -->
    <div
      x-data="{show: true}"
      x-show="show"
      x-init="setTimeout(() => show = false, 4000)"
      x-transition
      class="fixed right-4 top-4 z-99999 flex w-full max-w-sm items-center gap-3 rounded-xl border p-4 shadow-theme-lg dark:shadow-none
        <?= $flash['type'] === 'error' ? 'border-error-200 bg-error-50 dark:border-error-500/30 dark:bg-error-500/10' : 'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10' ?>"
    >
      <span
        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
        <?= $flash['type'] === 'error' ? 'bg-error-500 text-white' : 'bg-success-500 text-white' ?>"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <?php if ($flash['type'] === 'error') : ?>
            <path d="M18.364 5.636L5.636 18.364M5.636 5.636L18.364 18.364" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          <?php else : ?>
            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          <?php endif; ?>
        </svg>
      </span>
      <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90"><?= e($flash['msg']) ?></p>
      <button class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" @click="show = false" aria-label="Cerrar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>
    <?php endif; ?>

  <script defer src="assets/js/bundle.js"></script>
  </body>
</html>