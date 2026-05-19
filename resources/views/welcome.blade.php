<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ragbot — Intelligent AI assistant</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    <style>
        [x-cloak] { display: none !important; }
        
        .glass-card {
            background: rgba(24, 24, 27, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(63, 63, 70, 0.3);
        }

        .hero-gradient {
            background: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .image-glow {
            box-shadow: 0 0 40px -10px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-950 antialiased font-sans text-zinc-100 overflow-x-hidden selection:bg-indigo-500/30">
    <!-- Background Decor -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[800px] hero-gradient"></div>
        <div class="absolute top-[10%] right-[10%] size-[400px] bg-purple-900/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[10%] left-[5%] size-[500px] bg-indigo-900/10 blur-[150px] rounded-full"></div>
    </div>

    <!-- Navigation -->
    <header class="sticky top-0 z-50 w-full border-b border-zinc-800/50 bg-zinc-950/60 backdrop-blur-xl">
        <div class="container mx-auto px-6 lg:px-12">
            <nav class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="size-9 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <flux:icon icon="bolt" variant="mini" class="text-white h-5 w-5" />
                    </div>
                    <span class="font-bold text-xl tracking-tight uppercase italic">Ragbot</span>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
                    <a href="#product" class="hover:text-white transition-colors">Product</a>
                    <a href="#features" class="hover:text-white transition-colors">Features</a>
                    <a href="#showcase" class="hover:text-white transition-colors">Showcase</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth('ragbot')
                        <flux:button variant="primary" href="{{ url(config('ragbot.prefix', 'ragbot') . '/tenant/' . (app()->bound('ragbot.project') ? app('ragbot.project')->slug : 'test-project') . '/dashboard') }}">Go to Dashboard</flux:button>
                    @else
                        <flux:button variant="ghost" href="{{ route('ragbot.tenant.register', ['project_slug' => 'test-project']) }}">Log in</flux:button>
                        <flux:button variant="primary" href="{{ route('ragbot.tenant.register', ['project_slug' => 'test-project']) }}">Get Started</flux:button>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main class="relative z-10">
        <!-- Hero Section -->
        <section id="product" class="pt-24 pb-20 lg:pt-32 lg:pb-32">
            <div class="container mx-auto px-6 lg:px-12 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-800 mb-8 animate-fade-in">
                    <flux:badge size="sm" variant="success" class="bg-emerald-500/10 text-emerald-400 border-emerald-500/20">v2.0 is live</flux:badge>
                    <span class="text-xs font-medium text-zinc-400 px-2">Production-ready RAG for enterprise</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 max-w-4xl mx-auto leading-[1.1]">
                    The Intelligent <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">AI Assistant</span> for your Business Data
                </h1>
                
                <p class="text-lg md:text-xl text-zinc-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                    Transform your messy documents into a semantic knowledge base. Build, test, and deploy context-aware chatbots in minutes.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-24">
                    <flux:button variant="primary" href="{{ route('ragbot.tenant.register', ['project_slug' => 'test-project']) }}" class="w-full sm:w-auto h-14 px-8 text-base">Start Building for Free</flux:button>
                    <flux:button variant="subtle" href="#showcase" class="w-full sm:w-auto h-14 px-8 text-base border-zinc-800 bg-zinc-900/50">View Showcase</flux:button>
                </div>

                <!-- Showcase Carousel -->
                <div id="showcase" x-data="{ 
                    active: 0,
                    images: [
                        '/storage/systemimages/demo1.png',
                        '/storage/systemimages/demo2.png',
                        '/storage/systemimages/demo3.png',
                        '/storage/systemimages/demo4.png',
                        '/storage/systemimages/demo5.png',
                        '/storage/systemimages/demo6.png',
                        '/storage/systemimages/demo7.png',
                        '/storage/systemimages/demo8.png',
                        '/storage/systemimages/demo9.png'
                    ],
                    next() { this.active = (this.active + 1) % this.images.length },
                    prev() { this.active = (this.active - 1 + this.images.length) % this.images.length },
                    init() { setInterval(() => this.next(), 6000) }
                }" class="relative max-w-6xl mx-auto group">
                    
                    <div class="relative aspect-[16/10] lg:aspect-[21/9] rounded-3xl overflow-hidden glass-card p-2 image-glow">
                        <div class="w-full h-full rounded-2xl overflow-hidden relative">
                            <template x-for="(img, index) in images" :key="index">
                                <div x-show="active === index" 
                                     x-transition:enter="transition ease-out duration-700"
                                     x-transition:enter-start="opacity-0 scale-105"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-700"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0">
                                    <img :src="img" class="w-full h-full object-cover" alt="Platform Demo">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 size-12 rounded-full glass-card flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all hover:bg-white/10">
                        <flux:icon icon="chevron-left" variant="mini" />
                    </button>
                    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 size-12 rounded-full glass-card flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all hover:bg-white/10">
                        <flux:icon icon="chevron-right" variant="mini" />
                    </button>

                    <!-- Indicators -->
                    <div class="mt-8 flex justify-center gap-2">
                        <template x-for="(img, index) in images" :key="index">
                            <button @click="active = index" 
                                    :class="active === index ? 'w-8 bg-indigo-500' : 'w-2 bg-zinc-800'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Bento Grid -->
        <section id="features" class="py-24 bg-zinc-950">
            <div class="container mx-auto px-6 lg:px-12">
                <div class="flex flex-col md:flex-row items-end justify-between gap-8 mb-16">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl md:text-4xl font-bold mb-4 italic uppercase tracking-wider">Engineered for quality.</h2>
                        <p class="text-zinc-400 text-lg">We've built the most robust RAG pipeline so you don't have to worry about the math.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 glass-card p-10 rounded-3xl group hover:border-indigo-500/50 transition-colors">
                        <div class="size-12 bg-indigo-600/20 rounded-xl flex items-center justify-center mb-8 border border-indigo-500/20">
                            <flux:icon icon="document-text" class="text-indigo-400" />
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Deep Document Parsing</h3>
                        <p class="text-zinc-400 leading-relaxed mb-6">Our system handles PDF, DOCX, and TXT with advanced layout detection. We chunk your data logically to preserve context and maximize LLM accuracy.</p>
                        <div class="flex gap-2">
                            <flux:badge variant="neutral" class="bg-zinc-800 text-zinc-300 border-zinc-700">OCR Support</flux:badge>
                            <flux:badge variant="neutral" class="bg-zinc-800 text-zinc-300 border-zinc-700">Metadata Ingestion</flux:badge>
                        </div>
                    </div>

                    <div class="glass-card p-10 rounded-3xl group hover:border-purple-500/50 transition-colors">
                        <div class="size-12 bg-purple-600/20 rounded-xl flex items-center justify-center mb-8 border border-purple-500/20">
                            <flux:icon icon="magnifying-glass" class="text-purple-400" />
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Semantic Search</h3>
                        <p class="text-zinc-400 leading-relaxed">Stop relying on keywords. Our vector-based retrieval finds the underlying meaning in user queries.</p>
                    </div>

                    <div class="glass-card p-10 rounded-3xl group hover:border-emerald-500/50 transition-colors">
                        <div class="size-12 bg-emerald-600/20 rounded-xl flex items-center justify-center mb-8 border border-emerald-500/20">
                            <flux:icon icon="code-bracket" class="text-emerald-400" />
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Universal API</h3>
                        <p class="text-zinc-400 leading-relaxed">Integrate with any stack. Our REST API and 1-line script widget make deployment a breeze.</p>
                    </div>

                    <div class="md:col-span-2 glass-card p-10 rounded-3xl group hover:border-amber-500/50 transition-colors flex flex-col md:flex-row gap-10 items-center">
                        <div class="flex-1">
                            <div class="size-12 bg-amber-600/20 rounded-xl flex items-center justify-center mb-8 border border-amber-500/20">
                                <flux:icon icon="chart-bar" class="text-amber-400" />
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Billing & Token Insights</h3>
                            <p class="text-zinc-400 leading-relaxed">Monitor your LLM costs and token usage in real-time. Breakdown by model and chatbot to optimize your spend.</p>
                        </div>
                        <div class="flex-1 w-full p-6 bg-zinc-900/50 rounded-2xl border border-zinc-800">
                             <div class="space-y-4">
                                 <div class="h-2 w-3/4 bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-amber-500 w-2/3"></div></div>
                                 <div class="h-2 w-1/2 bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-indigo-500 w-1/2"></div></div>
                                 <div class="h-2 w-2/3 bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-purple-500 w-1/3"></div></div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-32 relative overflow-hidden">
            <div class="absolute inset-0 bg-indigo-600"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            
            <div class="container mx-auto px-6 lg:px-12 relative z-10 text-center">
                <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-8">
                    Ready to build your knowledge engine?
                </h2>
                <p class="text-indigo-100 text-lg mb-12 max-w-2xl mx-auto">
                    Join forward-thinking developers and businesses transforming their private data into intelligent assistants.
                </p>
                <flux:button href="{{ route('ragbot.tenant.register', ['project_slug' => 'test-project']) }}" class="bg-white text-indigo-600 hover:bg-zinc-100 h-16 px-12 text-lg shadow-2xl shadow-black/20">Get Started for Free</flux:button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-20 border-t border-zinc-800 bg-zinc-950 relative z-10">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-12">
                <div class="flex flex-col items-center md:items-start gap-4">
                    <div class="flex items-center gap-2">
                        <div class="size-6 bg-indigo-600 rounded flex items-center justify-center">
                            <flux:icon icon="bolt" variant="mini" class="text-white h-3 w-3" />
                        </div>
                        <span class="font-bold text-lg italic uppercase tracking-tighter">Ragbot</span>
                    </div>
                    <p class="text-zinc-500 text-sm max-w-xs text-center md:text-left">Building the future of Retrieval-Augmented Generation for everyone.</p>
                </div>
                
                <nav class="flex flex-wrap justify-center gap-x-12 gap-y-4 text-sm font-medium text-zinc-400">
                    <a href="#" class="hover:text-white transition-colors">Documentation</a>
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Contact</a>
                </nav>

                <div class="text-zinc-500 text-sm">
                    &copy; {{ date('Y') }} Ragbot. Developed by Sanjay.
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
