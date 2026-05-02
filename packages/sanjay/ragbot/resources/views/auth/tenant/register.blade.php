@extends("ragbot::layouts.auth")

@section("content")
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center font-semibold">
        Register for {{ app("ragbot.project")->name }}
    </div>

    <form method="POST" action="{{ route("ragbot.tenant.register.store", ["project_slug" => request()->route("project_slug")]) }}">
        @csrf

        <div>
            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label>
            <input id="name" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="text" name="name" value="{{ old("name") }}" required autofocus autocomplete="name" />
            @error("name")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="email" name="email" value="{{ old("email") }}" required autocomplete="username" />
            @error("email")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
            <input id="password" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="password" name="password" required autocomplete="new-password" />
            @error("password")
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800" href="{{ route("ragbot.tenant.login", ["project_slug" => request()->route("project_slug")]) }}">
                Already registered?
            </a>

            <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Register
            </button>
        </div>
    </form>
@endsection
