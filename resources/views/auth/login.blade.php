@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-surface-container-low">
    <div class="max-w-md w-full space-y-8 bg-surface p-8 rounded-xl shadow-sm border border-outline-variant">
        <div>
            <div class="mx-auto w-16 h-16 bg-primary rounded-xl flex items-center justify-center text-on-primary shadow-sm mb-6">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">shield</span>
            </div>
            <h2 class="text-center text-3xl font-display font-extrabold text-on-surface tracking-tight">
                Masuk ke Akun Anda
            </h2>
            <p class="mt-2 text-center text-sm text-on-surface-variant font-sans">
                Atau
                <a href="{{ route('register') }}" class="font-bold text-primary hover:text-primary-container transition-colors">
                    daftar akun baru
                </a>
            </p>
        </div>

        @if($errors->any())
            <div class="bg-error-container text-on-error-container p-4 rounded-lg flex flex-col gap-1 shadow-sm border border-error">
                <ul class="list-disc pl-6 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block font-sans font-bold text-sm text-on-surface mb-1">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-outline-variant placeholder-on-surface-variant/50 text-on-surface focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm bg-surface transition-colors"
                           placeholder="nama@email.com">
                </div>
                <div>
                    <label for="password" class="block font-sans font-bold text-sm text-on-surface mb-1">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-outline-variant placeholder-on-surface-variant/50 text-on-surface focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm bg-surface transition-colors"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 text-primary focus:ring-primary border-outline-variant rounded bg-surface">
                    <label for="remember" class="ml-2 block text-sm text-on-surface-variant font-sans">
                        Ingat saya
                    </label>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-sans font-bold rounded-lg text-on-primary bg-primary hover:bg-on-primary-fixed-variant focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 shadow-sm">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <span class="material-symbols-outlined text-on-primary/80 group-hover:text-on-primary">login</span>
                    </span>
                    Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
