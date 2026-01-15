<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header del perfil con foto --}}
        <div style="background: linear-gradient(to right, #059669, #10b981, #34d399) !important;" class="rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-6">
                @if(auth()->user()->profile_photo)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                         alt="Foto de perfil"
                         class="h-24 w-24 rounded-2xl object-cover shadow-lg ring-4 ring-white/30">
                @else
                    <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-24 w-24 items-center justify-center rounded-2xl backdrop-blur-sm">
                        <span class="text-4xl font-bold" style="color: white !important;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold" style="color: white !important;">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="mt-1" style="color: #d1fae5 !important;">
                        {{ auth()->user()->email ?? 'Sin correo registrado' }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                        <span style="background-color: rgba(255,255,255,0.2) !important; color: white !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>
                            Docente
                        </span>
                        @if(auth()->user()->identification)
                            <span style="background-color: rgba(255,255,255,0.2) !important; color: white !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                                ID: {{ auth()->user()->identification }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de foto de perfil --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            <div style="background-color: #ecfdf5 !important; border-bottom: 1px solid #a7f3d0 !important;" class="px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-semibold" style="color: #065f46 !important;">
                    <div style="background-color: #d1fae5 !important;" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </div>
                    Foto de Perfil
                </h3>
                <p class="mt-1 text-sm" style="color: #047857 !important;">Gestiona tu imagen de perfil</p>
            </div>
            <div class="p-6">
                {{-- Mostrar foto actual si existe --}}
                @if(auth()->user()->profile_photo)
                    <div class="mb-6 flex items-center gap-4 p-4 rounded-xl" style="background-color: #ecfdf5 !important;">
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                             alt="Foto actual" 
                             class="h-20 w-20 rounded-full object-cover ring-4 ring-emerald-200">
                        <div class="flex-1">
                            <p class="font-medium" style="color: #065f46 !important;">Foto actual</p>
                            <p class="text-sm" style="color: #059669 !important;">Para cambiarla, sube una nueva imagen abajo</p>
                        </div>
                        <button type="button" 
                                wire:click="deletePhoto"
                                wire:confirm="¿Estás seguro de que deseas eliminar tu foto de perfil?"
                                style="background-color: #fee2e2 !important; color: #dc2626 !important;"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:opacity-80 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Eliminar
                        </button>
                    </div>
                @endif

                {{-- Formulario para subir nueva foto --}}
                <x-filament-panels::form wire:submit="updatePhoto">
                    {{ $this->photoForm }}
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                style="background-color: #059669 !important; color: white !important;"
                                class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            Guardar Nueva Foto
                        </button>
                    </div>
                </x-filament-panels::form>
            </div>
        </div>

        {{-- Formulario de información personal --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            <div style="background-color: #f9fafb !important; border-bottom: 1px solid #e5e7eb !important;" class="px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-semibold" style="color: #111827 !important;">
                    <div style="background-color: #d1fae5 !important;" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    Información Personal
                </h3>
                <p class="mt-1 text-sm" style="color: #6b7280 !important;">Actualiza tu teléfono y dirección</p>
            </div>
            <div class="p-6">
                <x-filament-panels::form wire:submit="updateProfile">
                    {{ $this->profileForm }}
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                style="background-color: #10b981 !important; color: white !important;"
                                class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            Actualizar Información
                        </button>
                    </div>
                </x-filament-panels::form>
            </div>
        </div>

        {{-- Formulario de firma digital --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            <div style="background-color: #faf5ff !important; border-bottom: 1px solid #e9d5ff !important;" class="px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-semibold" style="color: #6b21a8 !important;">
                    <div style="background-color: #f3e8ff !important;" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9333ea" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    Mi Firma Digital
                </h3>
                <p class="mt-1 text-sm" style="color: #7c3aed !important;">Tu firma aparecerá en boletines y documentos oficiales</p>
            </div>
            <div class="p-6">
                {{-- Mostrar firma actual si existe --}}
                @if(auth()->user()->signature)
                    <div class="mb-6 flex items-center gap-4 p-4 rounded-xl" style="background-color: #faf5ff !important;">
                        <img src="{{ asset('storage/' . auth()->user()->signature) }}" 
                             alt="Firma actual" 
                             class="h-16 object-contain">
                        <div class="flex-1">
                            <p class="font-medium" style="color: #6b21a8 !important;">Firma actual</p>
                            <p class="text-sm" style="color: #9333ea !important;">Para cambiarla, sube una nueva imagen abajo</p>
                        </div>
                        <button type="button" 
                                wire:click="deleteSignature"
                                wire:confirm="¿Estás seguro de que deseas eliminar tu firma?"
                                style="background-color: #fee2e2 !important; color: #dc2626 !important;"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:opacity-80 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Eliminar
                        </button>
                    </div>
                @endif

                {{-- Formulario para subir firma --}}
                <x-filament-panels::form wire:submit="updateSignature">
                    {{ $this->signatureForm }}
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                style="background-color: #9333ea !important; color: white !important;"
                                class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            Guardar Firma
                        </button>
                    </div>
                </x-filament-panels::form>
            </div>
        </div>

        {{-- Formulario de contraseña --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            <div style="background-color: #fffbeb !important; border-bottom: 1px solid #fde68a !important;" class="px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-semibold" style="color: #92400e !important;">
                    <div style="background-color: #fef3c7 !important;" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    Cambiar Contraseña
                </h3>
                <p class="mt-1 text-sm" style="color: #a16207 !important;">Actualiza tu contraseña de acceso</p>
            </div>
            <div class="p-6">
                <x-filament-panels::form wire:submit="updatePassword">
                    {{ $this->passwordForm }}
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                style="background-color: #f59e0b !important; color: white !important;"
                                class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Cambiar Contraseña
                        </button>
                    </div>
                </x-filament-panels::form>
            </div>
        </div>

        {{-- Información adicional --}}
        <div style="background-color: #eff6ff !important; border: 1px solid #bfdbfe !important;" class="rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div style="background-color: #3b82f6 !important;" class="flex h-10 w-10 items-center justify-center rounded-xl flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold" style="color: #1e40af !important;">Seguridad de la cuenta</h4>
                    <p class="mt-1 text-sm" style="color: #1d4ed8 !important;">
                        Recomendamos usar una contraseña segura de al menos 8 caracteres que incluya letras mayúsculas, minúsculas, números y símbolos especiales.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
