<?php
// Encabezado superior del panel (barra compacta en una sola fila).
$userAvatar = $_SESSION['avatar_url'] ?? 'assets/images/user/user-01.jpg';
$userNombre = $_SESSION['nombre_completo'] ?? 'Usuario';
$userRol    = $_SESSION['rol'] ?? '';

if (!isset($pageTitle) || $pageTitle === '') {
    $titulosPaginaHeader = [
        'dashboard' => 'Dashboard de Contenido',
        'usuarios' => 'Gestion de Usuarios',
        'autores' => 'Gestion de Autores',
        'reportajes' => 'Gestion de Reportajes',
        'reportajes_fotos' => 'Fotos de Reportajes',
        'noticias' => 'Gestion de Noticias',
        'boletines' => 'Gestion de Boletines',
        'podcasts' => 'Gestion de Podcasts',
        'videos' => 'Gestion de Videos',
        'pdfs' => 'Gestion de Documentos PDF',
        'fotos' => 'Gestion de Fotos',
        '' => 'Panel de Administracion',
    ];
    $pageTitle = $titulosPaginaHeader[$page ?? ''] ?? 'Panel de Administracion';
}
?>
<!-- ===== Page Content Wrapper (lo cierra footer.php) ===== -->
<div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
<!-- ===== Header Start ===== -->
<header
  x-data="{profileToggle: false}"
  class="sticky top-0 z-99999 flex w-full shrink-0 items-center justify-between gap-3 border-b border-gray-200 bg-white px-3 py-3 sm:px-5 lg:px-6 dark:border-gray-800 dark:bg-gray-900"
>
  <!-- Izquierda: hamburguesa + titulo de seccion -->
  <div class="flex min-w-0 items-center gap-2">
    <button
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 lg:h-11 lg:w-11 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800"
      @click.stop="sidebarToggle = !sidebarToggle"
      aria-label="Alternar menu"
    >
      <svg class="hidden fill-current lg:block" width="18" height="14" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z" fill="currentColor" />
      </svg>
      <svg class="fill-current lg:hidden" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 6C3.25 5.58579 3.58579 5.25 4 5.25L20 5.25C20.4142 5.25 20.75 5.58579 20.75 6C20.75 6.41421 20.4142 6.75 20 6.75L4 6.75C3.58579 6.75 3.25 6.41422 3.25 6ZM3.25 18C3.25 17.5858 3.58579 17.25 4 17.25L20 17.25C20.4142 17.25 20.75 17.5858 20.75 18C20.75 18.4142 20.4142 18.75 20 18.75L4 18.75C3.58579 18.75 3.25 18.4142 3.25 18ZM4 11.25C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75L12 12.75C12.4142 12.75 12.75 12.4142 12.75 12C12.75 11.5858 12.4142 11.25 12 11.25L4 11.25Z" fill="currentColor" />
      </svg>
    </button>

    <a href="index.php" class="shrink-0 lg:hidden">
      <img src="assets/images/logo/logo.png" alt="Diálogo y Desarrollo" style="height:38px;width:auto" />
    </a>

    <div class="hidden min-w-0 flex-col lg:flex">
      <h1 class="truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90"><?= e($pageTitle) ?></h1>
      <p class="truncate text-theme-xs text-gray-500 dark:text-gray-400">Bienvenido, <?= e($userNombre) ?></p>
    </div>
  </div>

  <!-- Derecha: ver sitio + modo oscuro + perfil -->
  <div class="flex shrink-0 items-center gap-2 sm:gap-3">
    <!-- Ver sitio publico -->
    <a
      href="../index.php"
      target="_blank"
      class="flex h-11 items-center gap-2 rounded-full border border-gray-200 bg-white px-4 text-theme-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
    >
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-brand-500">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.86625 2.27204C8.83174 1.90383 9.10533 1.57878 9.47354 1.54427C10.2738 1.46775 11.0856 1.59835 11.8691 1.83803C11.8691 1.83803 11.8714 1.83803 11.8737 1.83803C13.0259 2.23666 14.0332 2.96528 14.82 3.9755C15.55 4.9116 16.0782 6.07328 16.0782 7.33767C16.0782 8.60205 15.55 9.76374 14.82 10.6998C14.0332 11.7101 13.0259 12.4387 11.8737 12.8373C11.0856 13.077 10.2738 13.2076 9.47354 13.1311C9.10533 13.0966 8.83174 12.7715 8.86625 12.4033C8.90076 12.0351 9.22581 11.7615 9.59402 11.796C10.162 11.8505 10.7382 11.7642 11.3096 11.5983C12.1191 11.3418 12.8206 10.8305 13.3427 10.146C13.8042 9.54334 14.0794 8.79744 14.0794 7.99834C14.0794 7.19925 13.8042 6.45334 13.3427 5.8507C12.8206 5.16618 12.1191 4.65487 11.3096 4.39833C10.7382 4.23249 10.162 4.14622 9.59402 4.2007C9.22581 4.23521 8.90076 3.96162 8.86625 3.59341C8.83174 3.22521 9.10533 2.90015 9.47354 2.86564C9.43176 2.86174 9.38991 2.85817 9.348 2.85492C9.18754 2.84112 9.02717 2.8357 8.86625 2.27204Z" fill="currentColor"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.87887 4.71128C5.1738 4.42164 5.65135 4.42563 5.94099 4.72056C6.23063 5.01549 6.22664 5.49304 5.93171 5.78268C4.76338 6.92992 4.09057 8.37449 4.09057 9.847C4.09057 11.3195 4.76338 12.764 5.93171 13.9112C5.93171 13.9112 5.93171 13.9112 5.93171 13.9112C6.22664 14.2009 6.23063 14.6784 5.94099 14.9734C5.65135 15.2683 5.1738 15.2723 4.87887 14.9826C3.39975 13.5296 2.41885 11.758 2.11649 9.847C1.81413 7.936 3.05569 5.82508 4.87887 4.71128Z" fill="currentColor"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.9278 6.77563C8.90373 6.36993 9.13909 5.9925 9.51976 5.86569C9.90154 5.73853 10.3189 5.90427 10.4939 6.25878C10.6849 6.64753 11.0706 6.95708 11.7796 7.33793C11.7803 7.33837 11.781 7.33881 11.7817 7.33925C12.1953 7.56692 12.5798 7.83231 12.9278 8.17473C13.2247 8.4771 13.2247 8.91754 12.9278 9.21991C12.22 9.96932 10.9 10.6202 9.84391 9.76405L9.83837 9.75976C9.83837 9.75976 9.3966 9.3932 9.3966 9.3932C9.3966 9.3932 9.16871 9.22758 8.93709 9.22758C8.85303 9.22758 8.82766 9.24457 8.78435 9.27541C8.59346 9.47382 8.39187 9.66578 8.26041 9.89917C8.68716 10.2312 9.1853 10.2424 9.3966 10.0506C9.20148 10.1353 8.99884 10.1695 8.7833 10.1695L8.71194 10.1695C8.37999 10.1695 8.04133 10.3219 7.85798 10.6195C7.20501 11.6171 7.28136 12.8021 7.99249 13.4392C8.25671 13.6898 7.78744 14.0868 7.52323 14.0868C7.52323 14.0868 7.12652 13.7545 7.12652 13.7545C5.85974 12.5809 5.80335 10.7609 7.0191 9.42768C7.67125 8.70884 8.53269 8.1326 9.36787 7.7498C8.88208 7.5553 8.4639 7.49579 8.1129 7.29482C8.17171 7.0942 8.67171 7.08572 8.9278 6.77563Z" fill="currentColor"/>
      </svg>
      Ver sitio
    </a>

    <!-- Dark Mode Toggler -->
    <button
      class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
      @click.prevent="darkMode = !darkMode"
      aria-label="Modo oscuro"
    >
      <svg class="hidden dark:block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035Z" fill="currentColor" />
      </svg>
      <svg class="dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM4.01902 14.9204C4.31191 14.6135 4.78679 14.6135 5.07968 14.9204C5.37258 15.2274 5.37258 15.7161 5.07968 16.0231L4.1958 16.9229C3.9029 17.2298 3.42803 17.2298 3.13513 16.9229C2.84224 16.6159 2.84224 16.1272 3.13513 15.8202L4.01902 14.9204ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001Z" fill="currentColor" />
      </svg>
    </button>

    <!-- Perfil -->
    <div class="relative" x-data="{open: false}" @click.outside="open = false">
      <button
        class="flex items-center gap-1.5 rounded-full border border-gray-200 bg-white p-1 pr-3 transition hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
        @click="open = !open"
      >
        <img
          class="h-8 w-8 rounded-full object-cover"
          src="<?= e($userAvatar) ?>"
          alt="<?= e($userNombre) ?>"
        />
        <span class="hidden text-theme-sm text-gray-700 sm:block dark:text-gray-300">
          <?= e($userNombre) ?>
        </span>
        <svg class="fill-gray-500 dark:fill-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M3.80766 5.64149C4.10055 5.3486 4.57543 5.3486 4.86832 5.64149L8.00049 8.77366L11.1327 5.64149C11.4256 5.3486 11.9004 5.3486 12.1933 5.64149C12.4862 5.93438 12.4862 6.40926 12.1933 6.70215L8.5308 10.3647C8.23791 10.6576 7.76303 10.6576 7.47014 10.3647L3.80766 6.70215C3.51477 6.40926 3.51477 5.93438 3.80766 5.64149Z" fill="currentColor" />
        </svg>
      </button>

      <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute right-0 z-999 mt-2 w-64 rounded-xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
      >
        <div class="flex items-center gap-3 border-b border-gray-200 pb-3 dark:border-gray-800">
          <img class="h-10 w-10 rounded-full object-cover" src="<?= e($userAvatar) ?>" alt="Perfil" />
          <div>
            <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90"><?= e($userNombre) ?></p>
            <p class="text-theme-xs text-gray-500 dark:text-gray-400"><?= e($userRol) ?></p>
          </div>
        </div>
        <div class="mt-3">
          <a
            href="logout.php"
            class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-theme-sm font-medium text-error-700 hover:bg-error-50 dark:text-error-500 dark:hover:bg-error-500/10"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M16 17L21 12M21 12L16 7M21 12H9M12 21H6C5.44772 21 5 20.5523 5 20V4C5 3.44772 5.44772 3 6 3H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Cerrar sesion
          </a>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- ===== Header End ===== -->