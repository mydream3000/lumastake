@if($posts->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($posts as $post)
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 shadow-[0_4px_4px_0_rgba(43,166,255,1)] flex flex-col transition-transform hover:scale-[1.02] min-h-[550px]">
                <div class="h-[240px] mb-8 rounded-[24px] overflow-hidden">
                    <img src="{{ $post->image ? asset($post->image) : asset('img/blog/blog-article-main.png') }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <span class="px-4 py-1 bg-[#3B4EFC]/10 text-[#3B4EFC] rounded-full text-sm font-bold uppercase tracking-wider">Article</span>
                    <span class="text-gray-400 text-sm">{{ $post->published_at->format('M d, Y') }}</span>
                </div>

                <h3 class="text-[32px] font-the-bold-font font-black text-[#262262] mb-6 uppercase leading-[0.9] tracking-tighter line-clamp-2 min-h-[58px]">
                    {{ $post->title }}
                </h3>

                <p class="text-[#262262]/70 text-[18px] leading-relaxed mb-8 line-clamp-3">
                    {{ Str::limit(strip_tags($post->content), 150) }}
                </p>

                <a href="{{ route('blog.show', $post->slug) }}" class="mt-auto inline-flex items-center gap-2 text-[#3B4EFC] font-black text-xl uppercase tracking-tighter hover:gap-4 transition-all">
                    Read More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-16 blog-pagination">
        {{ $posts->links() }}
    </div>
@else
    <div class="text-center py-20">
        <h3 class="text-3xl font-the-bold-font text-gray-400 uppercase">No articles found yet.</h3>
    </div>
@endif
