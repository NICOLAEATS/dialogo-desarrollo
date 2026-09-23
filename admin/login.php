<?php
require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Ingresa tu usuario y contrasena.';
    } else {
        $nivel = strtolower(trim($email));
        $stmt = $pdo->prepare(
            'SELECT * FROM usuarios WHERE LOWER(email) = ? OR LOWER(SUBSTRING_INDEX(email, "@", 1)) = ? LIMIT 1'
        );
        $stmt->execute([$nivel, $nivel]);
        $user = $stmt->fetch();

        if ($user && hash_equals($user['password_hash'], hash('sha256', $password))) {
            if ((int) $user['activo'] !== 1) {
                $error = 'Tu cuenta esta desactivada. Contacta al administrador.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['user_id'];
                $_SESSION['nombre_completo'] = $user['nombre_completo'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['avatar_url'] = $user['avatar_url'] ?: 'assets/images/user/user-01.jpg';
                redirect('index.php');
            }
        } else {
            $error = 'Credenciales incorrectas.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Iniciar sesion | <?= e(APP_NAME) ?></title>
    <link rel="icon" href="assets/favicon.ico" />
    <link href="assets/css/style.css" rel="stylesheet" />
  </head>
  <body
    x-data="{ 'loaded': true, 'darkMode': false, showPassword: false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
  >
    <!-- ===== Preloader Start ===== -->
    <div
      x-show="loaded"
      x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
      class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black"
    >
      <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
    </div>
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
      <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
        <!-- Form -->
        <div class="flex flex-col flex-1 w-full lg:w-1/2">
          <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
            <div>
              <div class="mb-5 sm:mb-8">
                <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                  Iniciar Sesion
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Panel de administracion de <?= e(APP_NAME) ?>.
                  Gestiona usuarios, autores, reportajes, noticias, boletines, podcasts, videos, PDFs y fotos.
                </p>
              </div>

              <?php if ($error) : ?>
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-error-200 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/10">
                  <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-error-500 text-white">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M18.364 5.636L5.636 18.364M5.636 5.636L18.364 18.364" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </span>
                  <p class="text-theme-sm font-medium text-error-700 dark:text-error-400"><?= e($error) ?></p>
                </div>
              <?php endif; ?>

              <form method="post" action="login.php">
                <div class="space-y-5">
                  <!-- Email -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Usuario<span class="text-error-500">*</span>
                    </label>
                    <input
                      type="text"
                      name="email"
                      value="<?= e($email) ?>"
                      placeholder="admin"
                      required
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>
                  <!-- Password -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Contrasena<span class="text-error-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        placeholder="admin"
                        required
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      />
                      <span
                        @click="showPassword = !showPassword"
                        class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400"
                      >
                        <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3" />
                        </svg>
                        <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z" fill="#98A2B3" />
                        </svg>
                      </span>
                    </div>
                  </div>
                  <!-- Button -->
                  <div>
                    <button
                      type="submit"
                      class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"
                    >
                      Ingresar al Panel
                    </button>
                  </div>
                </div>
              </form>

              <div class="mt-6 rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                <p class="text-theme-xs text-gray-500 dark:text-gray-400">
                  Usuario demo: <b>admin</b> — contrasena: <b>admin</b>
                  (definida en el script de la base de datos).
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="relative items-center hidden w-full h-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/2">
          <div class="flex items-center justify-center z-1">
            <div class="absolute right-0 top-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]">
              <img src="assets/images/shape/grid-01.svg" alt="grid" />
            </div>
            <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]">
              <img src="assets/images/shape/grid-01.svg" alt="grid" />
            </div>
            <div class="flex flex-col items-center max-w-sm text-center">
              <a href="index.php" class="block mb-4">
                <img src="assets/images/logo/logo.png" alt="Diálogo y Desarrollo" style="height:80px;width:80px;object-fit:contain" class="rounded-2xl" />
              </a>
              <p class="text-gray-400 dark:text-white/60">
                Base de datos <b>dialogoydesarrollo</b>: reportajes, noticias, boletines, podcasts, videos, PDFs y fotos.
              </p>
            </div>
          </div>
        </div>

        <!-- Toggler -->
        <div class="fixed z-50 hidden bottom-6 right-6 sm:block">
          <button
            class="inline-flex items-center justify-center text-white transition-colors rounded-full size-14 bg-brand-500 hover:bg-brand-600"
            @click.prevent="darkMode = !darkMode"
            aria-label="Modo oscuro"
          >
            <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035Z" fill="currentColor" />
            </svg>
            <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7725 13.1131C12.857 13.1176 11.1472 12.215 10.0982 10.7494C9.16342 9.43403 8.7777 7.78054 9.18107 6.13008C9.11337 6.14683 9.04762 6.16964 8.98411 6.1983C6.50525 7.34067 5.31579 10.2412 6.42834 12.7137C7.36776 14.8153 9.86183 16.4585 12.6953 16.6515L12.697 16.6514C13.6346 16.7313 14.5379 16.6699 15.3678 16.4748C14.8131 16.7311 14.1904 16.9675 13.5444 17.1069C12.8784 17.251 12.1879 17.2947 11.5083 17.2328C8.72553 16.9729 6.56595 13.6787 6.59334 10.7261C6.53549 12.0971 7.94399 13.9273 10.4881 14.9362C10.7366 15.0237 11.252 14.4282 10.9876 14.1584L7.88269 9.82331L7.51881 10.0336C9.31535 14.5894 12.1268 15.2582 13.9742 15.1958C15.9699 15.128 18.6753 12.1367 18.9639 7.3345C19.0273 6.32127 16.4237 6.74674 14.771 10.2544C14.5357 10.6095 14.508 11.1141 14.5613 11.4869L9.48307 9.58696L9.51463 9.64163L14.7725 13.1131Z" fill="currentColor" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->

  <script defer src="assets/js/bundle.js"></script>
  </body>
</html>