<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Boletín - {{ $student->name }}</title>
    <style>
        /* ================================================================
           BOLETÍN ACADÉMICO V3 - OPTIMIZADO PARA IMPRESIÓN
           Tamaño: Carta (8.5" x 11" = 216mm x 279mm)
           Márgenes: Superior 15mm, Inferior 15mm, Izq 18mm, Der 18mm
           Área imprimible: 180mm x 249mm
           ================================================================ */
        
        @page {
            size: letter portrait;
            margin: 50mm 50mm 50mm 50mm;
        }
        
        @media print {
            body { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .document {
                page-break-inside: avoid;
            }
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1a202c;
            line-height: 1.45;
            background: #fff;
            padding-top: 15mm;
        }

        /* === CONTENEDOR PRINCIPAL === */
        .document {
            width: 100%;
            max-width: 180mm;
            margin: 0 auto;
            padding: 0;
        }

        /* === HEADER INSTITUCIONAL === */
        .header {
            border-bottom: 2px solid #7c9cbf;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        
        .header-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-logo {
            width: 75px;
            vertical-align: middle;
            text-align: center;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            border: 2px solid #7c9cbf;
            border-radius: 8px;
            background: #ffffff;
            text-align: center;
            padding: 4px;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .logo-container img {
            max-width: 72px;
            max-height: 72px;
            object-fit: contain;
        }
        
        .logo-placeholder {
            font-size: 22px;
            font-weight: bold;
            color: #7c9cbf;
            line-height: 57px;
        }
        
        .header-info {
            vertical-align: middle;
            padding-left: 25px;
        }
        
        .institution-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1e40af;
            letter-spacing: 0.2px;
            margin-bottom: 4px;
        }
        
        .institution-meta {
            font-size: 7.5pt;
            color: #4a5568;
            line-height: 1.4;
        }
        
        .header-year {
            width: 75px;
            vertical-align: middle;
            text-align: right;
        }
        
        .year-badge {
            display: inline-block;
            background: #93c5fd;
            color: #1e3a5f;
            padding: 6px 10px;
            border-radius: 6px;
            text-align: center;
        }
        
        .year-label {
            font-size: 5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        
        .year-value {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.1;
        }

        /* === TÍTULO DOCUMENTO === */
        .doc-title {
            background: #e0f2fe;
            border-left: 3px solid #7dd3fc;
            padding: 6px 12px;
            margin-bottom: 10px;
        }
        
        .doc-title-text {
            font-size: 10pt;
            font-weight: bold;
            color: #0369a1;
        }
        
        .doc-title-period {
            font-size: 8pt;
            color: #4a5568;
            margin-left: 8px;
        }

        /* === TARJETA ESTUDIANTE === */
        .student-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }
        
        .student-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .student-grid td {
            padding: 3px 8px;
            vertical-align: top;
        }
        
        .field-label {
            font-size: 6.5pt;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            font-weight: 600;
        }
        
        .field-value {
            font-size: 9pt;
            font-weight: 600;
            color: #1a202c;
            margin-top: 1px;
        }
        
        .field-divider {
            border-right: 1px solid #e2e8f0;
        }

        /* === TABLA DE CALIFICACIONES === */
        .grades-section {
            margin-bottom: 10px;
        }
        
        .section-header {
            background: #93c5fd;
            color: #1e3a5f;
            padding: 5px 10px;
            border-radius: 3px 3px 0 0;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e0;
            border-top: none;
        }
        
        .grades-table thead th {
            background: #edf2f7;
            color: #2d3748;
            padding: 6px 4px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 1.5px solid #cbd5e0;
            border-right: 1px solid #e2e8f0;
        }
        
        .grades-table thead th:first-child {
            text-align: left;
            padding-left: 8px;
            width: 30%;
        }
        
        .grades-table thead th:last-child {
            border-right: none;
        }
        
        .grades-table thead th.col-period {
            width: 8%;
            background: #e2e8f0;
        }
        
        .grades-table thead th.col-def {
            width: 10%;
            background: #e9d5ff;
            color: #6b21a8;
        }
        
        .grades-table thead th.col-faults {
            width: 8%;
            background: #fecaca;
            color: #991b1b;
        }
        
        .grades-table thead th.col-behavior {
            width: 10%;
            background: #fce7f3;
            color: #9d174d;
        }
        
        .grades-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .grades-table tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        .grades-table tbody td {
            padding: 5px 4px;
            text-align: center;
            font-size: 8.5pt;
            border-right: 1px solid #edf2f7;
            vertical-align: middle;
        }
        
        .grades-table tbody td:first-child {
            text-align: left;
            padding-left: 8px;
            font-weight: 600;
            font-size: 8pt;
            color: #2d3748;
        }
        
        .grades-table tbody td:last-child {
            border-right: none;
        }

        /* Estilos de notas con color */
        .grade-num {
            font-weight: 700;
            font-size: 8.5pt;
        }
        
        .grade-superior { color: #7c3aed; }
        .grade-alto { color: #3b82f6; }
        .grade-basico { color: #f59e0b; }
        .grade-bajo { color: #ef4444; }
        
        .grade-muted {
            color: #a0aec0;
            font-size: 8pt;
        }

        /* Celda definitiva */
        .td-def {
            background: #faf5ff !important;
            font-size: 8.5pt !important;
            font-weight: 800 !important;
        }

        /* Badge de nivel */
        .level-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        
        .level-superior { background: #f3e8ff; color: #7c3aed; }
        .level-alto { background: #e0f2fe; color: #0284c7; }
        .level-basico { background: #fef9c3; color: #ca8a04; }
        .level-bajo { background: #ffe4e6; color: #e11d48; }

        /* Fallas */
        .faults-num {
            font-size: 8pt;
            color: #718096;
        }
        
        .faults-alert {
            color: #c53030;
            font-weight: 700;
        }
        
        /* Celda comportamiento */
        .td-behavior {
            background: #fdf2f8 !important;
        }
        
        /* Badge de comportamiento */
        .behavior-badge {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 5.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1px;
        }
        
        .behavior-superior { background: #f3e8ff; color: #7c3aed; }
        .behavior-alto { background: #e0f2fe; color: #0284c7; }
        .behavior-basico { background: #fef9c3; color: #ca8a04; }
        .behavior-bajo { background: #ffe4e6; color: #e11d48; }
        
        /* Celda fallas */
        .td-faults {
            background: #fef2f2 !important;
        }

        /* === FILA PROMEDIO === */
        .row-summary {
            background: #edf2f7 !important;
            border-top: 1.5px solid #a0aec0;
        }
        
        .row-summary td {
            padding: 6px 4px !important;
            font-weight: 700;
        }
        
        .row-summary td:first-child {
            font-size: 8pt;
            text-transform: uppercase;
            color: #4a5568;
            background: #e2e8f0;
        }
        
        .summary-avg {
            font-size: 11pt !important;
            color: #3b82f6;
        }

        /* === FILA ESTADO === */
        .row-status {
            background: #334155 !important;
        }
        
        .row-status td {
            padding: 6px 4px !important;
            color: white;
            text-align: center;
        }
        
        .row-status td:first-child {
            background: #1e293b !important;
            font-size: 7pt;
            text-transform: uppercase;
        }
        
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        
        .status-approved { background: #86efac; color: #166534; }
        .status-pending { background: #fed7aa; color: #9a3412; }
        .status-failed { background: #fca5a5; color: #991b1b; }
        
        .status-detail {
            font-size: 7pt;
            opacity: 0.85;
            margin-left: 6px;
        }

        /* === PANEL DE RESUMEN === */
        .summary-panel {
            margin: 10px 0;
        }
        
        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
        }
        
        .summary-box {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
            border-top: 2px solid;
        }
        
        .box-avg { border-top-color: #93c5fd; }
        .box-level { border-top-color: #86efac; }
        .box-rank { border-top-color: #fde047; }
        .box-behavior { border-top-color: #f9a8d4; }
        .box-absences { border-top-color: #a1a1aa; }
        
        .box-value {
            font-size: 12pt;
            font-weight: 800;
            line-height: 1.2;
        }
        
        .val-avg { color: #3b82f6; }
        .val-level { color: #22c55e; }
        .val-rank { color: #eab308; }
        .val-behavior { color: #ec4899; }
        .val-absences { color: #71717a; }
        
        .box-label {
            font-size: 6pt;
            color: #718096;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-top: 1px;
        }

        /* === ESCALA DE VALORACIÓN === */
        .scale-section {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
        }
        
        .scale-title {
            font-size: 7pt;
            font-weight: 700;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 5px;
        }
        
        .scale-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .scale-grid td {
            padding: 3px 8px;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        
        .scale-indicator {
            width: 7px;
            height: 7px;
            border-radius: 2px;
            display: inline-block;
            margin-right: 3px;
            vertical-align: middle;
        }
        
        .ind-superior { background: #a78bfa; }
        .ind-alto { background: #7dd3fc; }
        .ind-basico { background: #fcd34d; }
        .ind-bajo { background: #fca5a5; }
        
        .scale-name {
            font-weight: 700;
            color: #2d3748;
        }
        
        .scale-range {
            color: #718096;
            font-size: 7pt;
        }

        /* === SECCIÓN DE FIRMAS === */
        .signatures-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }
        
        .signatures-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 0 25px;
        }
        
        .signature-image {
            height: 50px;
            max-width: 150px;
            object-fit: contain;
            margin: 0 auto;
        }
        
        .signature-line {
            width: 150px;
            height: 50px;
            border-bottom: 1.5px solid #2d3748;
            margin: 0 auto 4px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        
        .signature-name {
            font-size: 8pt;
            font-weight: 700;
            color: #1a202c;
        }
        
        .signature-role {
            font-size: 7pt;
            color: #718096;
            margin-top: 2px;
        }

        /* === FOOTER === */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1.5px solid #93c5fd;
        }
        
        .footer-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .footer-brand {
            font-size: 7pt;
            font-weight: 700;
            color: #2c5282;
        }
        
        .footer-tagline {
            font-size: 6pt;
            color: #718096;
        }
        
        .footer-meta {
            text-align: right;
            font-size: 6pt;
            color: #a0aec0;
        }

        /* === INDICADOR VISUAL === */
        .performance-indicator {
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-right: 2px;
            vertical-align: middle;
        }
        
        .perf-excellent { background: #86efac; }
        .perf-good { background: #93c5fd; }
        .perf-regular { background: #fcd34d; }
        .perf-low { background: #fca5a5; }
    </style>
</head>
<body>
    <div class="document">
        
        <!-- ==================== HEADER INSTITUCIONAL ==================== -->
        <div class="header">
            <table class="header-grid">
                <tr>
                    <td class="header-logo">
                        <div class="logo-container">
                            @if($settings['logo'])
                                <img src="{{ $settings['logo'] }}" alt="Logo">
                            @else
                                <span class="logo-placeholder">IE</span>
                            @endif
                        </div>
                    </td>
                    <td class="header-info">
                        <div class="institution-name">{{ strtoupper($settings['name']) }}</div>
                        <div class="institution-meta">
                            @if($settings['resolution'])Res. {{ $settings['resolution'] }}@endif
                            @if($settings['dane']) | DANE: {{ $settings['dane'] }}@endif
                            @if($settings['nit']) | NIT: {{ $settings['nit'] }}@endif
                            <br>
                            <strong>Sede:</strong> {{ $sede->name }}
                            @if($settings['city']) | {{ $settings['city'] }}@endif
                            @if($settings['department']), {{ $settings['department'] }}@endif
                        </div>
                    </td>
                    <td class="header-year">
                        <div class="year-badge">
                            <div class="year-label">Año</div>
                            <div class="year-value">{{ $schoolYear->name }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ==================== TÍTULO DEL DOCUMENTO ==================== -->
        <div class="doc-title">
            <span class="doc-title-text">INFORME ACADÉMICO</span>
            <span class="doc-title-period">| {{ $period->name }}</span>
        </div>

        <!-- ==================== INFORMACIÓN DEL ESTUDIANTE ==================== -->
        <div class="student-card">
            <table class="student-grid">
                <tr>
                    <td class="field-divider" style="width: 44%;">
                        <div class="field-label">Nombre del Estudiante</div>
                        <div class="field-value">{{ strtoupper($student->name) }}</div>
                    </td>
                    <td class="field-divider" style="width: 16%;">
                        <div class="field-label">Identificación</div>
                        <div class="field-value">{{ $student->identification ?? 'N/A' }}</div>
                    </td>
                    <td class="field-divider" style="width: 14%;">
                        <div class="field-label">Grado</div>
                        <div class="field-value">{{ $grade->name }}</div>
                    </td>
                    <td style="width: 26%;">
                        <div class="field-label">Director(a) de Grupo</div>
                        <div class="field-value">{{ $director?->name ?? 'Sin asignar' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ==================== TABLA DE CALIFICACIONES ==================== -->
        <div class="grades-section">
            <div class="section-header">
                CALIFICACIONES POR ÁREA | {{ count($subjects) }} Asignaturas
            </div>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Área / Asignatura</th>
                        @foreach($periods as $p)
                            <th class="col-period">P{{ $p->number }}</th>
                        @endforeach
                        <th class="col-def">DEF.</th>
                        <th class="col-faults">Fallas</th>
                        <th class="col-behavior">Comp.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        @php
                            $final = $subject['final'];
                            $gradeClass = 'grade-basico';
                            $levelClass = 'level-basico';
                            
                            if ($final !== null) {
                                if ($final >= 4.6) {
                                    $gradeClass = 'grade-superior';
                                    $levelClass = 'level-superior';
                                } elseif ($final >= 4.0) {
                                    $gradeClass = 'grade-alto';
                                    $levelClass = 'level-alto';
                                } elseif ($final >= 3.0) {
                                    $gradeClass = 'grade-basico';
                                    $levelClass = 'level-basico';
                                } else {
                                    $gradeClass = 'grade-bajo';
                                    $levelClass = 'level-bajo';
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                @if($final !== null)
                                    <span class="performance-indicator {{ $final >= 4.6 ? 'perf-excellent' : ($final >= 4.0 ? 'perf-good' : ($final >= 3.0 ? 'perf-regular' : 'perf-low')) }}"></span>
                                @endif
                                {{ $subject['name'] }}
                            </td>
                            @foreach($periods as $p)
                                @php
                                    $pScore = $subject['periods'][$p->number] ?? null;
                                    $pClass = 'grade-basico';
                                    if ($pScore !== null) {
                                        if ($pScore >= 4.6) $pClass = 'grade-superior';
                                        elseif ($pScore >= 4.0) $pClass = 'grade-alto';
                                        elseif ($pScore >= 3.0) $pClass = 'grade-basico';
                                        else $pClass = 'grade-bajo';
                                    }
                                @endphp
                                <td>
                                    @if($pScore !== null)
                                        <span class="grade-num {{ $pClass }}">{{ number_format($pScore, 1) }}</span>
                                    @else
                                        <span class="grade-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="td-def">
                                @if($final !== null)
                                    <span class="grade-num {{ $gradeClass }}">{{ number_format($final, 1) }}</span>
                                @else
                                    <span class="grade-muted">—</span>
                                @endif
                            </td>
                            <td class="td-faults">
                                @php
                                    $subjectAbsences = $subject['absences'] ?? $subject['totalAbsences'] ?? 0;
                                @endphp
                                @if($subjectAbsences > 0)
                                    <span class="faults-alert">{{ $subjectAbsences }}</span>
                                @else
                                    <span class="faults-num">0</span>
                                @endif
                            </td>
                            <td class="td-behavior">
                                @php
                                    $subjectBehavior = $subject['behavior'] ?? null;
                                    $behaviorClass = 'behavior-basico';
                                    if ($subjectBehavior === 'SUPERIOR') $behaviorClass = 'behavior-superior';
                                    elseif ($subjectBehavior === 'ALTO') $behaviorClass = 'behavior-alto';
                                    elseif ($subjectBehavior === 'BAJO') $behaviorClass = 'behavior-bajo';
                                @endphp
                                @if($subjectBehavior)
                                    <span class="behavior-badge {{ $behaviorClass }}">{{ $subjectBehavior }}</span>
                                @else
                                    <span class="grade-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- FILA PROMEDIO GENERAL -->
                    @php
                        $avgLevelClass = 'level-basico';
                        if ($generalAverage !== null) {
                            if ($generalAverage >= 4.6) { $avgLevelClass = 'level-superior'; }
                            elseif ($generalAverage >= 4.0) { $avgLevelClass = 'level-alto'; }
                            elseif ($generalAverage >= 3.0) { $avgLevelClass = 'level-basico'; }
                            else { $avgLevelClass = 'level-bajo'; }
                        }
                    @endphp
                    <tr class="row-summary">
                        <td>PROMEDIO GENERAL</td>
                        @foreach($periods as $p)
                            <td><span class="grade-muted">—</span></td>
                        @endforeach
                        <td class="td-def">
                            <span class="summary-avg">{{ $generalAverage !== null ? number_format($generalAverage, 2) : '—' }}</span>
                        </td>
                        <td class="td-faults"></td>
                        <td class="td-behavior"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ==================== PANEL DE RESUMEN ==================== -->
        <div class="summary-panel">
            <table class="summary-grid">
                <tr>
                    <td style="width: 33.33%;">
                        <div class="summary-box box-avg">
                            <div class="box-value val-avg">{{ $generalAverage !== null ? number_format($generalAverage, 2) : '—' }}</div>
                            <div class="box-label">Promedio</div>
                        </div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="summary-box box-level">
                            <div class="box-value val-level">{{ $performanceLevel !== '-' ? $performanceLevel : '—' }}</div>
                            <div class="box-label">Desempeño</div>
                        </div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="summary-box box-rank">
                            <div class="box-value val-rank">{{ $position }}<sup style="font-size: 6pt;">°</sup>/{{ $totalStudents }}</div>
                            <div class="box-label">Puesto</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ==================== ESCALA DE VALORACIÓN ==================== -->
        <div class="scale-section">
            <div class="scale-title">Escala de Valoración Institucional</div>
            <table class="scale-grid">
                <tr>
                    <td style="width: 25%;">
                        <span class="scale-indicator ind-superior"></span>
                        <span class="scale-name">Superior</span>
                        <span class="scale-range">(4.6-5.0)</span>
                    </td>
                    <td style="width: 25%;">
                        <span class="scale-indicator ind-alto"></span>
                        <span class="scale-name">Alto</span>
                        <span class="scale-range">(4.0-4.5)</span>
                    </td>
                    <td style="width: 25%;">
                        <span class="scale-indicator ind-basico"></span>
                        <span class="scale-name">Básico</span>
                        <span class="scale-range">(3.0-3.9)</span>
                    </td>
                    <td style="width: 25%;">
                        <span class="scale-indicator ind-bajo"></span>
                        <span class="scale-name">Bajo</span>
                        <span class="scale-range">(1.0-2.9)</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ==================== FIRMAS ==================== -->
        <div class="signatures-section">
            <table class="signatures-grid">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-line">
                            @if(isset($directorSignature) && $directorSignature)
                                <img src="{{ $directorSignature }}" alt="Firma Director" class="signature-image">
                            @endif
                        </div>
                        <div class="signature-name">{{ $director?->name ?? 'Director(a) de Grupo' }}</div>
                        <div class="signature-role">Director(a) de Grupo</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-line">
                            @if(isset($settings['rector_signature']) && $settings['rector_signature'])
                                <img src="{{ $settings['rector_signature'] }}" alt="Firma Rector" class="signature-image">
                            @endif
                        </div>
                        <div class="signature-name">{{ $settings['rector_name'] ?? 'Rector(a)' }}</div>
                        <div class="signature-role">Rector(a)</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="footer">
            <table class="footer-grid">
                <tr>
                    <td>
                        <div class="footer-brand">NOTAS 2.0</div>
                        <div class="footer-tagline">Sistema de Gestión Académica</div>
                    </td>
                    <td class="footer-meta">
                        Generado: {{ $generatedAt->format('d/m/Y H:i') }}<br>
                        Documento válido sin firmas cuando se genera digitalmente
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>
</html>
