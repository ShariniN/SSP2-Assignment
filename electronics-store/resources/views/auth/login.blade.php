@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Login</h2>

    {{-- Normal Login Form --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Login
        </button>
    </form>

    {{-- Divider --}}
    <div class="my-6 flex items-center">
        <hr class="flex-grow border-gray-300">
        <span class="px-3 text-gray-500 text-sm">or</span>
        <hr class="flex-grow border-gray-300">
    </div>

    {{-- Google Login --}}
    <a href="{{ route('login.google') }}"
       class="flex items-center justify-center w-full bg-red-500 text-white py-2 rounded hover:bg-red-600">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 488 512">
            <path d="M488 261.8c0-17.8-1.6-35-4.8-51.6H249v97.8h134.6c-5.8 31-23.6 
                     57.2-50.2 74.8v61h81c47.4-43.6 73.6-107.8 73.6-182z" />
            <path d="M249 492c67.2 0 123.6-22.2 164.8-60.2l-81-61c-22.4 15-51 
                     24-83.8 24-64.4 0-119-43.4-138.6-101.6H27v63.4C67.4 
                     439.8 152.2 492 249 492z" />
            <path d="M110.4 293.2c-4.8-14-7.6-28.8-7.6-44s2.8-30 7.6-44v-63.4H27C9.8 
                     172.8 0 209.6 0 249s9.8 76.2 27 107.2l83.4-63z" />
            <path d="M249 97c35.8 0 67.6 12.4 92.8 36.6l69.6-69.6C372.6 25 316.2 
                     0 249 0 152.2 0 67.4 52.2 27 141.8l83.4 63C130 
                     140.4 184.6 97 249 97z" />
        </svg>
        Login with Google
    </a>
</div>
@endsection
