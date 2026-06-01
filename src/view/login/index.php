<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - ChipStore</title>
  <?php include_once('../../shared/head.html') ?>
  <link rel="stylesheet" href="style.css">
</head>

<body class="text-[var(--color-text)] min-h-screen flex">

  <!-- Left Panel -->
  <div class="hidden lg:flex flex-col justify-between w-1/2 p-10 lg:px-16 lg:py-14 relative overflow-hidden bg-[var(--main-bg-color)]">
    <!-- Background Glows -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
      <div class="absolute -top-[20%] -left-[10%] w-[40rem] h-[40rem] bg-[var(--main-color)] rounded-full mix-blend-screen filter blur-[150px] opacity-[0.12]"></div>
      <div class="absolute top-[40%] right-[-10%] w-[35rem] h-[35rem] bg-[var(--main-bg-color-composition)] rounded-full mix-blend-screen filter blur-[150px] opacity-[0.08]"></div>
    </div>

    <!-- Header / Logo -->
    <div class="relative z-10 flex items-center gap-2">
      <div class="text-[var(--main-color)] text-xl">
        <img class="w-10" src="../../images/icon.svg" alt="">
      </div>
      <span class="font-bold text-lg tracking-tight mt-1">Chip<strong class="text-[var(--main-color)]">Store</strong></span>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-xl">
      <h1 class="text-4xl lg:text-5xl font-extrabold mb-5 tracking-tight">
        Tecnologia<br>de ponta na<br><span class="text-[var(--main-color)]">velocidade</span><br><span class="text-[var(--main-color)]">de um chip.</span>
      </h1>

      <p class="text-[var(--color-text-muted-400)] text-base mb-8 max-w-md">
        Processadores, placas de vídeo, periféricos e tudo o que seu setup precisa. Bem-vindo à ChipStore.
      </p>

      <ul class="space-y-4">
        <li class="flex items-center gap-4 text-sm text-[var(--color-text-muted-300)] font-medium">
          <div class="w-9 h-9 rounded-lg bg-[var(--mini-card-bg-color)] flex items-center justify-center border border-[var(--mini-card-border-color)]">
            <i class="fa-solid fa-bolt text-[var(--main-color)]"></i>
          </div>
          <span>Entrega expressa em todo o Brasil</span>
        </li>
        <li class="flex items-center gap-4 text-sm text-[var(--color-text-muted-300)] font-medium">
          <div class="w-9 h-9 rounded-lg bg-[var(--mini-card-bg-color)] flex items-center justify-center border border-[var(--mini-card-border-color)]">
            <i class="fa-solid fa-shield-halved text-[var(--main-color)]"></i>
          </div>
          <span>Garantia estendida em todos os produtos</span>
        </li>
        <li class="flex items-center gap-4 text-sm text-[var(--color-text-muted-300)] font-medium">
          <div class="w-9 h-9 rounded-lg bg-[var(--mini-card-bg-color)] flex items-center justify-center border border-[var(--mini-card-border-color)]">
            <i class="fa-solid fa-truck text-[var(--main-color)]"></i>
          </div>
          <span>Frete grátis acima de R$ 500</span>
        </li>
      </ul>
    </div>

    <!-- Footer -->
    <div class="relative z-10 flex items-center gap-2 text-[var(--color-text-muted-500)] text-xs font-medium">
      <i class="fa-solid fa-microchip text-[var(--main-color)]"></i>
      <span>ChipStore &copy; 2026</span>
    </div>
  </div>

  <!-- Right Panel (Login Form) -->
  <div class="w-full lg:w-1/2 flex items-center justify-center py-8 px-4  bg-[var(--second-bg-color)]">
    <div class="w-full max-w-[360px]">
      <div class="flex flex-col relative z-10 lg:hidden items-center justify-center gap-4 mb-8">
        <div class="flex items-center gap-2">
          <div class="text-[var(--main-color)] text-xl">
            <img class="w-13" src="../../images/icon.svg" alt="">
          </div>
          <span class="font-bold text-3xl tracking-tight mt-1">Chip<strong class="text-[var(--main-color)]">Store</strong></span>
        </div>
        <p class="text-center text-[var(--color-text-muted-300)]">Tecnologia de ponta na <strong class="text-[var(--main-color-hover)]">velocidade de um chip.</strong></p>
      </div>
      <div class="mb-8">
        <h2 class="text-[28px] font-bold mb-1.5 tracking-tight"><i class="fa-solid fa-arrow-right-to-bracket mr-2 text-[var(--main-color)]"></i> Entrar na sua conta</h2>
        <p class="text-[var(--color-text-muted-400)] text-sm">
          Acesse o painel administrativo.</p>
      </div>

      <form action="#" method="POST" class="space-y-4">
        <div>
          <label for="email" class="block text-[13px] font-semibold text-[var(--color-text-muted-300)] mb-2">E-mail</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
              <i class="fa-regular fa-envelope text-[var(--color-text-muted-500)] group-focus-within:text-[var(--main-color)]"></i>
            </div>
            <input type="email" id="email" name="email" placeholder="voce@email.com" class="bg-[var(--input-login-bg-color)] border border-[var(--input-login-border-color)] text-[--color-text] text-sm rounded-lg focus:ring-[var(--main-color)] focus:border-[var(--main-color)] block w-full pl-10 p-3 transition-colors outline-none placeholder-[var(--color-text-muted-500)]" required>
          </div>
        </div>

        <div>
          <label for="password" class="block text-[13px] font-semibold text-[var(--color-text-muted-300)] mb-2">Senha</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
              <i class="fa-solid fa-lock text-[var(--color-text-muted-500)] group-focus-within:text-[var(--main-color)]"></i>
            </div>
            <input type="password" id="password" name="password" placeholder="••••••••" class="bg-[var(--input-login-bg-color)] border border-[var(--input-login-border-color)] text-[--color-text] text-sm rounded-lg focus:ring-[var(--main-color)] focus:border-[var(--main-color)] block w-full pl-10 pr-10 p-3 transition-colors outline-none placeholder-[var(--color-text-muted-500)]" required>
            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[var(--color-text-muted-500)] hover:text-[var(--color-text)] transition-colors focus:outline-none">
              <i class="fa-regular fa-eye" id="toggleIcon"></i>
            </button>
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="w-full text-[var(--color-text-button-submit)] bg-[var(--main-color)] hover:bg-[var(--main-color-hover)] font-bold rounded-lg text-sm px-5 py-3 text-center flex items-center justify-center gap-2 transition-all glow-effect">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            Entrar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="./script.js"></script>
</body>

</html>