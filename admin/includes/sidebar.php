<?php
// Barra lateral (sidebar) - menu adaptado a la base de datos dialogoydesarrollo.
$pageActive = $page ?? '';
$p = fn($name) => htmlspecialchars($name ?? '');
$isActive = fn($name) => $pageActive === $name ? 'menu-item-active' : 'menu-item-inactive';
$isIcon = fn($name) => $pageActive === $name ? 'menu-item-icon-active' : 'menu-item-icon-inactive';
?>
<!-- ===== Sidebar Start ===== -->
<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
  <!-- SIDEBAR HEADER -->
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 sidebar-header pb-7"
  >
    <a href="index.php">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img src="assets/images/logo/logo.png" alt="Diálogo y Desarrollo" class="h-10 w-10 object-contain" />
      </span>
      <img
        class="logo-icon"
        :class="sidebarToggle ? 'lg:block' : 'hidden'"
        src="assets/images/logo/logo.png"
        alt="Diálogo y Desarrollo"
        style="max-height:40px;max-width:64px;object-fit:contain"
      />
    </a>
  </div>
  <!-- SIDEBAR HEADER -->

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
    <nav>
      <!-- Grupo: PRINCIPAL -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            Principal
          </span>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Ver Sitio Publico -->
          <li>
            <a
              href="../index.php"
              target="_blank"
              class="menu-item group"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="menu-item-icon active remove-[&>path:last-child]:fill-brand-500">
                <path d="M14 4.75H16.5686C16.7924 4.75 17.0069 4.83839 17.1635 4.99508L18.7549 6.58652C19.0882 6.91986 19.0882 7.45485 18.7549 7.78819L15.7882 10.7549C15.4549 11.0882 14.9199 11.0882 14.5865 10.7549L13.2451 9.41347C12.9118 9.08013 12.9118 8.54514 13.2451 8.2118L14 7.45689V4.75Z" fill="#4650F8" opacity="0.2"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.25C5.50736 3.25 4.5 4.25736 4.5 5.5V18.5C4.5 19.7426 5.50736 20.75 6.75 20.75H17.25C18.4926 20.75 19.5 19.7426 19.5 18.5V9.75C19.5 9.33579 19.1642 9 18.75 9C18.3358 9 18 9.33579 18 9.75V18.5C18 18.9142 17.6642 19.25 17.25 19.25H6.75C6.33579 19.25 6 18.9142 6 18.5V5.5C6 5.08579 6.33579 4.75 6.75 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H6.75ZM17.75 5.75C17.75 5.33579 17.4142 5 17 5H15.25V6.75H17C17.4142 6.75 17.75 6.41421 17.75 6V5.75ZM13.75 5V6.75H15.25V5H13.75ZM12.25 6.75H13.75V5H12.25V6.75ZM17.75 7.75H16V9.5H17.75V7.75ZM14.5 7.75H16V9.5H14.5V7.75ZM17.5 10H17.75V11.75C17.75 12.1642 17.4142 12.5 17 12.5C16.5858 12.5 16.25 12.1642 16.25 11.75V10H14.5V11.75C14.5 12.1642 14.1642 12.5 13.75 12.5C13.3358 12.5 13 12.1642 13 11.75V10H11.25V11.75C11.25 12.1642 10.9142 12.5 10.5 12.5C10.0858 12.5 9.75 12.1642 9.75 11.75V10H8V11.75C8 12.1642 7.66421 12.5 7.25 12.5C6.83579 12.5 6.5 12.1642 6.5 11.75V10H5.75C5.33579 10 5 9.66421 5 9.25C5 8.83579 5.33579 8.5 5.75 8.5H6.5V6.75C6.5 6.33579 6.83579 6 7.25 6C7.66421 6 8 6.33579 8 6.75V8.5H9.75V6.75C9.75 6.33579 10.0858 6 10.5 6C10.9142 6 11.25 6.33579 11.25 6.75V8.5H13V6.75C13 6.33579 13.3358 6 13.75 6C14.1642 6 14.5 6.33579 14.5 6.75V8.5H16.25V6.75C16.25 6.33579 16.5858 6 17 6C17.4142 6 17.75 6.33579 17.75 6.75V8.5H18.5C18.9142 8.5 19.25 8.83579 19.25 9.25C19.25 9.66421 18.9142 10 18.5 10H17.75V11.75Z" fill="#4650F8" opacity="0.2"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 21C16.9706 21 21 16.9706 21 11.9991C21 7.02858 16.9706 3 12 3C7.02944 3 3 7.02858 3 11.9991C3 16.9706 7.02944 21 12 21ZM12 19.5C16.1421 19.5 19.5 16.1421 19.5 11.9991C19.5 7.85786 16.1421 4.5 12 4.5C7.85786 4.5 4.5 7.85786 4.5 11.9991C4.5 16.1421 7.85786 19.5 12 19.5ZM8.25 13.4991C8.25 12.3955 9.14643 11.4991 10.25 11.4991H12.75C13.8546 11.4991 14.75 12.3955 14.75 13.4991C14.75 13.9133 14.4142 14.2491 14 14.2491H10C9.58579 14.2491 9.25 13.9133 9.25 13.4991ZM10.25 10.4991C9.5934 10.4991 9.075 10.9805 9.075 11.5925C9.075 12.2046 9.5934 12.686 10.25 12.686C10.9066 12.686 11.425 12.2046 11.425 11.5925C11.425 10.9805 10.9066 10.4991 10.25 10.4991ZM12.575 11.5925C12.575 10.9805 13.0934 10.4991 13.75 10.4991C14.4066 10.4991 14.925 10.9805 14.925 11.5925C14.925 12.2046 14.4066 12.686 13.75 12.686C13.0934 12.686 12.575 12.2046 12.575 11.5925Z" fill="#4650F8"/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Ver Sitio</span>
            </a>
          </li>

          <!-- Dashboard -->
          <li>
            <a
              href="index.php"
              class="menu-item group"
              :class="page === 'dashboard' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="page === 'dashboard' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>

          <!-- Usuarios -->
          <li>
            <a
              href="usuarios.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'usuarios' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'usuarios' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Usuarios</span>
            </a>
          </li>

          <!-- Autores -->
          <li>
            <a
              href="autores.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'autores' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'autores' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25C8.50391 3.83579 8.83969 3.5 9.25391 3.5H15.2777C15.4766 3.5 15.6674 3.57902 15.8081 3.71967L18.2807 6.19234C18.4214 6.333 18.5004 6.52376 18.5004 6.72268V16.75C18.5004 17.1642 18.1646 17.5 17.7504 17.5H16.248V17.4993H14.748V17.5H9.25391C8.83969 17.5 8.50391 17.1642 8.50391 16.75V4.25ZM14.748 19H9.25391C8.01126 19 7.00391 17.9926 7.00391 16.75V6.49854H6.24805C5.83383 6.49854 5.49805 6.83432 5.49805 7.24854V19.75C5.49805 20.1642 5.83383 20.5 6.24805 20.5H13.998C14.4123 20.5 14.748 20.1642 14.748 19.75L14.748 19ZM7.00391 4.99854V4.25C7.00391 3.00736 8.01127 2 9.25391 2H15.2777C15.8745 2 16.4468 2.23705 16.8687 2.659L19.3414 5.13168C19.7634 5.55364 20.0004 6.12594 20.0004 6.72268V16.75C20.0004 17.9926 18.9931 19 17.7504 19H16.248L16.248 19.75C16.248 20.9926 15.2407 22 13.998 22H6.24805C5.00541 22 3.99805 20.9926 3.99805 19.75V7.24854C3.99805 6.00589 5.00541 4.99854 6.24805 4.99854H7.00391Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Autores</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Grupo: CONTENIDO -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            Contenido
          </span>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Reportajes -->
          <li>
            <a
              href="reportajes.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'reportajes' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'reportajes' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H17C17.4143 13.5357 17.75 13.8715 17.75 14.2857C17.75 14.6999 17.4143 15.0357 17 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Reportajes</span>
            </a>
          </li>

          <!-- Fotos de reportajes -->
          <li>
            <a
              href="reportajes_fotos.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'reportajes_fotos' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'reportajes_fotos' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM4.5 12C4.5 7.85786 7.85786 4.5 12 4.5C16.1421 4.5 19.5 7.85786 19.5 12C19.5 16.1421 16.1421 19.5 12 19.5C7.85786 19.5 4.5 16.1421 4.5 12ZM12 9.75C10.7574 9.75 9.75 10.7574 9.75 12C9.75 13.2426 10.7574 14.25 12 14.25C13.2426 14.25 14.25 13.2426 14.25 12C14.25 10.7574 13.2426 9.75 12 9.75ZM8.25 12C8.25 9.92893 9.92893 8.25 12 8.25C14.0711 8.25 15.75 9.92893 15.75 12C15.75 14.0711 14.0711 15.75 12 15.75C9.92893 15.75 8.25 14.0711 8.25 12Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Fotos de Reportajes</span>
            </a>
          </li>

          <!-- Noticias -->
          <li>
            <a
              href="noticias.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'noticias' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'noticias' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 2.25C6.33579 2.25 6 2.58579 6 3V4H4.5C3.25736 4 2.25 5.00736 2.25 6.25V17.75C2.25 18.9926 3.25736 20 4.5 20H19.5C20.7426 20 21.75 18.9926 21.75 17.75V7.25C21.75 6.00736 20.7426 5 19.5 5H9.06L7.8 2.925C7.6804 2.72522 7.46339 2.25 7 2.25H6.75ZM7.5 5L8.325 6.15C8.37008 6.21694 8.4475 6.25 8.53 6.25H19.5C19.7761 6.25 19.9821 6.25 20 6.25H21.75V17.75C21.75 18.7165 20.9665 19.5 20 19.5H4.5C4.08579 19.5 3.75 19.1642 3.75 18.75V6.25C3.75 5.83579 4.08579 5.5 4.5 5.5H7.5V5ZM6.75 9.25C6.75 8.83579 7.08579 8.5 7.5 8.5H17.5C17.9142 8.5 18.25 8.83579 18.25 9.25V14.25C18.25 14.6642 17.9142 15 17.5 15H7.5C7.08579 15 6.75 14.6642 6.75 14.25V9.25ZM8.25 10V13.5H16.75V10H8.25ZM7.5 16.5C7.08579 16.5 6.75 16.8358 6.75 17.25C6.75 17.6642 7.08579 18 7.5 18H17.5C17.9142 18 18.25 17.6642 18.25 17.25C18.25 16.8358 17.9142 16.5 17.5 16.5H7.5Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Noticias</span>
            </a>
          </li>

          <!-- Boletines -->
          <li>
            <a
              href="boletines.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'boletines' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'boletines' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 2.75C6.5 2.33579 6.83579 2 7.25 2H16.75C17.1642 2 17.5 2.33579 17.5 2.75V9.68934L21.5303 5.65901C21.7985 5.39082 22.2338 5.39082 22.502 5.65901C22.7702 5.9272 22.7702 6.36243 22.502 6.63063L18.5303 10.6023L22.502 14.5739C22.7702 14.8421 22.7702 15.2774 22.502 15.5456C22.2338 15.8138 21.7985 15.8138 21.5303 15.5456L17.5 11.5153V21.25C17.5 21.6642 17.1642 22 16.75 22H7.25C6.83579 22 6.5 21.6642 6.5 21.25V2.75ZM4 7.25C3.58579 7.25 3.25 7.58579 3.25 8V19.5C3.25 20.7426 4.25736 21.75 5.5 21.75H8V7.25H4ZM8 5.75H16V3.5H8V5.75Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Boletines</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Grupo: MEDIA -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            Media
          </span>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Podcasts -->
          <li>
            <a
              href="podcasts.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'podcasts' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'podcasts' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.5 9C8.5 7.067 10.067 5.5 12 5.5C13.933 5.5 15.5 7.067 15.5 9V13C15.5 14.933 13.933 16.5 12 16.5C10.067 16.5 8.5 14.933 8.5 13V9ZM12 4C9.23858 4 7 6.23858 7 9V13C7 15.7614 9.23858 18 12 18C14.7614 18 17 15.7614 17 13V9C17 6.23858 14.7614 4 12 4ZM5.25 9.5C5.66421 9.5 6 9.83579 6 10.25V15.25C6 18.4257 8.57452 21 11.75 21H12.25C15.4255 21 18 18.4257 18 15.25V10.25C18 9.83579 18.3358 9.5 18.75 9.5C19.1642 9.5 19.5 9.83579 19.5 10.25V15.25C19.5 19.2534 16.2534 22.5 12.25 22.5H11.75C7.7466 22.5 4.5 19.2534 4.5 15.25V10.25C4.5 9.83579 4.83579 9.5 5.25 9.5Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Podcasts</span>
            </a>
          </li>

          <!-- Videos -->
          <li>
            <a
              href="videos.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'videos' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'videos' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.5 5C4.08579 5 3.75 5.33579 3.75 5.75V17.25C3.75 17.6642 4.08579 18 4.5 18H14.5C14.9142 18 15.25 17.6642 15.25 17.25V12.6512L20.25 15.75C20.5766 15.9543 21 15.7166 21 15.33V7.67C21 7.28335 20.5766 7.04571 20.25 7.25L15.25 10.3488V5.75C15.25 5.33579 14.9142 5 14.5 5H4.5ZM15.25 12.1452V10.8548L20.25 13.953V9.047L15.25 12.1452ZM13.75 6.5H5.25V16.5H13.75V6.5Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Videos</span>
            </a>
          </li>

          <!-- PDFs -->
          <li>
            <a
              href="pdfs.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'pdfs' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'pdfs' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 2C6.33579 2 6 2.33579 6 2.75V21.25C6 21.6642 6.33579 22 6.75 22H17.25C17.6642 22 18 21.6642 18 21.25V6.72268C18 6.52376 17.921 6.33231 17.7803 6.19173L14.8081 3.21967C14.6674 3.07902 14.4766 3 14.2777 3H6.75ZM8.64 3.75H13.5V7.25C13.5 7.66421 13.8358 8 14.25 8H16.5V20.5H8.64V3.75ZM8.64 10.5H15.36V12H8.64V10.5ZM8.64 14.5H15.36V16H8.64V14.5ZM8.64 18H15.36V19.5H8.64V18Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Documentos PDF</span>
            </a>
          </li>

          <!-- Fotos -->
          <li>
            <a
              href="fotos.php"
              class="menu-item group"
              :class="<?= $p('page') ?> === 'fotos' ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="<?= $p('page') ?> === 'fotos' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
              >
                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 6.75C2.25 5.50736 3.25736 4.5 4.5 4.5H5.83751C6.26913 4.5 6.68439 4.32796 6.99748 4.02045L9.3562 1.71676C9.66755 1.41019 10.0808 1.25 10.5101 1.25H13.4899C13.9192 1.25 14.3325 1.41019 14.6438 1.71676L17.0025 4.02045C17.3156 4.32796 17.7309 4.5 18.1625 4.5H19.5C20.7426 4.5 21.75 5.50736 21.75 6.75V18.25C21.75 19.4926 20.7426 20.5 19.5 20.5H4.5C3.25736 20.5 2.25 19.4926 2.25 18.25V6.75ZM4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V18.25C3.75 18.6642 4.08579 19 4.5 19H19.5C19.9142 19 20.25 18.6642 20.25 18.25V6.75C20.25 6.33579 19.9142 6 19.5 6H18.1625C17.7309 6 17.3156 5.82796 17.0025 5.52045L14.6438 3.21676C14.3325 2.91019 13.9192 2.75 13.4899 2.75H10.5101C10.0808 2.75 9.66755 2.91019 9.3562 3.21676L6.99748 5.52045C6.68439 5.82796 6.26913 6 5.83751 6H4.5ZM12 7.75C9.65279 7.75 7.75 9.65279 7.75 12C7.75 14.3472 9.65279 16.25 12 16.25C14.3472 16.25 16.25 14.3472 16.25 12C16.25 9.65279 14.3472 7.75 12 7.75ZM9.25 12C9.25 10.4812 10.4812 9.25 12 9.25C13.5188 9.25 14.75 10.4812 14.75 12C14.75 13.5188 13.5188 14.75 12 14.75C10.4812 14.75 9.25 13.5188 9.25 12Z" fill="" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Fotos</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</aside>
<!-- ===== Sidebar End ===== -->