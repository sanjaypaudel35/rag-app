<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ragbot - Build Intelligent Knowledge Assistants</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-950 antialiased font-sans text-zinc-900 dark:text-zinc-100">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 w-full border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <flux:navbar class="-mb-px">
                <flux:brand href="/" logo="https://fluxui.dev/img/demo/logo.png" name="Ragbot" />

                <flux:spacer />

                <flux:navbar.item href="#features" class="max-sm:hidden">Features</flux:navbar.item>
                <flux:navbar.item href="#how-it-works" class="max-sm:hidden">How it Works</flux:navbar.item>
                
                <div class="flex items-center gap-4 ml-4">
                    @if (Route::has('ragbot.login'))
                        @auth('ragbot')
                            <flux:button href="{{ app()->bound('ragbot.project') ? route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug]) : route('ragbot.platform.dashboard') }}">Dashboard</flux:button>
                        @elseauth('web')
                            <flux:button href="{{ route('ragbot.platform.dashboard') }}">Dashboard</flux:button>
                        @else
                            <flux:navbar.item href="{{ route('ragbot.login') }}">Log in</flux:navbar.item>
                            @if (Route::has('ragbot.register'))
                                <flux:button href="{{ route('ragbot.register') }}">Get Started</flux:button>
                            @endif
                        @endauth
                    @endif
                </div>
            </flux:navbar>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-24 sm:py-32">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-4xl mx-auto">
                    <flux:badge size="sm" class="mb-6">Powered by Advanced RAG Technology</flux:badge>
                    <flux:heading size="xl" level="1" class="text-4xl sm:text-6xl font-extrabold tracking-tight mb-6">
                        Build Intelligent <span class="text-indigo-600 dark:text-indigo-400">Knowledge Assistants</span> in Minutes
                    </flux:heading>
                    <flux:text size="lg" class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-400 mb-10 max-w-2xl mx-auto">
                        Connect your documents, automate your support, and empower your team with Ragbot's powerful RAG engine. The smartest way to talk to your data.
                    </flux:text>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <flux:button href="{{ route('ragbot.register') }}" class="w-full sm:w-auto">Start Building for Free</flux:button>
                        <flux:button href="#features" class="w-full sm:w-auto">Explore Features</flux:button>
                    </div>
                </div>

                <!-- Hero Image/Mockup Placeholder -->
                <div class="mt-16 relative">
                    <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-zinc-950 via-transparent to-transparent z-10"></div>
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 shadow-2xl overflow-hidden aspect-video max-w-5xl mx-auto flex items-center justify-center">
                         <div class="text-zinc-400 dark:text-zinc-600 flex flex-col items-center gap-4">
                             <flux:icon icon="squares-plus" class="size-16" />
                             <flux:text>Interactive Dashboard Preview</flux:text>
                         </div>
                    </div>
                </div>
            </div>
            
            <!-- Background Shapes -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-0 opacity-20 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] size-[500px] bg-indigo-500/30 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] size-[500px] bg-purple-500/30 rounded-full blur-[120px]"></div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <flux:heading size="lg" class="mb-4">Everything you need for RAG</flux:heading>
                    <flux:text size="lg" class="text-zinc-600 dark:text-zinc-400">
                        Powerful tools to help you build, manage, and deploy retrieval-augmented generation assistants.
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-6 text-indigo-600 dark:text-indigo-400">
                            <flux:icon icon="document-text" />
                        </div>
                        <flux:heading class="mb-3">Document Processing</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Upload PDFs, Word docs, or link websites. Ragbot processes and indexes your knowledge automatically with high-fidelity parsing.
                        </flux:text>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6 text-purple-600 dark:text-purple-400">
                            <flux:icon icon="magnifying-glass" />
                        </div>
                        <flux:heading class="mb-3">Smart Retrieval</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Our advanced vector search ensures your AI always finds the most relevant information to provide accurate and grounded answers.
                        </flux:text>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-6 text-emerald-600 dark:text-emerald-400">
                            <flux:icon icon="code-bracket" />
                        </div>
                        <flux:heading class="mb-3">Easy Integration</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Embed your chatbot on any website with a single line of code or use our robust API to build custom experiences.
                        </flux:text>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-6 text-amber-600 dark:text-amber-400">
                            <flux:icon icon="shield-check" />
                        </div>
                        <flux:heading class="mb-3">Secure & Private</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Your data is encrypted and protected. You have full control over who can access your knowledge base and how it's used.
                        </flux:text>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-6 text-rose-600 dark:text-rose-400">
                            <flux:icon icon="chart-bar" />
                        </div>
                        <flux:heading class="mb-3">Analytics & Insights</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Track how users interact with your assistants and gain insights into what they're asking and how your bots are performing.
                        </flux:text>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-8 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="size-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-6 text-cyan-600 dark:text-cyan-400">
                            <flux:icon icon="cpu-chip" />
                        </div>
                        <flux:heading class="mb-3">Custom LLM Support</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            Choose from various LLM providers or use your own models. Ragbot is flexible and works with the best AI models available.
                        </flux:text>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works Section -->
        <section id="how-it-works" class="py-24">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <flux:heading size="lg" class="mb-4">How it works</flux:heading>
                    <flux:text size="lg" class="text-zinc-600 dark:text-zinc-400">
                        Get your assistant up and running in three simple steps.
                    </flux:text>
                </div>

                <div class="flex flex-col lg:flex-row gap-12 items-start">
                    <div class="lg:w-1/2 space-y-12">
                        <!-- Step 1 -->
                        <div class="flex gap-6">
                            <div class="flex-none size-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">1</div>
                            <div>
                                <flux:heading class="mb-2">Upload your knowledge</flux:heading>
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
                                    Simply upload your documents or provide URLs. Ragbot will parse, chunk, and embed your content automatically.
                                </flux:text>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-6">
                            <div class="flex-none size-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">2</div>
                            <div>
                                <flux:heading class="mb-2">Configure your assistant</flux:heading>
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
                                    Customize the look and feel, set system prompts, and define the behavior of your AI assistant to match your brand.
                                </flux:text>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-6">
                            <div class="flex-none size-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">3</div>
                            <div>
                                <flux:heading class="mb-2">Deploy and interact</flux:heading>
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
                                    Embed the widget on your site or use the API. Start getting accurate, grounded answers from your own knowledge base.
                                </flux:text>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/2 bg-zinc-100 dark:bg-zinc-900 rounded-3xl p-12 border border-zinc-200 dark:border-zinc-800 aspect-square flex items-center justify-center overflow-hidden relative group">
                        <!-- Decorative elements -->
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-50 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute top-1/4 left-1/4 size-32 bg-indigo-500/10 blur-3xl rounded-full"></div>
                        <div class="absolute bottom-1/4 right-1/4 size-32 bg-purple-500/10 blur-3xl rounded-full"></div>
                        
                        <!-- Illustration content -->
                        <div class="relative w-full h-full flex items-center justify-center">
                            <div class="p-8 bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 transform group-hover:scale-105 transition-transform duration-500">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-700"></div>
                                    <div class="h-2 w-24 bg-zinc-100 dark:bg-zinc-700 rounded"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-700 rounded"></div>
                                    <div class="h-2 w-5/6 bg-zinc-100 dark:bg-zinc-700 rounded"></div>
                                    <div class="h-2 w-4/6 bg-zinc-100 dark:bg-zinc-700 rounded"></div>
                                </div>
                                <div class="mt-8 flex justify-end">
                                    <div class="px-4 py-2 bg-indigo-600 rounded-lg text-[10px] text-white font-bold">Ragbot is processing...</div>
                                </div>
                            </div>
                            
                            <!-- Floating icons -->
                            <div class="absolute top-10 right-10 p-4 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 animate-bounce" style="animation-duration: 3s;">
                                <flux:icon icon="document-text" variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div class="absolute bottom-10 left-10 p-4 bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 animate-bounce" style="animation-duration: 4s;">
                                <flux:icon icon="chat-bubble-left-right" variant="mini" class="text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-indigo-600 dark:bg-indigo-700">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <flux:heading size="xl" level="2" class="text-white mb-6">
                    Ready to build your AI assistant?
                </flux:heading>
                <flux:text size="lg" class="text-indigo-100 mb-10 max-w-2xl mx-auto">
                    Join hundreds of companies using Ragbot to transform their documents into intelligent, actionable knowledge.
                </flux:text>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <flux:button href="{{ route('ragbot.register') }}" class="bg-white text-indigo-600 hover:bg-zinc-100 w-full sm:w-auto">Get Started for Free</flux:button>
                    <flux:button href="{{ route('ragbot.login') }}" class="text-white hover:bg-white/10 w-full sm:w-auto">Already have an account?</flux:button>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-12 border-t border-zinc-200 dark:border-zinc-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <img src="https://fluxui.dev/img/demo/logo.png" alt="Ragbot" class="size-6">
                    <span class="font-bold text-lg">Ragbot</span>
                </div>
                
                <nav class="flex gap-8 text-sm text-zinc-600 dark:text-zinc-400">
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Privacy Policy</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Terms of Service</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Contact Us</a>
                </nav>

                <div class="text-sm text-zinc-500">
                    &copy; {{ date('Y') }} Ragbot. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
