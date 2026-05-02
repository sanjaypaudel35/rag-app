@extends("ragbot::layouts.auth")

@section("content")
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center font-semibold">
        Welcome to platform dashboard
    </div>

    <div class="flex items-center justify-center mt-4">
        <form method="POST" action="{{ route('ragbot.logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Logout
            </button>
        </form>
    </div>
@endsection
