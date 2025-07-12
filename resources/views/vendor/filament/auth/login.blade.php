<x-filament-panels::page.simple>
    {{-- اسم المشروع --}}
    <div class="text-center mb-6">
        <h1 class="text-3xl font-extrabold tracking-tight text-white">
            Amper Office
        </h1>

        {{-- العنوان Welcome --}}
        <h2 class="text-xl font-semibold text-gray-300 mt-2">
            {{ app()->getLocale() === 'ar' ? 'مرحبًا بعودتك 👋' : 'Welcome Back 👋' }}
        </h2>

        {{-- زر تبديل اللغة --}}
        <div class="mt-4">
            <a href="{{ url()->current() }}?lang={{ app()->getLocale() === 'en' ? 'ar' : 'en' }}"
               class="inline-block px-3 py-1 text-sm bg-white text-black rounded hover:bg-gray-200 transition">
                {{ app()->getLocale() === 'en' ? 'العربية' : 'English' }}
            </a>
        </div>
    </div>

    {{-- رابط التسجيل إن وجد --}}
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}
            {{ $this->registerAction }}
        </x-slot>
    @endif

    {{-- بداية الفورم --}}
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>
