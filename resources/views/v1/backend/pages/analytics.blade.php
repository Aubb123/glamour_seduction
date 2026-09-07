@extends('v1.backend.layouts.app')

@section('title') {{ __('Dashboard Analytics') }} @endsection

@section('content')

    <div class="d-lg-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">{{ __('Dashboard Analytics') }}</h2>
            <p class="mb-0">{{ __('Visualisez les statistiques de votre application selon la période choisie.') }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('backend.analytics') }}" method="GET" class="row g-3 align-items-end">

                {{-- Période prédéfinie --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <label class="form-label">{{ __('Période') }}</label>
                    <select name="period" id="period" class="form-select">
                        <option value="hour" {{ $period === 'hour' ? 'selected' : '' }}>{{ __('Heure') }}</option>
                        <option value="day" {{ $period === 'day' ? 'selected' : '' }}>{{ __('Jour') }}</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('Semaine') }}</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('Mois') }}</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('Année') }}</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>{{ __('Personnalisée') }}</option>
                    </select>
                </div>

                {{-- Date de début (personnalisée) --}}
                <div class="col-xl-3 col-md-4 col-sm-6 custom-date {{ $period !== 'custom' ? 'd-none' : '' }}" id="dateFromWrap">
                    <label class="form-label">{{ __('Date de début') }}</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
                </div>

                {{-- Date de fin (personnalisée) --}}
                <div class="col-xl-3 col-md-4 col-sm-6 custom-date {{ $period !== 'custom' ? 'd-none' : '' }}" id="dateToWrap">
                    <label class="form-label">{{ __('Date de fin') }}</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
                </div>

                <div class="col-xl-2 col-md-6 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter me-1"></i>{{ __('Filtrer') }}
                    </button>
                    <a href="{{ route('backend.analytics') }}" class="btn btn-light" title="{{ __('Réinitialiser') }}">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>

                <div class="col-xl-2 col-md-6 col-sm-12 text-xl-end">
                    <small class="text-muted">
                        <i class="bx bx-calendar me-1"></i>
                        @formatDate($start, 'D MMM YYYY') — @formatDate($end, 'D MMM YYYY')
                    </small>
                </div>

            </form>
        </div>
    </div>

    {{-- Cartes de totaux --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-primary p-3"><i class="bx bx-user fs-4 text-primary"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['users'] }}</h5>
                        <small class="text-muted">{{ __('Utilisateurs') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-success p-3"><i class="bx bx-send fs-4 text-success"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['subscribes'] }}</h5>
                        <small class="text-muted">{{ __('Abonnés') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-warning p-3"><i class="bx bx-mail-send fs-4 text-warning"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['contacts'] }}</h5>
                        <small class="text-muted">{{ __('Contacts') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-info p-3"><i class="bx bx-book fs-4 text-info"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['posts'] }}</h5>
                        <small class="text-muted">{{ __('Articles') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-danger p-3"><i class="bx bx-message-square fs-4 text-danger"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['comments'] }}</h5>
                        <small class="text-muted">{{ __('Commentaires') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-secondary p-3"><i class="bx bx-file fs-4 text-secondary"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['audits'] }}</h5>
                        <small class="text-muted">{{ __('Audits') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-primary p-3"><i class="bx bx-pulse fs-4 text-primary"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $totals['activityLogs'] }}</h5>
                        <small class="text-muted">{{ __('Logs d\'activité') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphes --}}
    <div class="row g-3">

        {{-- Courbe globale (toutes les métriques) --}}
        <div class="col-xxl-8 col-xl-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-0">{{ __('Évolution générale') }}</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 320px;">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Donut : répartition des totaux --}}
        <div class="col-xxl-4 col-xl-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">{{ __('Répartition') }}</h5></div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 320px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Utilisateurs --}}
        <div class="col-xl-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">{{ __('Inscriptions utilisateurs') }}</h5></div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Abonnés & Contacts --}}
        <div class="col-xl-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">{{ __('Abonnés & Contacts') }}</h5></div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="subsContactsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Articles & Commentaires --}}
        <div class="col-xl-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">{{ __('Articles & Commentaires') }}</h5></div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="postsCommentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audits & Logs --}}
        <div class="col-xl-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">{{ __('Audits & Logs d\'activité') }}</h5></div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="auditsLogsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('styles')
<style>
    .chart-wrapper {
        position: relative;
        width: 100%;
    }
    .chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset(getEnvFolder().'backend/assets/plugins/chartjs/chart.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ─── Afficher/masquer les dates personnalisées ─────────────
        const period = document.getElementById('period');
        const dateFromWrap = document.getElementById('dateFromWrap');
        const dateToWrap = document.getElementById('dateToWrap');

        period.addEventListener('change', function () {
            const show = this.value === 'custom';
            dateFromWrap.classList.toggle('d-none', !show);
            dateToWrap.classList.toggle('d-none', !show);
        });

        // ─── Données depuis le contrôleur ──────────────────────────
        const labels = @json($chartData['labels']);
        const data = {
            users: @json($chartData['users']),
            subscribes: @json($chartData['subscribes']),
            contacts: @json($chartData['contacts']),
            posts: @json($chartData['posts']),
            comments: @json($chartData['comments']),
            audits: @json($chartData['audits']),
            activityLogs: @json($chartData['activityLogs']),
        };

        const totals = @json($totals);

        const gridColor = 'rgba(160, 160, 160, 0.12)';
        const tickColor = '#8a8a8a';

        // Options communes aux courbes
        const lineOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor, maxTicksLimit: 12 } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor } },
            },
        };

        const lineDefaults = {
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 2,
            pointHoverRadius: 5,
        };

        // ─── 1. Évolution générale ─────────────────────────────────
        new Chart(document.getElementById('overviewChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: '{{ __("Utilisateurs") }}', data: data.users, borderColor: '#4d6cf6', backgroundColor: 'rgba(77,108,246,0.12)', ...lineDefaults },
                    { label: '{{ __("Abonnés") }}', data: data.subscribes, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.12)', ...lineDefaults },
                    { label: '{{ __("Contacts") }}', data: data.contacts, borderColor: '#f5a623', backgroundColor: 'rgba(245,166,35,0.12)', ...lineDefaults },
                    { label: '{{ __("Articles") }}', data: data.posts, borderColor: '#17a2b8', backgroundColor: 'rgba(23,162,184,0.12)', ...lineDefaults },
                    { label: '{{ __("Commentaires") }}', data: data.comments, borderColor: '#ef4b4b', backgroundColor: 'rgba(239,75,75,0.12)', ...lineDefaults },
                    { label: '{{ __("Audits") }}', data: data.audits, borderColor: '#6c757d', backgroundColor: 'rgba(108,117,125,0.12)', ...lineDefaults },
                    { label: '{{ __("Logs") }}', data: data.activityLogs, borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.12)', ...lineDefaults },
                ],
            },
            options: lineOptions,
        });

        // ─── 2. Répartition (donut) ────────────────────────────────
        new Chart(document.getElementById('distributionChart'), {
            type: 'doughnut',
            data: {
                labels: ['{{ __("Utilisateurs") }}', '{{ __("Abonnés") }}', '{{ __("Contacts") }}', '{{ __("Articles") }}', '{{ __("Commentaires") }}', '{{ __("Audits") }}', '{{ __("Logs") }}'],
                datasets: [{
                    data: [totals.users, totals.subscribes, totals.contacts, totals.posts, totals.comments, totals.audits, totals.activityLogs],
                    backgroundColor: ['#4d6cf6', '#22c55e', '#f5a623', '#17a2b8', '#ef4b4b', '#6c757d', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                },
            },
        });

        // ─── 3. Inscriptions utilisateurs ──────────────────────────
        new Chart(document.getElementById('usersChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: '{{ __("Utilisateurs") }}',
                    data: data.users,
                    backgroundColor: 'rgba(77,108,246,0.75)',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }],
            },
            options: lineOptions,
        });

        // ─── 4. Abonnés & Contacts ─────────────────────────────────
        new Chart(document.getElementById('subsContactsChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: '{{ __("Abonnés") }}', data: data.subscribes, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.12)', ...lineDefaults },
                    { label: '{{ __("Contacts") }}', data: data.contacts, borderColor: '#f5a623', backgroundColor: 'rgba(245,166,35,0.12)', ...lineDefaults },
                ],
            },
            options: lineOptions,
        });

        // ─── 5. Articles & Commentaires ────────────────────────────
        new Chart(document.getElementById('postsCommentsChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: '{{ __("Articles") }}', data: data.posts, borderColor: '#17a2b8', backgroundColor: 'rgba(23,162,184,0.12)', ...lineDefaults },
                    { label: '{{ __("Commentaires") }}', data: data.comments, borderColor: '#ef4b4b', backgroundColor: 'rgba(239,75,75,0.12)', ...lineDefaults },
                ],
            },
            options: lineOptions,
        });

        // ─── 6. Audits & Logs ──────────────────────────────────────
        new Chart(document.getElementById('auditsLogsChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: '{{ __("Audits") }}', data: data.audits, borderColor: '#6c757d', backgroundColor: 'rgba(108,117,125,0.12)', ...lineDefaults },
                    { label: '{{ __("Logs") }}', data: data.activityLogs, borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.12)', ...lineDefaults },
                ],
            },
            options: lineOptions,
        });

    });
</script>
@endpush