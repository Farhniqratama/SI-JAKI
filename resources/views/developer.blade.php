<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI-JAKI &mdash; Profil Pengembang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-container {
            perspective: 1000px;
        }
        .card {
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
        .card-container:hover .card {
            transform: rotateY(180deg);
        }
        .card-front, .card-back {
            backface-visibility: hidden;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .card-back {
            transform: rotateY(180deg);
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('dashboard') }}" class="inline-block mb-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>

        <div class="card-container w-full max-w-sm mx-auto h-[600px]">
            <div class="card relative w-full h-full">
                <!-- Sisi Depan (Foto) -->
                <div class="card-front absolute w-full h-full">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden h-full">
                        <img src={{ asset('image/dzaky.jpg') }} 
                             alt="Foto Profil" 
                             class="w-full h-full object-cover object-center">
                        <div class="absolute rounded-b-lg bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-4">
                            <h2 class="text-2xl font-bold">Ahmad Dzaky Santino</h2>
                            <p class="text-sm">Universitas Gunadarma</p>
                        </div>
                    </div>
                </div>

                <!-- Sisi Belakang (Detail) -->
                <div class="card-back absolute w-full h-full rounded-lg bg-gradient-to-br from-blue-600 to-purple-600 text-white p-6">
                    <div class="h-full overflow-y-auto">
                        <h2 class="text-3xl font-bold mb-4 border-b pb-2">Tentang Saya</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-xl mb-2">Kontak</h3>
                                <div class="space-y-2">
                                    <p class="flex items-center">
                                        <i class="fas fa-envelope mr-3"></i>
                                        dzakysan2002@gmail.com
                                    </p>
                                    {{-- <p class="flex items-center">
                                        <i class="fas fa-phone mr-3"></i>
                                        +62 812-3456-7890
                                    </p>
                                    <p class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-3"></i>
                                        Jakarta, Indonesia
                                    </p> --}}
                                </div>
                            </div>

                            {{-- <div>
                                <h3 class="font-semibold text-xl mb-2">Keahlian</h3>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $skills = [
                                            'Laravel', 'PHP', 'Vue.js', 'React', 
                                            'Node.js', 'MySQL', 'Docker', 'Kubernetes'
                                        ];
                                    @endphp
                                    @foreach($skills as $skill)
                                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <h3 class="font-semibold text-xl mb-2">Pengalaman</h3>
                                <div class="space-y-2">
                                    <div>
                                        <p class="font-medium">Senior Full Stack Developer</p>
                                        <p class="text-sm">PT Inovasi Digital Indonesia</p>
                                        <p class="text-xs">Jan 2021 - Sekarang</p>
                                    </div>
                                    <div>
                                        <p class="font-medium">Full Stack Developer</p>
                                        <p class="text-sm">Startup Tech Jakarta</p>
                                        <p class="text-xs">Jun 2018 - Des 2020</p>
                                    </div>
                                </div>
                            </div> --}}

                            <div>
                                <h3 class="font-semibold text-xl mb-2">Sosial Media</h3>
                                <div class="flex space-x-4">
                                    <a href="https://www.linkedin.com/in/dzakysantino/" target="_blank" class="text-white hover:text-gray-200">
                                        <i class="fab fa-linkedin text-2xl"></i>
                                    </a>
                                    {{-- <a href="#" class="text-white hover:text-gray-200">
                                        <i class="fab fa-github text-2xl"></i>
                                    </a>
                                    <a href="#" class="text-white hover:text-gray-200">
                                        <i class="fab fa-twitter text-2xl"></i>
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- <div class="absolute bottom-4 left-0 right-0 text-center text-sm">
                        <p>Arahkan kursor untuk melihat detail</p>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</body>
</html>