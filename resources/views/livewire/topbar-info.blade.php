<div class="topbar-info-wrapper">
    <div class="topbar-info-widget" wire:poll.30s>
    <style>
        .topbar-info-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 0.25rem 0;
        }
        
        .topbar-info-widget {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.4rem 1rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,0.15);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            max-width: 100%;
            flex-wrap: nowrap;
        }
        
        .dark .topbar-info-widget {
            background: linear-gradient(135deg, rgba(30,41,59,0.95) 0%, rgba(15,23,42,0.9) 100%);
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        }
        
        /* Logo Section */
        .topbar-logo-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-right: 1rem;
            border-right: 1px solid rgba(148,163,184,0.2);
            flex-shrink: 0;
        }
        
        .topbar-logo {
            height: 32px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
            transition: transform 0.2s ease;
        }
        
        .topbar-logo:hover {
            transform: scale(1.05);
        }
        
        .topbar-school-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: #0f172a;
            white-space: nowrap;
            letter-spacing: -0.01em;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .dark .topbar-school-name {
            color: #f8fafc;
        }
        
        /* User Section */
        .topbar-user-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-right: 1rem;
            border-right: 1px solid rgba(148,163,184,0.2);
            flex-shrink: 0;
        }
        
        .topbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            color: white;
            text-transform: uppercase;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            flex-shrink: 0;
        }
        
        .topbar-avatar:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(0,0,0,0.2);
        }
        
        .topbar-avatar.admin { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .topbar-avatar.teacher { background: linear-gradient(135deg, #10b981, #047857); }
        .topbar-avatar.student { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        
        .topbar-avatar-img {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            flex-shrink: 0;
        }
        
        .topbar-avatar-img:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(0,0,0,0.2);
        }
        
        .topbar-avatar-img.admin { border: 2px solid #3b82f6; }
        .topbar-avatar-img.teacher { border: 2px solid #10b981; }
        .topbar-avatar-img.student { border: 2px solid #8b5cf6; }
        
        .topbar-user-info {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }
        
        .topbar-user-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: #0f172a;
            line-height: 1.2;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .dark .topbar-user-name {
            color: #f8fafc;
        }
        
        .topbar-role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.5rem;
            border-radius: 5px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            width: fit-content;
        }
        
        .topbar-role-badge.admin {
            background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(29,78,216,0.15));
            color: #2563eb;
        }
        .topbar-role-badge.teacher {
            background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(4,120,87,0.15));
            color: #059669;
        }
        .topbar-role-badge.student {
            background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(109,40,217,0.15));
            color: #7c3aed;
        }
        
        /* Period Section */
        .topbar-period-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        
        .topbar-period-info {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }
        
        .topbar-period-label {
            font-size: 0.65rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
        }
        
        .topbar-period-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #0f172a;
            line-height: 1.2;
        }
        
        .dark .topbar-period-name {
            color: #f8fafc;
        }
        
        /* Countdown Timer - MEJORADO */
        .topbar-countdown {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            border: 1px solid rgba(14,165,233,0.2);
        }
        
        .dark .topbar-countdown {
            background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, rgba(2,132,199,0.15) 100%);
        }
        
        .topbar-countdown.warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: rgba(245,158,11,0.3);
        }
        
        .dark .topbar-countdown.warning {
            background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, rgba(217,119,6,0.15) 100%);
        }
        
        .topbar-countdown.danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: rgba(239,68,68,0.3);
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .dark .topbar-countdown.danger {
            background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(220,38,38,0.15) 100%);
        }
        
        .topbar-countdown.expired {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-color: rgba(100,116,139,0.2);
        }
        
        @keyframes pulse-glow {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(239,68,68,0.4);
            }
            50% { 
                box-shadow: 0 0 0 4px rgba(239,68,68,0.1);
            }
        }
        
        .topbar-countdown-icon {
            width: 20px;
            height: 20px;
            color: #0ea5e9;
            flex-shrink: 0;
        }
        
        .topbar-countdown.warning .topbar-countdown-icon {
            color: #f59e0b;
        }
        
        .topbar-countdown.danger .topbar-countdown-icon {
            color: #ef4444;
        }
        
        .topbar-countdown.expired .topbar-countdown-icon {
            color: #94a3b8;
        }
        
        .topbar-countdown-timer {
            display: flex;
            align-items: center;
            gap: 0.1rem;
        }
        
        .topbar-countdown-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 32px;
            padding: 0.2rem 0.3rem;
            background: rgba(255,255,255,0.8);
            border-radius: 6px;
        }
        
        .dark .topbar-countdown-block {
            background: rgba(15,23,42,0.5);
        }
        
        .topbar-countdown-number {
            font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
            font-weight: 800;
            font-size: 0.95rem;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        
        .dark .topbar-countdown-number {
            color: #f8fafc;
        }
        
        .topbar-countdown.danger .topbar-countdown-number {
            color: #dc2626;
        }
        
        .topbar-countdown-unit {
            font-size: 0.5rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-top: 0.05rem;
        }
        
        .topbar-countdown-colon {
            font-weight: 800;
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1;
            margin: 0 0.05rem;
            margin-bottom: 0.6rem;
        }
        
        .topbar-countdown.danger .topbar-countdown-colon {
            color: #fca5a5;
            animation: blink 1s step-end infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .topbar-countdown-suffix {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 0.25rem;
        }
        
        .topbar-countdown-expired-text {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            font-style: italic;
        }
        
        .topbar-no-period {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            border: 1px solid rgba(245,158,11,0.3);
        }
        
        .topbar-no-period-icon {
            width: 18px;
            height: 18px;
            color: #d97706;
        }
        
        .topbar-no-period-text {
            font-size: 0.75rem;
            color: #92400e;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .topbar-info-widget {
                gap: 0.75rem;
                padding: 0.4rem 1rem;
            }
            
            .topbar-school-name {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
        
        @media (max-width: 1024px) {
            .topbar-info-widget {
                gap: 0.5rem;
                padding: 0.4rem 0.75rem;
            }
            
            .topbar-school-name {
                display: none;
            }
            
            .topbar-user-info {
                display: none;
            }
            
            .topbar-period-info {
                display: none;
            }
            
            .topbar-logo-section,
            .topbar-user-section {
                padding-right: 0.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .topbar-countdown-timer {
                gap: 0.1rem;
            }
            
            .topbar-countdown-block {
                min-width: 32px;
                padding: 0.2rem 0.25rem;
            }
            
            .topbar-countdown-number {
                font-size: 0.9rem;
            }
            
            .topbar-countdown-suffix {
                display: none;
            }
        }
    </style>
    
    <!-- Logo y Nombre del Colegio -->
    <div class="topbar-logo-section">
        @if($this->schoolLogo)
            <img src="{{ $this->schoolLogo }}" alt="Logo" class="topbar-logo">
        @else
            <svg class="topbar-logo" style="height: 36px; width: 36px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3ZM18.82 9L12 12.72L5.18 9L12 5.28L18.82 9ZM17 15.99L12 18.72L7 15.99V12.27L12 15L17 12.27V15.99Z" fill="currentColor" class="text-primary-500"/>
            </svg>
        @endif
        <span class="topbar-school-name">{{ $this->schoolName }}</span>
    </div>
    
    <!-- Usuario Logueado -->
    <div class="topbar-user-section">
        @if($this->user->profile_photo)
            <img src="{{ asset('storage/' . $this->user->profile_photo) }}" 
                 alt="Foto de perfil" 
                 class="topbar-avatar-img {{ $panelType }}">
        @else
            <div class="topbar-avatar {{ $panelType }}">
                {{ strtoupper(substr($this->user->name ?? 'U', 0, 2)) }}
            </div>
        @endif
        <div class="topbar-user-info">
            <span class="topbar-user-name">{{ $this->user->name ?? 'Usuario' }}</span>
            <span class="topbar-role-badge {{ $panelType }}">{{ $this->roleDisplay }}</span>
        </div>
    </div>
    
    <!-- Período Activo y Cronómetro -->
    @if($this->activePeriod)
        <div class="topbar-period-section">
            <div class="topbar-period-info">
                <span class="topbar-period-label">Período Activo</span>
                <span class="topbar-period-name">
                    {{ $this->activePeriod->name }} - {{ $this->activePeriod->schoolYear->name ?? '' }}
                </span>
            </div>
            
            @if($this->timeRemaining)
                @php
                    $time = $this->timeRemaining;
                    $countdownClass = 'topbar-countdown';
                    if ($time['expired']) {
                        $countdownClass .= ' expired';
                    } elseif ($time['days'] <= 3) {
                        $countdownClass .= ' danger';
                    } elseif ($time['days'] <= 7) {
                        $countdownClass .= ' warning';
                    }
                @endphp
                
                <div class="{{ $countdownClass }}">
                    @if($time['expired'])
                        <svg class="topbar-countdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="topbar-countdown-expired-text">Período finalizado</span>
                    @else
                        <svg class="topbar-countdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        
                        <div class="topbar-countdown-timer">
                            <!-- Días -->
                            <div class="topbar-countdown-block">
                                <span class="topbar-countdown-number">{{ str_pad($time['days'], 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="topbar-countdown-unit">días</span>
                            </div>
                            
                            <span class="topbar-countdown-colon">:</span>
                            
                            <!-- Horas -->
                            <div class="topbar-countdown-block">
                                <span class="topbar-countdown-number">{{ str_pad($time['hours'], 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="topbar-countdown-unit">hrs</span>
                            </div>
                            
                            <span class="topbar-countdown-colon">:</span>
                            
                            <!-- Minutos -->
                            <div class="topbar-countdown-block">
                                <span class="topbar-countdown-number">{{ str_pad($time['minutes'], 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="topbar-countdown-unit">min</span>
                            </div>
                        </div>
                        
                        <span class="topbar-countdown-suffix">restantes</span>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="topbar-period-section">
            <div class="topbar-no-period">
                <svg class="topbar-no-period-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="topbar-no-period-text">Sin período activo</span>
            </div>
        </div>
    @endif
</div>
</div>
