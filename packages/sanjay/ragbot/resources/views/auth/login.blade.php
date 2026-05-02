@extends("ragbot::layouts.auth")

@section("content")
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center font-semibold">
        Log in to your project account
    </div>

    @if (session("status"))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session("status") }}
        </div>
    @endif

    <form method="POST" action="{{ route("ragbot.login", ["project_slug" => request()->route("project_slug")]) }}">
        @csrf

        <div>
            <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="email" name="email" value="{{ old("email") }}" required autofocus autocomplete="username" />
            @error("email")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
            <input id="password" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="password" name="password" required autocomplete="current-password" />
            @error("password")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has("ragbot.password.request"))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800" href="{{ route("ragbot.password.request", ["project_slug" => request()->route("project_slug")]) }}">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Log in
            </button>
        </div>

        <div class="mt-4 text-center">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route("ragbot.register", ["project_slug" => request()->route("project_slug")]) }}">
                Need an account? Register
            </a>
        </div>
    </form>
@endsection
