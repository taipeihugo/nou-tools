<x-layout
    title="{{ $store->name }} - 優惠店家 - NOU 小幫手"
    description="{{ $store->name }} 的學生優惠與使用資訊。"
    :noindex="true"
>
    <div
        class="mx-auto max-w-4xl space-y-6"
        x-data="discountStoreReportForm({ storeName: @js($store->name) })"
    >
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <a
                href="{{ route('discount-stores.index') }}"
                class="inline-flex items-center gap-1 text-warm-600 transition hover:text-warm-900 hover:underline"
            >
                <x-heroicon-o-chevron-left class="size-4" />
                回到優惠店家列表
            </a>
        </div>

        <x-card>
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span
                        class="rounded-full bg-warm-100 px-3 py-1 font-medium text-warm-800"
                    >
                        @if ($store->category)
                            <x-dynamic-component
                                :component="$store->category->icon"
                                class="inline-block size-4"
                            />
                        @endif

                        {{ $store->category?->name ?? '未分類' }}
                    </span>
                    <span
                        class="rounded-full bg-orange-100 px-3 py-1 font-medium text-orange-700"
                    >
                        {{ $store->type->label() }}
                    </span>

                    @if ($store->city)
                        <span class="text-warm-500">
                            {{ $store->city }}
                            {{ $store->district }}
                        </span>
                    @endif
                </div>

                <h2 class="text-3xl font-bold text-warm-900">
                    {{ $store->name }}
                </h2>

                @if ($store->address)
                    <p class="flex items-center gap-1 text-sm text-warm-600">
                        @if ($store->type === \App\Enums\DiscountStoreType::Online)
                            @if (Str::startsWith($store->address, ['http://', 'https://']))
                                <x-heroicon-o-globe-alt
                                    class="inline-block size-4"
                                />
                                <a
                                    href="{{ $store->address }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-orange-600 hover:underline"
                                >
                                    {{ $store->address }}
                                </a>
                            @endif
                        @else
                            <x-heroicon-o-map-pin class="inline-block size-4" />
                            {{ $store->address }}
                        @endif
                    </p>
                @endif

                <div class="text-sm text-warm-700">
                    <p>
                        <span class="font-medium">優惠內容：</span>
                        {{ $store->discount_details }}
                    </p>

                    @if ($store->verification_method)
                        <p class="mt-1">
                            <span class="font-medium">驗證方式：</span>
                            {{ $store->verification_method }}
                        </p>
                    @endif
                </div>

                @if ($store->notes)
                    <p class="text-sm text-warm-500">
                        備註：{{ $store->notes }}
                    </p>
                @endif

                <div class="flex items-center gap-2 text-sm text-warm-500">
                    @if ($store->valid_reports_count > 0)
                        <span
                            class="inline-flex items-center gap-1 text-green-600"
                        >
                            <x-heroicon-s-check-circle class="size-4" />
                            {{ $store->valid_reports_count }}
                        </span>
                    @endif

                    @if ($store->invalid_reports_count > 0)
                        <span
                            class="inline-flex items-center gap-1 text-red-500"
                        >
                            <x-heroicon-s-x-circle class="size-4" />
                            {{ $store->invalid_reports_count }}
                        </span>
                    @endif

                    @if ($store->comments_count > 0)
                        <span class="text-xs text-warm-500">
                            {{ $store->comments_count }} 則留言
                        </span>
                    @endif
                </div>

                <div
                    class="flex items-center gap-2 border-t border-warm-100 pt-3"
                >
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg border border-green-200 px-3 py-1.5 text-sm text-green-600 transition hover:bg-green-50"
                        title="回報有效"
                        @click="openModal(true)"
                    >
                        <x-heroicon-o-check-circle class="size-4" />
                        回報有效
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-500 transition hover:bg-red-50"
                        title="回報無效"
                        @click="openModal(false)"
                    >
                        <x-heroicon-o-x-circle class="size-4" />
                        回報無效
                    </button>
                </div>

                <div
                    x-show="showModal"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                    @click.self="closeModal()"
                >
                    <div
                        class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
                    >
                        <h3 class="mb-4 text-lg font-semibold text-warm-900">
                            回報「
                            <span x-text="storeName"></span>
                            」
                            <span x-text="isValid ? '有效' : '無效'"></span>
                        </h3>
                        <form
                            action="{{ route('discount-stores.reports.store', $store) }}"
                            method="POST"
                            class="space-y-4"
                        >
                            @csrf
                            <input
                                type="hidden"
                                name="is_valid"
                                :value="isValid ? '1' : '0'"
                            />
                            <div>
                                <label
                                    for="report-comment-{{ $store->id }}"
                                    class="mb-1 block text-sm font-medium text-warm-700"
                                >
                                    備註（選填）
                                </label>
                                <textarea
                                    id="report-comment-{{ $store->id }}"
                                    name="comment"
                                    rows="2"
                                    class="w-full rounded-lg border border-warm-200 px-3 py-2 text-sm focus:border-orange-300 focus:ring-orange-300"
                                    placeholder="補充說明..."
                                ></textarea>
                            </div>
                            <div>
                                <x-turnstile-widget language="zh-tw" />
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition"
                                    :class="isValid ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                                >
                                    確認回報
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg border border-warm-200 px-4 py-2 text-sm text-warm-700 transition hover:bg-warm-50"
                                    @click="closeModal()"
                                >
                                    取消
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-card>

        @if ($store->comments->isNotEmpty())
            <x-card>
                <div class="space-y-2">
                    <h3 class="text-base font-semibold text-warm-700">留言</h3>
                    @foreach ($store->comments as $comment)
                        <div
                            class="rounded-lg bg-warm-50 px-3 py-2 text-sm text-warm-700"
                        >
                            <p class="mb-2 text-sm font-medium text-warm-900">
                                {{ $comment->nickname }}
                            </p>
                            <p>{{ $comment->content }}</p>
                            <span class="text-xs text-warm-400">
                                —
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        <x-card>
            <div class="space-y-2">
                <h3 class="text-base font-semibold text-warm-700">新增留言</h3>
                <form
                    action="{{ route('discount-stores.comments.store', $store) }}"
                    method="POST"
                    class="space-y-2"
                >
                    @csrf
                    <div class="flex flex-col gap-2">
                        <input
                            type="text"
                            name="nickname"
                            class="rounded-lg border border-warm-200 px-3 py-2 text-sm focus:border-orange-300 focus:ring-orange-300"
                            placeholder="暱稱"
                            maxlength="100"
                            required
                            value="{{ old('nickname') }}"
                        />
                        <textarea
                            type="text"
                            name="content"
                            class="flex-1 rounded-lg border border-warm-200 px-3 py-2 text-sm focus:border-orange-300 focus:ring-orange-300"
                            placeholder="留言（審核後顯示）..."
                            maxlength="1000"
                            rows="5"
                            required
                        >
{{ old('content') }}</textarea
                        >
                    </div>
                    <div>
                        <x-turnstile-widget language="zh-tw" />
                    </div>
                    <p class="text-xs text-warm-500">
                        為避免垃圾留言，留言將由管理員審核後才會顯示出來。
                    </p>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1 rounded-lg bg-warm-800 px-3 py-2 text-sm font-medium text-white transition hover:bg-warm-900"
                    >
                        <x-heroicon-o-chat-bubble-left class="size-4" />
                        送出
                    </button>
                </form>
            </div>
        </x-card>

        <script>
            function discountStoreReportForm(config) {
                return {
                    storeName: config.storeName,
                    showModal: false,
                    isValid: true,

                    openModal(isValid) {
                        this.isValid = isValid
                        this.showModal = true
                    },

                    closeModal() {
                        this.showModal = false
                    },
                }
            }
        </script>
    </div>
</x-layout>
