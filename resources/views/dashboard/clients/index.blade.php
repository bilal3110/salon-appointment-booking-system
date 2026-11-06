@extends('dashboard.layout.main')

@section('title', 'Clients')
@section('section-name', 'Operations')
@section('page-name', 'Clients')

@section('main-section')

    @php
        $clientTotal = count($clients);
        $returning = $clients->filter(function ($client) {
            return $client->bookings_count > 1;
        })->count();
        $lifetime = $clients->sum('spend');
    @endphp

    <div class="page-head">
        <div>
            <p class="eyebrow">Operations</p>
            <h1 class="page-title">Clients</h1>
            <p class="page-lede">
                Everyone who has sat in a chair, drawn from the appointment ledger — no separate register to keep.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.bookings') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                Appointment ledger
            </a>
        </div>
    </div>

    <div class="stack">

        <section class="band" aria-label="Client figures">
            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                    Total clients
                </p>
                <p class="band__value">{{ number_format($clientTotal) }}</p>
                <p class="band__note">Distinct in the ledger</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-user-check"></use></svg>
                    Returning
                </p>
                <p class="band__value">{{ number_format($returning) }}</p>
                <p class="band__note">More than one visit</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-banknote"></use></svg>
                    Completed value
                </p>
                <p class="band__value band__value--sm">
                    {{ number_format((float) $lifetime, 0) }}<span class="unit">PKR</span>
                </p>
                <p class="band__note">From completed work</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-percent"></use></svg>
                    Returning rate
                </p>
                <p class="band__value">
                    {{ $clientTotal > 0 ? round(($returning / $clientTotal) * 100) : 0 }}<span class="unit">%</span>
                </p>
                <p class="band__note">Of all clients</p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Client Register</h2>
                    <p class="panel__sub">Every client the house has served, most frequent first</p>
                </div>
            </div>

            @if ($clientTotal)
                <div class="ledger-wrap">
                    <table class="ledger">
                        <thead>
                            <tr>
                                <th scope="col">Client</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Email</th>
                                <th scope="col" class="cell-num">Visits</th>
                                <th scope="col" class="cell-num">Completed value</th>
                                <th scope="col" class="cell-num">Last visit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td class="row-mark">
                                        <div class="person">
                                            <span class="initials">{{ \Illuminate\Support\Str::of($client->customer_name)->substr(0, 1)->upper() }}</span>
                                            <span>
                                                <span class="person__name">{{ $client->customer_name }}</span>
                                                @if ($client->bookings_count > 1)
                                                    <span class="person__meta">Returning client</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $client->customer_contact ?: '—' }}</td>
                                    <td class="cell-ref">{{ $client->customer_email }}</td>
                                    <td class="cell-num">{{ $client->bookings_count }}</td>
                                    <td class="cell-num cell-strong">{{ number_format((float) $client->spend, 0) }} PKR</td>
                                    <td class="cell-num cell-ref">
                                        {{ \Illuminate\Support\Carbon::parse($client->last_visit)->format('j M Y') }}
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
                            <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                        </span>
                        <h3 class="empty__title">No clients on the books</h3>
                        <p class="empty__text">
                            The register fills itself as clients book appointments through the public site.
                        </p>
                    </div>
                </div>
            @endif
        </section>

    </div>

@endsection
