<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Menara PeFI</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen w-screen flex">
  
  <!-- Kiri: Gambar -->
  <div class="w-1/2 bg-white flex flex-col items-center justify-center p-8">
    <img src="./img/logo-1.png" alt="Logo Menara PeFI" class="w-2/3">
  </div>

  <!-- Kanan: Form Login -->
  <div class="w-1/2 bg-[#8BC34A] flex flex-col items-center justify-center p-8">
    
    <div class="w-full max-w-md">
      <h2 class="text-3xl font-bold text-center mb-8">Login</h2>

      <!-- Switch Login Pilihan Username/Email -->
      <form action="#" method="POST" class="space-y-5">
        
        <div>
          <label class="block text-white font-semibold mb-1">Username</label>
          <input type="text" placeholder="Masukkan NIK" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-600">
        </div>

        <div>
          <label class="block text-white font-semibold mb-1">Password</label>
          <input type="password" placeholder="Masukkan Password" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-600">
        </div>

        <div class="flex justify-end text-sm">
          <a href="#" class="text-white hover:underline">Forgot password?</a>
        </div>

        <div class="flex justify-center">
          <button type="submit" class="w-1/2 py-2 bg-green-800 hover:bg-green-900 text-white text-lg font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-green-600">
            Login
          </button>
        </div>

      </form>

      <!-- Separator -->
      <div class="flex items-center my-6">
        <hr class="flex-grow border-white">
        <span class="text-white px-4 font-semibold text-sm">Login with Others</span>
        <hr class="flex-grow border-white">
      </div>

      <!-- Button Google Login -->
      <div class="flex justify-center">
        <button class="w-full flex items-center justify-center gap-3 py-2 border border-white rounded-xl text-white hover:bg-green-700 transition">
          <img src="https://cdn-icons-png.flaticon.com/512/2702/2702602.png" alt="Google" class="w-6 h-6">
          <span class="text-sm font-semibold">Login with <span class="font-bold">Google</span></span>
        </button>
      </div>

    </div>
    
  </div>

</body>
</html>
