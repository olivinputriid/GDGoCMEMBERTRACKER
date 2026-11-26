<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | GDGoC UNSRI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gradient-to-br from-[#F8FAFF] to-[#EEF2FF] flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="text-8xl font-black text-[#163CA8] mb-2">404</div>
        <h1 class="text-2xl font-extrabold text-slate-800 mb-3">Page Not Found</h1>
        <p class="text-slate-500 text-sm mb-8">Oops! The page you're looking for doesn't exist or has been moved.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ url('/home') }}" class="px-6 py-3 rounded-full bg-[#163CA8] text-white text-sm font-bold hover:bg-[#123189] transition">Go Home</a>
            <a href="javascript:history.back()" class="px-6 py-3 rounded-full bg-white border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">Go Back</a>
        </div>
    </div>
</body>
</html>
