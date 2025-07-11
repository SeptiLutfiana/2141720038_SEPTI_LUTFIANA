<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menara PeFI</title>
    <link rel="stylesheet" href="./style/css/style.css">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body class="font-sans">

    <header x-data="{ isOpen: false }" class="bg-white shadow">
        <div class="container mx-auto px-2 py-2 flex items-center justify-between">
            <div class="logo">
                <img src="./style/img/logo.png" alt="Menara PeFI" class="h-10">
            </div>

            <nav class="hidden md:block">
                <ul class="flex space-x-8 items-center">
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Home</a></li>
                    <li><a href="#about" class="text-gray-700 hover:text-gray-900">About us</a></li>
                    <li><a href="#fitur" class="text-gray-700 hover:text-gray-900">Fitur</a></li>
                    <li><a href="#benefit" class="text-gray-700 hover:text-gray-900">Benefit</a></li>
                    <li><a href="#contact" class="text-gray-700 hover:text-gray-900">Contact us</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">FAQs</a></li>
                    <li>
                        <a href="{{ route('login') }}"
                            class="bg-green-700 text-white text-xs font-semibold rounded-md px-4 py-2 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Login
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="md:hidden">
                <button @click="isOpen = !isOpen" type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md bg-green-800 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-white">

                    <svg :class="{ 'hidden': isOpen, 'block': !isOpen }" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>

                    <svg :class="{ 'block': isOpen, 'hidden': !isOpen }" class="h-6 w-6 hidden" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </button>
            </div>

        </div>

        <div x-show="isOpen" x-transition class="md:hidden absolute top-full left-0 w-full bg-white shadow-md z-50"
            id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#home" @click="isOpen = false"
                    class="text-gray-900 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Home</a>
                <a href="#about" @click="isOpen = false"
                    class="text-gray-900 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">About
                    Us</a>
                <a href="#fitur" @click="isOpen = false"
                    class="text-gray-900 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Fitur</a>
                <a href="#benefit" @click="isOpen = false"
                    class="text-gray-900 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Benefit</a>
                <a href="#contact" @click="isOpen = false"
                    class="text-gray-900 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Contact
                    Us</a>
                <div class="mt-4"> <a href="{{ route('login') }}" 
                        class="items-center bg-gray-500 text-white block rounded-md px-3 py-2 text-base font-medium hover:bg-green-800">
                        <i class="fas fa-user mr-2"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </header>
    <section class="py-18 bg-gray-100">
        <div class="container mx-auto px-4 text-left">
            <div class="flex flex-col-reverse md:flex-row items-center md:justify-between max-w-4xl mx-auto">
                <div class="hero-text w-full md:w-1/2 pr-0 md:pr-16 mb-4 md:mb-0 text-center md:text-left">
                    <h1 class="text-4xl font-bold text-gray-800 mb-4">
                        <span style="color: #83B92C;">Langkah Terukur</span>,<br>
                        <span style="color: #086044;">Karir Terarah</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">MENARA PeFI membantu Anda mengelola pengembangan karir dengan
                        mudah, memantau kompetensi, dan meraih prestasi secara terukur</p>
                    <a href="{{ route('login') }}"
                        class="text-white font-bold rounded-md px-8 py-3 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                        style="background-color: #086044;">Login</a>
                </div>
                <div class="hero-image w-full md:w-1/2">
                    <img src="./style/img/logo-onbording1.png" alt="Karakter Menara PeFI"
                        class="w-3/4 mx-auto md:ml-auto md:mr-0">
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="py-18" style="color: #D9D9D9">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-semibold mb-6" style="color: #086044;">About Us</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Selamat datang di platform MENARA PeFI (Monitoring &
                Navigation Application for Resource Advancement) – solusi digital terdepan untuk mendukung pengembangan
                kompetensi dan karir di Perhutani Forestry Institute.</p>
        </div>
    </section>

    <section id="fitur" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-semibold mb-8 text-center" style="color: #086044;">Fitur Utama Platform</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <img src="./style/img/kompetensi.png" alt="Monitoring Kompetensi"
                        class="h-16 w-auto mx-auto mb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Monitoring Kompetensi</h3>
                    <p class="text-gray-600">Tracking capaian kompetensi karyawan.</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <img src="./style/img/IPD.png" alt="Target IDP" class="h-16 w-auto mx-auto mb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Target IDP</h3>
                    <p class="text-gray-600">Susun rencana pengembangan pribadi dengan target bertahap.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="benefit" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-semibold mb-8 text-center" style="color: #086044;">Benefit</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-100 rounded-lg p-6 text-center">
                    <img src="./style/img/karir.png" alt="Perencanaan Karir" class="h-16 w-auto mx-auto mb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Perencanaan Karir yang Lebih Jelas</h3>
                    <p class="text-gray-600">Dengan fitur IDP, pengguna dapat membuat rencana karir yang terstruktur
                        dan mendapatkan gambaran jelas mengenai langkah-langkah yang perlu diambil untuk mencapai tujuan
                        mereka.</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-6 text-center">
                    <img src="./style/img/kompetensi2.png" alt="Peningkatan Kompetensi"
                        class="h-16 w-auto mx-auto mb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Peningkatan Kompetensi dan Keterampilan</h3>
                    <p class="text-gray-600">Tracking capaian kompetensi dan keterampilan karyawan.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-16 bg-gray-100 text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-semibold mb-8" style="color: #086044;">Contact Us</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                Kami siap membantu Anda dengan segala pertanyaan atau kebutuhan terkait MENARA PeFI. <br>Jangan ragu
                untuk menghubungi kami melalui saluran berikut:
            </p>
            <p class="text-lg font-semibold text-gray-700 mb-4">Nikmatul Khoyri Anwar - Humas SDM PeFI</p>
            <p class="text-gray-600 mb-2">
                <strong>Telepon:</strong>
                <a href="tel:+6283114135317" class="underline hover:text-green-500">083114135317</a>
            </p>
            <p class="text-gray-600 mb-2">
                <strong>WhatsApp:</strong>
                <a href="https://wa.me/6283114135317" target="_blank" class="underline hover:text-green-500">Chat di
                    WhatsApp</a>
            </p>
        </div>
    </section>

    <footer class="bg-gray-500 text-gray-300 py-12">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="mb-6 md:mb-0 text-center md:text-left">
                <div class="logo-footer">
                    <img src="./style/img/logo.png" alt="Menara PeFI" class="h-8 mb-2 inline">
                </div>
                <p class="text-sm">Copyright © 2024 menarapeFI. <br> All rights reserved.</p>
                <div class="flex justify-center md:justify-start mt-4 space-x-4">
                    <a href="https://www.instagram.com/perhutani_pefi/" class="hover:text-gray-900">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="https://www.facebook.com/phtforestryinstitute/" class="hover:text-gray-900">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="https://phtforestryinstitute.id/" class="hover:text-gray-900">
                        <i class="fab fa-dribbble text-xl"></i>
                    </a>
                    <a href="https://www.youtube.com/@perhutaniforestryinstitute4448" class="hover:text-gray-900">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
            </div>

            <div class="mb-6 md:mb-0 text-center md:text-left">
                <div class="footer-links">
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="list-none">
                        <li><a href="#about" class="hover:text-gray-500">About us</a></li>
                        <li><a href="https://phtforestryinstitute.id/" class="hover:text-gray-900">Blog</a></li>
                        <li><a href="#contact" class="hover:text-gray-900">Contact us</a></li>
                        <li><a href="#fitur" class="hover:text-gray-900">Fitur</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center md:text-left">
                <div class="subscribe">
                    <h4 class="text-white font-semibold mb-4">Stay Up To Date</h4>
                    <div class="mt-2 relative">
                        <input type="email" placeholder="Your Comment"
                            class="bg-gray-600 text-white rounded-md py-2 px-4 w-full focus:outline-none focus:ring-2 focus:ring-green-500 pr-10">
                        <button type="submit"
                            class="absolute top-0 right-0 mt-9 mr-0 bg-transparent text-white hover:text-green-300 focus:outline-none">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
