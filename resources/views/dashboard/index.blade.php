@extends('dashboard.layout.main')

@section('title', 'Dashboard')
@section('section-name', 'Operations')
@section('page-name', 'Dashboard')

@section('main-section')

    @php
        $isAdmin = Auth::guard()->user()->type === 'admin';
        $maxTotal = max(1, collect($dailySeries)->max('total'));
        $chartW = 720;
        $chartH = 196;
        $padX = 6;
        $padT = 20;
        $padB = 28;
        $innerW = $chartW - $padX * 2;
        $innerH = $chartH - $padT - $padB;
        $slots = max(1, count($dailySeries));
        $slot = $innerW / $slots;
        $barW = min(24, $slot * 0.5);
        $statusTotal = max(1, collect($statusBreakdown)->sum('count'));
    @endphp

    <div class="page-head">
        <div>
            <p class="eyebrow">{{ now()->format('l, j F Y') }}</p>
            <h1 class="page-title">{{ $isAdmin ? 'The House Ledger' : 'My Appointments' }}</h1>
            <p class="page-lede">
                @if ($isAdmin)
                    Every chair, every client, every cut — the establishment's working record, kept in one place.
                @else
                    Your chair, your clients, your day — every appointment assigned to you.
                @endif
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.bookings') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                All appointments
            </a>
            @if ($isAdmin)
                <a href="{{ route('dashboard.reports') }}" class="btn btn--primary">
                    <svg class="icon" aria-hidden="true"><use href="#i-chart-column"></use></svg>
                    Reports
                </a>
            @endif
        </div>
    </div>

    <div class="stack">

        {{-- Key figures ---------------------------------------------------------- --}}
        <section class="band" aria-label="Key figures">
            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                    Total appointments
                </p>
                <p class="band__value">{{ number_format($totalAppointments) }}</p>
                <p class="band__note">All time</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-clock"></use></svg>
                    Today
                </p>
                <p class="band__value">{{ number_format($todayCount) }}</p>
                <p class="band__note">
                    {{ $todayCount === 1 ? 'Appointment' : 'Appointments' }} on the books
                </p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-hourglass"></use></svg>
                    Pending
                </p>
                <p class="band__value">{{ number_format($pendingBookingsCount) }}</p>
                <p class="band__note">Awaiting confirmation</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-circle-check"></use></svg>
                    Completed
                </p>
                <p class="band__value">{{ number_format($completedCount) }}</p>
                <p class="band__note">Services delivered</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                    Clients
                </p>
                <p class="band__value">{{ number_format($clientsCount) }}</p>
                <p class="band__note">Distinct customers</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-banknote"></use></svg>
                    Revenue
                </p>
                <p class="band__value band__value--sm">
                    {{ number_format($revenue, 0) }}<span class="unit">PKR</span>
                </p>
                <p class="band__note">From completed work</p>
            </div>
        </section>

        {{-- Today and the confirmation queue ------------------------------------- --}}
        <div class="grid-2">

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Today's Appointments</h2>
                        <p class="panel__sub">
                            {{ $todayCount === 0 ? 'Nothing booked for today' : $todayCount . ' booked for ' . now()->format('j F') }}
                        </p>
                    </div>
                    <div class="panel__actions">
                        <a href="{{ route('dashboard.bookings') }}" class="btn btn--quiet btn--sm">
                            Open ledger
                            <svg class="icon" aria-hidden="true"><use href="#i-chevron-right"></use></svg>
                        </a>
                    </div>
                </div>

                @if (count($todayBookings))
                    <div class="panel__body">
                        <ul class="timeline">
                            @foreach ($todayBookings as $booking)
                                <li class="timeline__item">
                                    <span class="timeline__time">
                                        {{ $booking->booking_time }}
                                        <small>{{ $booking->service->service_type ?? 'Service' }}</small>
                                    </span>

                                    <span class="timeline__main">
                                        <p class="timeline__name">{{ $booking->customer_name }}</p>
                                        <p class="timeline__meta">
                                            {{ $booking->service->service_name ?? 'No service' }}
                                            &middot;
                                            {{ $booking->staff->user->name ?? 'Unassigned' }}
                                        </p>
                                    </span>

                                    <span class="timeline__side">
                                        <span class="timeline__amount">
                                            {{ number_format((float) ($booking->service->price ?? 0), 0) }} PKR
                                        </span>
                                        <span class="seal seal--{{ $booking->status }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="panel__body panel__body--flush">
                        <div class="empty">
                            <span class="empty__mark">
                                <svg class="icon" aria-hidden="true"><use href="#i-calendar"></use></svg>
                            </span>
                            <h3 class="empty__title">The chairs stand empty</h3>
                            <p class="empty__text">
                                No appointments are booked for today. New bookings will appear here the moment
                                a client makes them.
                            </p>
                        </div>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Awaiting Confirmation</h2>
                        <p class="panel__sub">Pending requests needing a decision</p>
                    </div>
                    <div class="panel__actions">
                        <span class="seal seal--pending">{{ $pendingBookingsCount }}</span>
                    </div>
                </div>

                @if (count($pendingQueue))
                    <div class="panel__body">
                        <ul class="timeline">
                            @foreach ($pendingQueue as $booking)
                                <li class="timeline__item">
                                    <span class="timeline__time">
                                        {{ \Illuminate\Support\Carbon::parse($booking->booking_date)->format('j M') }}
                                        <small>{{ $booking->booking_time }}</small>
                                    </span>

                                    <span class="timeline__main">
                                        <p class="timeline__name">{{ $booking->customer_name }}</p>
                                        <p class="timeline__meta">
                                            {{ $booking->service->service_name ?? 'No service' }}
                                            &middot;
                                            {{ $booking->staff->user->name ?? 'Unassigned' }}
                                        </p>
                                    </span>

                                    <span class="timeline__side">
                                        <button type="button" class="icon-btn icon-btn--gold"
                                            data-action="confirm-booking" data-id="{{ $booking->id }}"
                                            title="Confirm this appointment" aria-label="Confirm appointment for {{ $booking->customer_name }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg>
                                        </button>
                                        <button type="button" class="icon-btn icon-btn--danger"
                                            data-action="cancel-booking" data-id="{{ $booking->id }}"
                                            title="Cancel this appointment" aria-label="Cancel appointment for {{ $booking->customer_name }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
                                        </button>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="panel__body panel__body--flush">
                        <div class="empty">
                            <span class="empty__mark">
                                <svg class="icon" aria-hidden="true"><use href="#i-circle-check"></use></svg>
                            </span>
                            <h3 class="empty__title">Nothing outstanding</h3>
                            <p class="empty__text">
                                Every request has been answered. There are no pending bookings waiting on a decision.
                            </p>
                        </div>
                    </div>
                @endif
            </section>

        </div>

        {{-- Activity and status ---------------------------------------------------- --}}
        <div class="grid-2">

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Appointments, Last 14 Days</h2>
                        <p class="panel__sub">Bookings per day, counted from the ledger</p>
                    </div>
                    <div class="panel__actions">
                        <span class="toolbar__count">Peak {{ $maxTotal }} per day</span>
                    </div>
                </div>

                <div class="panel__body">
                    <svg class="chart" viewBox="0 0 {{ $chartW }} {{ $chartH }}" role="img"
                        aria-label="Bar chart of appointments booked per day over the last fourteen days">
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
                                <text class="chart__value" x="{{ $centreX }}" y="{{ $barY - 6 }}" text-anchor="middle">
                                    {{ $day['total'] }}
                                </text>
                            @endif
                        @endforeach

                        @foreach ($dailySeries as $index => $day)
                            @if ($index % 2 === 0)
                                @php $centreX = $padX + ($index * $slot) + ($slot / 2); @endphp
                                <text class="chart__tick" x="{{ $centreX }}" y="{{ $chartH - 8 }}" text-anchor="middle">
                                    {{ $day['label'] }}
                                </text>
                            @endif
                        @endforeach
                    </svg>
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Status Ledger</h2>
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

                    <div class="row-between">
                        <span class="upper-label">Confirmed value</span>
                        <span class="display-figure" style="font-size: 22px">
                            {{ number_format($revenue, 0) }} <span class="unit text-smoke" style="font-size: 12px">PKR</span>
                        </span>
                    </div>
                    <p class="panel__sub" style="margin-top: 6px">
                        Earned from {{ $completedCount }} completed appointment{{ $completedCount === 1 ? '' : 's' }}.
                        Pending and cancelled work is not counted.
                    </p>
                </div>
            </section>

        </div>

        {{-- Service ledger ---------------------------------------------------------- --}}
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Service Ledger</h2>
                    <p class="panel__sub">The services the house actually performs</p>
                </div>
                <div class="panel__actions">
                    <a href="{{ route('dashboard.services') }}" class="btn btn--quiet btn--sm">
                        Manage services
                        <svg class="icon" aria-hidden="true"><use href="#i-chevron-right"></use></svg>
                    </a>
                </div>
            </div>

            @if (count($topServices))
                <div class="ledger-wrap">
                    <table class="ledger">
                        <thead>
                            <tr>
                                <th scope="col">Service</th>
                                <th scope="col">Type</th>
                                <th scope="col" class="cell-num">List price</th>
                                <th scope="col" class="cell-num">Appointments</th>
                                <th scope="col" class="cell-num">Completed value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topServices as $service)
                                <tr>
                                    <td class="cell-strong">{{ $service->service_name }}</td>
                                    <td>
                                        <span class="seal seal--plain">{{ ucfirst($service->service_type) }}</span>
                                    </td>
                                    <td class="cell-num">{{ number_format((float) $service->price, 0) }} PKR</td>
                                    <td class="cell-num">{{ $service->bookings_count }}</td>
                                    <td class="cell-num cell-strong">
                                        {{ number_format((float) $service->revenue, 0) }} PKR
                                    </td>
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
                        <h3 class="empty__title">No services recorded yet</h3>
                        <p class="empty__text">
                            Once services are added and appointments are booked, the house's performance appears here.
                        </p>
                        <div class="empty__action">
                            <a href="{{ route('dashboard.services.create') }}" class="btn btn--primary">
                                <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                                Add the first service
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </section>

    </div>

@endsection
