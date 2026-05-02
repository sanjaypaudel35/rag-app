@extends("ragbot::layouts.auth")

@section("content")
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    @if (session("status"))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session("status") }}
        </div>
    @endif

    <form method="POST" action="{{ route("password.email", ["project_slug" => request()->route("project_slug")]) }}">
        @csrf

        <div>
            <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="email" name="email" value="{{ old("email") }}" required autofocus autocomplete="username" />
            @error("email")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Email Password Reset Link
            </button>
        </div>
    </form>
@endsection
