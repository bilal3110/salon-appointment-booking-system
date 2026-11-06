@extends('dashboard.layout.main')

@section('title', 'Reports')
@section('section-name', 'Standing')
@section('page-name', 'Reports')

@section('main-section')

    @php
        $maxTotal = max(1, collect($dailySeries)->max('total'));
        $statusTotal = max(1, collect($statusBreakdown)->sum('count'));
        $chartW = 900;
        $chartH = 220;
        $padX = 8;
        $padT = 22;
        $padB = 30;
        $innerW = $chartW - $padX * 2;
        $innerH = $chartH - $padT - $padB;
        $slots = max(1, count($dailySeries));
        $slot = $innerW / $slots;
        $barW = min(14, $slot * 0.52);
        $maxServiceBookings = max(1, collect($servicePerformance)->max('bookings_count'));
    @endphp

    <div class="page-head">
        <div>
            <p class="eyebrow">Standing</p>
            <h1 class="page-title">Reports</h1>
            <p class="page-lede">
                What the establishment's own ledger says about the last thirty days. Every figure here is counted
                from real appointments.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.bookings') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                Open ledger
            </a>
        </div>
    </div>

    <div class="stack">

        <section class="band" aria-label="Headline figures">
            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                    Appointments
                </p>
                <p class="band__value">{{ number_format($totals['appointments']) }}</p>
                <p class="band__note">All time</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-circle-check"></use></svg>
                    Completed
                </p>
                <p class="band__value">{{ number_format($totals['completed']) }}</p>
                <p class="band__note">Services delivered</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                    Clients
                </p>
                <p class="band__value">{{ number_format($totals['clients']) }}</p>
                <p class="band__note">Distinct customers</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-banknote"></use></svg>
                    Revenue
                </p>
                <p class="band__value band__value--sm">
                    {{ number_format($totals['revenue'], 0) }}<span class="unit">PKR</span>
                </p>
                <p class="band__note">From completed work</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-ticket"></use></svg>
                    Average ticket
                </p>
                <p class="band__value band__value--sm">
                    {{ number_format($totals['average_ticket'], 0) }}<span class="unit">PKR</span>
                </p>
                <p class="band__note">Per completed appointment</p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Appointments, Last 30 Days</h2>
                    <p class="panel__sub">Bookings per day, counted from the ledger</p>
                </div>
                <div class="panel__actions">
                    <span class="toolbar__count">Peak {{ $maxTotal }} in a day</span>
                </div>
            </div>

            <div class="panel__body">
                <svg class="chart" viewBox="0 0 {{ $chartW }} {{ $chartH }}" role="img"
                    aria-label="Bar chart of appointments booked per day over the last thirty days">
                    <line class="chart__axis" x1="{{ $padX }}" y1="{{ $padT + $innerH }}"
                        x2="{{ $chartW - $padX }}" y2="{{ $padT + $innerH }}" />

                    @foreach ($dailySeries as $index => $day)
                        @php
                            $barHeight = $day['total'] > 0 ? max(3, round(($day['total'] / $maxTotal) * $innerH)) : 0;
                            $barX = $padX + ($index * $slot) + (($slot - $barW) / 2);
                            $barY = $padT + $innerH - $barHeight;
                            $centreX = $padX + ($index * $slot) + ($slot / 2);
                        @endphp

                        @if ($barHeight > 0)
                            <rect class="chart__bar {{ $day['total'] === $maxTotal ? 'chart__bar--peak' : '' }}"
                                x="{{ $barX }}" y="{{ $barY }}" width="{{ $barW }}" height="{{ $barHeight }}" rx="1">
                                <title>{{ $day['label'] }} — {{ $day['total'] }} appointment{{ $day['total'] === 1 ? '' : 's' }}</title>
                            </rect>
                        @endif
                    @endforeach

                    @foreach ($dailySeries as $index => $day)
                        @if ($index % 3 === 0)
                            @php $centreX = $padX + ($index * $slot) + ($slot / 2); @endphp
                            <text class="chart__tick" x="{{ $centreX }}" y="{{ $chartH - 9 }}" text-anchor="middle">
                                {{ $day['label'] }}
                            </text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </section>

        <div class="grid-2">

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Service Performance</h2>
                        <p class="panel__sub">Ranked by completed value</p>
                    </div>
                </div>

                @if (count($servicePerformance))
                    <div class="ledger-wrap">
                        <table class="ledger">
                            <thead>
                                <tr>
                                    <th scope="col">Service</th>
                                    <th scope="col">Type</th>
                                    <th scope="col" class="cell-num">Bookings</th>
                                    <th scope="col" class="cell-num">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($servicePerformance as $service)
                                    <tr>
                                        <td class="row-mark cell-strong">{{ $service->service_name }}</td>
                                        <td><span class="seal seal--plain">{{ ucfirst($service->service_type) }}</span></td>
                                        <td class="cell-num">{{ $service->bookings_count }}</td>
                                        <td class="cell-num cell-strong">{{ number_format((float) $service->revenue, 0) }} PKR</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="panel__body panel__body--flush">
                        <div class="empty">
                            <span class="empty__mark">
                                <svg class="icon" aria-hidden="true"><use href="#i-scissors"></use></svg>
                            </span>
                            <h3 class="empty__title">Nothing to report yet</h3>
                            <p class="empty__text">
                                Service performance appears once appointments have been booked against services.
                            </p>
                        </div>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Status Breakdown</h2>
                        <p class="panel__sub">Where every appointment stands</p>
                    </div>
                </div>

                <div class="panel__body">
                    <div class="meter-list">
                        @foreach ($statusBreakdown as $status)
                            <div class="meter__row">
                                <div class="meter__head">
                                    <span class="meter__name">
                                        <span class="meter__dot" style="background: {{ $status['color'] }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                    <span class="meter__value">{{ $status['count'] }}</span>
                                </div>
                                <div class="meter__track">
                                    <div class="meter__fill"
                                        style="width: {{ round(($status['count'] / $statusTotal) * 100, 1) }}%; background: {{ $status['color'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="rule rule--gold" style="margin: 22px 0 18px">

                    <h3 class="upper-label" style="margin-bottom: 12px">Barber performance</h3>

                    @if (count($staffPerformance))
                        <div class="ledger-wrap">
                            <table class="ledger">
                                <thead>
                                    <tr>
                                        <th scope="col">Barber</th>
                                        <th scope="col" class="cell-num">Bookings</th>
                                        <th scope="col" class="cell-num">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staffPerformance as $member)
                                        <tr>
                                            <td class="row-mark cell-strong">{{ $member->name }}</td>
                                            <td class="cell-num">{{ $member->bookings_count }}</td>
                                            <td class="cell-num cell-strong">{{ number_format((float) $member->revenue, 0) }} PKR</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="panel__sub">No barber has taken an appointment yet.</p>
                    @endif
                </div>
            </section>

        </div>

    </div>

@endsection
