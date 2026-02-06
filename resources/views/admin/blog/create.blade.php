@extends('layouts.admin')

@section('title', 'Create Blog Post')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Create New Blog Post</h1>
            <p class="text-gray-600 mt-1">Add a new article to your blog</p>
        </div>
        <a href="{{ route('admin.blog.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                       placeholder="Enter post title">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug
                </label>
                <input type="text"
                       id="slug"
                       name="slug"
                       value="{{ old('slug') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                       placeholder="auto-generated-from-title">
                <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from title</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content"
                          name="content"
                          rows="12"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                          placeholder="Enter post content">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Image
                </label>
                <input type="file"
                       id="image"
                       name="image"
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                <p class="mt-1 text-sm text-gray-500">Max size: 5MB. Formats: JPEG, PNG, GIF, WebP</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color Scheme -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Color Scheme
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center gap-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="color_scheme" value="#00ffa3" {{ old('color_scheme', '#00ffa3') == '#00ffa3' ? 'checked' : '' }} class="w-5 h-5">
                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #00ffa3;"></div>
                        <span class="text-sm font-medium">Green</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="color_scheme" value="#ff451c" {{ old('color_scheme') == '#ff451c' ? 'checked' : '' }} class="w-5 h-5">
                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #ff451c;"></div>
                        <span class="text-sm font-medium">Red</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="color_scheme" value="#ff00d8" {{ old('color_scheme') == '#ff00d8' ? 'checked' : '' }} class="w-5 h-5">
                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #ff00d8;"></div>
                        <span class="text-sm font-medium">Pink</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="color_scheme" value="#94c83e" {{ old('color_scheme') == '#94c83e' ? 'checked' : '' }} class="w-5 h-5">
                        <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #94c83e;"></div>
                        <span class="text-sm font-medium">Lime</span>
                    </label>
                </div>
                @error('color_scheme')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- SEO Fields -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>

                <div class="mb-4">
                    <label for="seo_title" class="block text-sm font-medium text-gray-700 mb-2">
                        SEO Title
                    </label>
                    <input type="text"
                           id="seo_title"
                           name="seo_title"
                           value="{{ old('seo_title') }}"
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                           placeholder="SEO optimized title">
                    @error('seo_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="seo_description" class="block text-sm font-medium text-gray-700 mb-2">
                        SEO Description
                    </label>
                    <textarea id="seo_description"
                              name="seo_description"
                              rows="3"
                              maxlength="500"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                              placeholder="Meta description for search engines">{{ old('seo_description') }}</textarea>
                    @error('seo_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="og_image" class="block text-sm font-medium text-gray-700 mb-2">
                        OG Image (для социальных сетей)
                    </label>
                    <input type="file"
                           id="og_image"
                           name="og_image"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                    <p class="mt-1 text-sm text-gray-500">Рекомендуемый размер: 1200x630px. Используется при репосте в соц.сетях</p>
                    @error('og_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="faq_schema" class="block text-sm font-medium text-gray-700 mb-2">
                        FAQ JSON-LD Schema
                    </label>
                    <textarea id="faq_schema"
                              name="faq_schema"
                              rows="10"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange font-mono text-sm"
                              placeholder='<script type="application/ld+json">...</script>'>{{ old('faq_schema') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Вставьте готовый код <script type="application/ld+json">...</script></p>
                    @error('faq_schema')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Published At -->
            <div class="mb-6">
                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                    Publish Date
                </label>
                <input type="datetime-local"
                       id="published_at"
                       name="published_at"
                       value="{{ old('published_at') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                <p class="mt-1 text-sm text-gray-500">Leave empty to save as draft</p>
                @error('published_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="ml-2 text-sm text-gray-700">Active (visible on public blog page)</span>
                </label>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Post
                </button>
                <a href="{{ route('admin.blog.index') }}"
                   class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: 'textarea#content',
                plugins: 'advlist autolink lists link image charmap preview hr anchor pagebreak code',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
                toolbar_mode: 'floating',
                height: 400,
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });

            // Sync TinyMCE content before form submit
            document.querySelector('form').addEventListener('submit', function(e) {
                if (tinymce.get('content')) {
                    tinymce.get('content').save();
                }

                // Check if content is empty
                var content = document.getElementById('content').value.trim();
                if (!content) {
                    e.preventDefault();
                    alert('Content is required');
                    return false;
                }
            });
        });
    </script>
@endpush
