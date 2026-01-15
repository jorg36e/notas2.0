<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Boletín - {{ $student->name }}</title>
    <style>
        /* ===================== PRINT OPTIMIZED ===================== */
        @page {
            margin: 10mm;
            size: letter portrait;
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #374151;
            line-height: 1.3;
            background-color: #ffffff;
        }
        
        /* 3 FUENTES: DejaVu Sans (normal), DejaVu Serif (titulos), DejaVu Sans Condensed (tablas) */
        .font-title { font-family: 'DejaVu Serif', Georgia, serif; }
        .font-normal { font-family: 'DejaVu Sans', sans-serif; }
        .font-table { font-family: 'DejaVu Sans', sans-serif; }
        
        .main-container {
            border: 1.5px solid #6b7280;
        }
        
        /* ===================== HEADER - PASTEL BLUE ===================== */
        .header {
            background-color: #dbeafe;
            padding: 12px 15px;
            border-bottom: 1.5px solid #93c5fd;
        }
        
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-cell { width: 55px; vertical-align: middle; }
        
        .logo-wrapper {
            width: 50px;
            height: 50px;
            background-color: #ffffff;
            border: 1px solid #93c5fd;
            border-radius: 4px;
            text-align: center;
            padding: 2px;
        }
        
        .logo-wrapper img { width: 46px; height: 46px; object-fit: contain; }
        
        .logo-placeholder {
            width: 46px;
            height: 46px;
            background-color: #fecaca;
            border-radius: 3px;
            line-height: 46px;
            text-align: center;
            color: #991b1b;
            font-weight: bold;
            font-size: 16px;
        }
        
        .school-info-cell { vertical-align: middle; padding-left: 12px; }
        .school-name { font-family: 'DejaVu Serif', Georgia, serif; font-size: 14px; font-weight: bold; color: #1e3a8a; }
        .school-details { font-size: 8px; color: #3b82f6; margin-top: 3px; }
        
        .year-badge-cell { width: 85px; vertical-align: middle; text-align: right; }
        .year-badge {
            display: inline-block;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: center;
        }
        .year-label { font-size: 6px; color: #92400e; text-transform: uppercase; font-weight: bold; }
        .year-value { font-size: 16px; font-weight: bold; color: #78350f; margin-top: 1px; }
        
        /* ===================== TITLE BAR - PASTEL YELLOW ===================== */
        .title-bar {
            background-color: #fef9c3;
            border-bottom: 1.5px solid #fcd34d;
            padding: 8px 15px;
            text-align: center;
        }
        .title-main { font-family: 'DejaVu Serif', Georgia, serif; font-size: 12px; font-weight: bold; color: #78350f; text-transform: uppercase; letter-spacing: 2px; }
        .title-period { font-size: 9px; color: #92400e; margin-top: 2px; }
        
        /* ===================== STUDENT INFO ===================== */
        .student-info-section { padding: 10px 12px; background-color: #f9fafb; }
        .student-card {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
        }
        .student-card-header {
            background-color: #e0e7ff;
            padding: 5px 10px;
            color: #3730a3;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #c7d2fe;
        }
        .student-card-body { padding: 8px 10px; }
        .student-info-table { width: 100%; border-collapse: collapse; }
        .student-info-table td { padding: 3px 6px; vertical-align: top; }
        .info-label { font-size: 6px; color: #6b7280; text-transform: uppercase; font-weight: bold; }
        .info-value { font-size: 10px; font-weight: bold; color: #1f2937; margin-top: 1px; }
        .cell-divider { border-right: 1px solid #e5e7eb; }
        
        /* ===================== GRADES TABLE ===================== */
        .grades-section { padding: 6px 12px 10px; }
        
        .grades-wrapper {
            border: 1px solid #9ca3af;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .grades-table-header {
            background-color: #e0e7ff;
            padding: 6px 12px;
            color: #3730a3;
            border-bottom: 1px solid #c7d2fe;
        }
        .grades-table-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .grades-table-count { float: right; background-color: #c7d2fe; padding: 2px 8px; border-radius: 8px; font-size: 7px; color: #4338ca; }
        
        .grades-table { width: 100%; border-collapse: collapse; }
        
        .grades-table thead th {
            background-color: #e0f2fe;
            color: #0c4a6e;
            padding: 6px 3px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #7dd3fc;
            border-right: 1px solid #bae6fd;
        }
        .grades-table thead th:first-child {
            text-align: left;
            padding-left: 8px;
            width: 22%;
            background-color: #dbeafe;
        }
        .grades-table thead th.th-teacher {
            text-align: left;
            padding-left: 6px;
            width: 18%;
            background-color: #e0e7ff;
        }
        .grades-table thead th:last-child { border-right: none; }
        .grades-table thead th.th-def { background-color: #fef3c7; color: #78350f; border-left: 1.5px solid #fcd34d; border-right: 1.5px solid #fcd34d; }
        .grades-table thead th.th-absences { background-color: #fee2e2; color: #991b1b; }
        .grades-table thead th.th-behavior { background-color: #fce7f3; color: #9d174d; }
        .grades-table thead th.th-perf { background-color: #d1fae5; color: #065f46; }
        
        .grades-table tbody td {
            padding: 5px 3px;
            text-align: center;
            font-size: 8px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #f3f4f6;
        }
        .grades-table tbody td:first-child {
            text-align: left;
            padding-left: 8px;
            font-weight: 600;
            color: #374151;
            background-color: #f9fafb;
            border-right: 1px solid #e5e7eb;
            font-size: 7px;
        }
        .grades-table tbody td.td-teacher {
            text-align: left;
            padding-left: 6px;
            font-size: 6px;
            color: #6b7280;
            background-color: #fafafa;
            border-right: 1px solid #e5e7eb;
        }
        .grades-table tbody td:last-child { border-right: none; }
        .grades-table tbody tr:nth-child(even) td { background-color: #fafafa; }
        .grades-table tbody tr:nth-child(even) td:first-child { background-color: #f3f4f6; }
        
        /* Grade Badges - PASTEL */
        .grade-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            min-width: 26px;
        }
        .grade-superior { background-color: #ede9fe; color: #6d28d9; }
        .grade-alto { background-color: #dbeafe; color: #1d4ed8; }
        .grade-basico { background-color: #fef3c7; color: #b45309; }
        .grade-bajo { background-color: #fee2e2; color: #b91c1c; }
        
        /* Definitiva Cell */
        .td-def {
            background-color: #fef9c3 !important;
            border-left: 1.5px solid #fcd34d !important;
            border-right: 1.5px solid #fcd34d !important;
        }
        .def-score { font-size: 10px; font-weight: bold; }
        
        /* Absences Cell */
        .td-absences {
            background-color: #fef2f2 !important;
        }
        .absences-value { font-size: 8px; font-weight: bold; color: #991b1b; }
        .absences-zero { font-size: 8px; color: #9ca3af; }
        
        /* Behavior Cell */
        .td-behavior {
            background-color: #fdf2f8 !important;
        }
        .behavior-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .behavior-superior { background-color: #ede9fe; color: #6d28d9; }
        .behavior-alto { background-color: #dbeafe; color: #1d4ed8; }
        .behavior-basico { background-color: #fef3c7; color: #b45309; }
        .behavior-bajo { background-color: #fee2e2; color: #b91c1c; }
        
        /* Performance Badges - PASTEL */
        .perf-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .perf-superior { background-color: #ede9fe; color: #6d28d9; }
        .perf-alto { background-color: #dbeafe; color: #1d4ed8; }
        .perf-basico { background-color: #fef3c7; color: #b45309; }
        .perf-bajo { background-color: #fee2e2; color: #b91c1c; }
        
        /* Average Row */
        .row-average td {
            background-color: #e5e7eb !important;
            border-top: 1.5px solid #9ca3af !important;
            padding: 8px 3px !important;
            font-weight: bold;
        }
        .row-average td:first-child {
            font-size: 8px;
            text-transform: uppercase;
            background-color: #d1d5db !important;
        }
        .avg-score { font-size: 12px; font-weight: bold; color: #92400e; }
        
        /* Status Row */
        .row-status td {
            background-color: #374151 !important;
            color: white !important;
            padding: 8px 3px !important;
            text-align: center;
        }
        .row-status td:first-child {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 15px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        .status-passed { background-color: #22c55e; }
        .status-failed { background-color: #ef4444; }
        .status-postponed { background-color: #f59e0b; }
        
        .text-muted { color: #9ca3af; }
        
        /* ===================== SUMMARY SECTION - COMPACT ===================== */
        .summary-section { padding: 6px 12px; background-color: #f9fafb; }
        .summary-grid { width: 100%; border-collapse: collapse; }
        .summary-grid td { padding: 0 2px; }
        .summary-card {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            padding: 5px 3px;
            text-align: center;
            border-top: 2px solid;
        }
        .card-avg { border-top-color: #a5b4fc; }
        .card-level { border-top-color: #6ee7b7; }
        .card-rank { border-top-color: #fcd34d; }
        .card-behavior { border-top-color: #f9a8d4; }
        .card-absences { border-top-color: #9ca3af; }
        
        .card-value { font-size: 10px; font-weight: bold; }
        .val-avg { color: #4338ca; }
        .val-level { color: #047857; }
        .val-rank { color: #b45309; }
        .val-behavior { color: #be185d; }
        .val-absences { color: #4b5563; }
        .card-label { font-size: 5px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-top: 1px; }
        
        /* ===================== FOOTER ===================== */
        .footer-section { padding: 10px 12px; background-color: #f3f4f6; }
        
        .signatures-row { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .signature-col { width: 50%; text-align: center; padding: 4px 12px; }
        .signature-line { width: 160px; border-bottom: 1.5px solid #4b5563; margin: 0 auto 6px; height: 20px; }
        .signature-name { font-size: 9px; font-weight: bold; color: #1f2937; }
        .signature-role { font-size: 7px; color: #6b7280; margin-top: 1px; }
        
        .footer-bar {
            background-color: #e5e7eb;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            padding: 6px 12px;
        }
        .footer-bar-table { width: 100%; border-collapse: collapse; }
        .footer-brand { color: #374151; }
        .footer-system { font-size: 9px; font-weight: bold; }
        .footer-tagline { font-size: 6px; color: #6b7280; margin-top: 1px; }
        .footer-timestamp { text-align: right; color: #6b7280; font-size: 7px; }
    </style>
</head>
<body>
    <div class="main-container">
        
        <!-- HEADER -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <div class="logo-wrapper">
                            @if($settings['logo'])
                                <img src="{{ $settings['logo'] }}" alt="Logo">
                            @else
                                <div class="logo-placeholder">IE</div>
                            @endif
                        </div>
                    </td>
                    <td class="school-info-cell">
                        <div class="school-name">{{ $settings['name'] }}</div>
                        <div class="school-details">Sede: {{ $sede->name }} | Grado: {{ $grade->name }}</div>
                    </td>
                    <td class="year-badge-cell">
                        <div class="year-badge">
                            <div class="year-label">Año Lectivo</div>
                            <div class="year-value">{{ $schoolYear->name }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- TITLE -->
        <div class="title-bar">
            <div class="title-main">Boletin de Calificaciones</div>
            <div class="title-period">{{ $period->name }} - Informe de Rendimiento Academico</div>
        </div>
        
        <!-- STUDENT INFO -->
        <div class="student-info-section">
            <div class="student-card">
                <div class="student-card-header">Informacion del Estudiante</div>
                <div class="student-card-body">
                    <table class="student-info-table">
                        <tr>
                            <td class="cell-divider" style="width: 42%;">
                                <div class="info-label">Nombre Completo</div>
                                <div class="info-value">{{ $student->name }}</div>
                            </td>
                            <td class="cell-divider" style="width: 23%;">
                                <div class="info-label">Identificacion</div>
                                <div class="info-value">{{ $student->identification ?? 'N/A' }}</div>
                            </td>
                            <td style="width: 35%;">
                                <div class="info-label">Director de Grupo</div>
                                <div class="info-value">{{ $director?->name ?? 'Sin asignar' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- GRADES TABLE -->
        <div class="grades-section">
            <div class="grades-wrapper">
                <div class="grades-table-header">
                    <span class="grades-table-title">Calificaciones por Asignatura</span>
                    <span class="grades-table-count">{{ count($subjects) }} asignaturas</span>
                </div>
                
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>Asignatura</th>
                            <th class="th-teacher">Docente</th>
                            @foreach($periods as $p)
                                <th>P{{ $p->number }}</th>
                            @endforeach
                            <th class="th-def">DEF.</th>
                            <th class="th-absences">Fallas</th>
                            <th class="th-behavior">Comp.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $subject['name'] }}</td>
                                <td class="td-teacher">{{ $subject['teacher'] ?? '-' }}</td>
                                @foreach($periods as $p)
                                    @php
                                        $score = $subject['periods'][$p->number] ?? null;
                                        $gradeClass = 'grade-basico';
                                        if ($score !== null) {
                                            if ($score >= 4.6) $gradeClass = 'grade-superior';
                                            elseif ($score >= 4.0) $gradeClass = 'grade-alto';
                                            elseif ($score >= 3.0) $gradeClass = 'grade-basico';
                                            else $gradeClass = 'grade-bajo';
                                        }
                                    @endphp
                                    <td>
                                        @if($score !== null)
                                            <span class="grade-badge {{ $gradeClass }}">{{ number_format($score, 1) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                @php
                                    $defColor = '#b45309';
                                    if ($subject['final'] !== null) {
                                        if ($subject['final'] >= 4.6) $defColor = '#6d28d9';
                                        elseif ($subject['final'] >= 4.0) $defColor = '#1d4ed8';
                                        elseif ($subject['final'] >= 3.0) $defColor = '#b45309';
                                        else $defColor = '#b91c1c';
                                    }
                                    
                                    $perfClass = 'perf-basico';
                                    if ($subject['performance'] === 'SUPERIOR') $perfClass = 'perf-superior';
                                    elseif ($subject['performance'] === 'ALTO') $perfClass = 'perf-alto';
                                    elseif ($subject['performance'] === 'BAJO') $perfClass = 'perf-bajo';
                                @endphp
                                <td class="td-def">
                                    @if($subject['final'] !== null)
                                        <span class="def-score" style="color: {{ $defColor }};">{{ number_format($subject['final'], 1) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="td-absences">
                                    @php
                                        $subjectAbsences = $subject['absences'] ?? 0;
                                    @endphp
                                    @if($subjectAbsences > 0)
                                        <span class="absences-value">{{ $subjectAbsences }}</span>
                                    @else
                                        <span class="absences-zero">0</span>
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        
                        <!-- AVERAGE ROW -->
                        <tr class="row-average">
                            <td colspan="2">Promedio General</td>
                            @foreach($periods as $p)
                                <td><span class="text-muted">-</span></td>
                            @endforeach
                            @php
                                // Total de fallas del periodo
                                $totalAbsencesPeriod = array_sum($absences ?? []);
                            @endphp
                            <td class="td-def">
                                <span class="avg-score">{{ $generalAverage !== null ? number_format($generalAverage, 1) : '-' }}</span>
                            </td>
                            <td class="td-absences">
                                <span class="absences-value">{{ $totalAbsencesPeriod }}</span>
                            </td>
                            <td class="td-behavior">
                                @php
                                    $generalBehaviorClass = 'behavior-basico';
                                    if ($behavior === 'SUPERIOR') $generalBehaviorClass = 'behavior-superior';
                                    elseif ($behavior === 'ALTO') $generalBehaviorClass = 'behavior-alto';
                                    elseif ($behavior === 'BAJO') $generalBehaviorClass = 'behavior-bajo';
                                @endphp
                                <span class="behavior-badge {{ $generalBehaviorClass }}">{{ $behavior }}</span>
                            </td>
                        </tr>
                        
                        <!-- STATUS ROW -->
                        @if($allSubjectsComplete && $finalStatus)
                        <tr class="row-status">
                            <td colspan="2">Estado Final</td>
                            <td colspan="{{ count($periods) + 3 }}">
                                @if($finalStatus === 'APROBADO')
                                    <span class="status-badge status-passed">APROBADO</span>
                                @elseif($finalStatus === 'APLAZADO')
                                    <span class="status-badge status-postponed">APLAZADO</span>
                                    <span style="font-size: 7px; margin-left: 5px;">{{ $failedSubjects }} materia(s) para recuperar</span>
                                @else
                                    <span class="status-badge status-failed">REPROBADO</span>
                                    <span style="font-size: 7px; margin-left: 5px;">{{ $failedSubjects }} materias perdidas</span>
                                @endif
                            </td>
                        </tr>
                        @else
                        <tr class="row-status">
                            <td colspan="2">Estado Final</td>
                            <td colspan="{{ count($periods) + 3 }}">
                                <span style="color: #9ca3af;">Pendiente - Faltan notas por registrar</span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- SUMMARY -->
        <div class="summary-section">
            <table class="summary-grid">
                <tr>
                    <td style="width: 33.33%;">
                        <div class="summary-card card-avg">
                            <div class="card-value val-avg">{{ $generalAverage !== null ? number_format($generalAverage, 2) : '-' }}</div>
                            <div class="card-label">Promedio</div>
                        </div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="summary-card card-level">
                            <div class="card-value val-level">{{ $performanceLevel !== '-' ? $performanceLevel : '-' }}</div>
                            <div class="card-label">Desempeño</div>
                        </div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="summary-card card-rank">
                            <div class="card-value val-rank">{{ $position }} / {{ $totalStudents }}</div>
                            <div class="card-label">Puesto</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- FOOTER -->
        <div class="footer-section">
            <table class="signatures-row">
                <tr>
                    <td class="signature-col">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $director?->name ?? 'Director de Grupo' }}</div>
                        <div class="signature-role">Director(a) de Grupo</div>
                    </td>
                    <td class="signature-col">
                        <div class="signature-line"></div>
                        <div class="signature-name">Rector(a)</div>
                        <div class="signature-role">Rector(a) de la Institucion</div>
                    </td>
                </tr>
            </table>
            
            <div class="footer-bar">
                <table class="footer-bar-table">
                    <tr>
                        <td class="footer-brand">
                            <div class="footer-system">NOTAS 2.0</div>
                            <div class="footer-tagline">Sistema de Gestion Academica</div>
                        </td>
                        <td class="footer-timestamp">
                            Generado: {{ now()->format('d/m/Y H:i') }} | Documento oficial
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
    </div>
</body>
</html>
