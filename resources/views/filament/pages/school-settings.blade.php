<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header con logo actual --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-6">
                @php
                    $currentLogo = \App\Models\SchoolSetting::get('school_logo');
                    $schoolName = \App\Models\SchoolSetting::get('school_name', 'NOTAS 2.0');
                @endphp
                
                <div class="flex-shrink-0">
                    @if($currentLogo)
                        <img src="{{ asset('storage/' . $currentLogo) }}" 
                             alt="Logo del colegio" 
                             class="w-24 h-24 object-contain rounded-lg border-2 border-gray-200 dark:border-gray-600">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-academic-cap class="w-12 h-12 text-white" />
                        </div>
                    @endif
                </div>
                
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $schoolName }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        {{ \App\Models\SchoolSetting::get('school_slogan', 'Configura tu institución educativa') }}
                    </p>
                    <div class="flex gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                        @if($dane = \App\Models\SchoolSetting::get('school_dane'))
                            <span class="flex items-center gap-1">
                                <x-heroicon-m-identification class="w-4 h-4" />
                                DANE: {{ $dane }}
                            </span>
                        @endif
                        @if($nit = \App\Models\SchoolSetting::get('school_nit'))
                            <span class="flex items-center gap-1">
                                <x-heroicon-m-document-text class="w-4 h-4" />
                                NIT: {{ $nit }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex-shrink-0">
                    <x-filament::button 
                        wire:click="saveAll"
                        icon="heroicon-o-check-circle"
                        size="lg"
                    >
                        Guardar Todo
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Información General --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            {{-- Previsualización de imágenes actuales --}}
            @php
                $currentLogo = \App\Models\SchoolSetting::get('school_logo');
                $currentFavicon = \App\Models\SchoolSetting::get('school_favicon');
            @endphp
            
            @if($currentLogo || $currentFavicon)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                    <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-3">Imágenes Actuales</h4>
                    <div class="flex gap-6 flex-wrap">
                        @if($currentLogo)
                            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                <img src="{{ asset('storage/' . $currentLogo) }}" 
                                     alt="Logo actual" 
                                     class="h-16 w-16 object-contain rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Logo del Colegio</p>
                                    <button type="button" 
                                            wire:click="deleteLogo"
                                            wire:confirm="¿Estás seguro de eliminar el logo?"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @if($currentFavicon)
                            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                <img src="{{ asset('storage/' . $currentFavicon) }}" 
                                     alt="Favicon actual" 
                                     class="h-16 w-16 object-contain rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Favicon</p>
                                    <button type="button" 
                                            wire:click="deleteFavicon"
                                            wire:confirm="¿Estás seguro de eliminar el favicon?"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">Para cambiar una imagen, sube una nueva en el formulario de abajo.</p>
                </div>
            @endif
            
            <form wire:submit="saveGeneral">
                {{ $this->generalForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-check">
                        Guardar Información General
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Información de Contacto --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <form wire:submit="saveContact">
                {{ $this->contactForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-check">
                        Guardar Contacto
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Apariencia --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <form wire:submit="saveAppearance">
                {{ $this->appearanceForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-paint-brush">
                        Guardar Apariencia
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Redes Sociales --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <form wire:submit="saveSocial">
                {{ $this->socialForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-share">
                        Guardar Redes Sociales
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Configuración de Login --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            {{-- Previsualización de imagen de fondo actual --}}
            @php
                $loginBackground = \App\Models\SchoolSetting::get('login_background');
            @endphp
            
            @if($loginBackground)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-indigo-50 dark:bg-indigo-900/20">
                    <h4 class="text-sm font-semibold text-indigo-800 dark:text-indigo-200 mb-3">Imagen de Fondo Actual</h4>
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('storage/' . $loginBackground) }}" 
                             alt="Fondo de login" 
                             class="h-20 w-32 object-cover rounded-lg border border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Imagen de fondo del login</p>
                            <button type="button" 
                                    wire:click="deleteLoginBackground"
                                    wire:confirm="¿Estás seguro de eliminar la imagen de fondo?"
                                    class="text-xs text-red-600 hover:text-red-800 font-medium mt-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            <form wire:submit="saveLogin">
                {{ $this->loginForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-lock-closed">
                        Guardar Configuración de Login
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Firmas para Documentos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            {{-- Previsualización de firmas actuales --}}
            @php
                $rectorSignature = \App\Models\SchoolSetting::get('rector_signature');
                $secretarySignature = \App\Models\SchoolSetting::get('secretary_signature');
                $rectorName = \App\Models\SchoolSetting::get('rector_name');
                $secretaryName = \App\Models\SchoolSetting::get('secretary_name');
            @endphp
            
            @if($rectorSignature || $secretarySignature)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-amber-50 dark:bg-amber-900/20">
                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-3">Firmas Actuales</h4>
                    <div class="flex gap-6 flex-wrap">
                        @if($rectorSignature)
                            <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                <img src="{{ asset('storage/' . $rectorSignature) }}" 
                                     alt="Firma del Rector" 
                                     class="h-16 object-contain">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $rectorName ?: 'Rector(a)' }}</p>
                                <p class="text-xs text-gray-500">Firma del Rector(a)</p>
                                <button type="button" 
                                        wire:click="deleteRectorSignature"
                                        wire:confirm="¿Estás seguro de eliminar la firma del rector?"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        @endif
                        
                        @if($secretarySignature)
                            <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                <img src="{{ asset('storage/' . $secretarySignature) }}" 
                                     alt="Firma del Secretario" 
                                     class="h-16 object-contain">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $secretaryName ?: 'Secretario(a)' }}</p>
                                <p class="text-xs text-gray-500">Firma del Secretario(a)</p>
                                <button type="button" 
                                        wire:click="deleteSecretarySignature"
                                        wire:confirm="¿Estás seguro de eliminar la firma del secretario?"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <form wire:submit="saveSignatures">
                {{ $this->signaturesForm }}
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-pencil-square">
                        Guardar Firmas
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
