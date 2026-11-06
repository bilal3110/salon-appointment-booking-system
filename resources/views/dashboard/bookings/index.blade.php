@extends('dashboard.layout.main')

@section('title', 'Appointments')
@section('section-name', 'Operations')
@section('page-name', 'Appointments')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The Ledger</p>
            <h1 class="page-title">Appointments</h1>
            <p class="page-lede">
                Every booking taken through the house. Search the day, work the queue, keep the chairs turning.
            </p>
        </div>

        <div class="page-head__actions">
            <button type="button" class="btn btn--primary" data-new-booking>
                <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                New appointment
            </button>
        </div>
    </div>

    <section class="panel">
        <div class="toolbar">
            <div class="search">
                <svg class="icon" aria-hidden="true"><use href="#i-search"></use></svg>
                <input class="input" type="search" id="bookingSearch" placeholder="Client, service or barber…"
                    aria-label="Search appointments" autocomplete="off">
                <button type="button" class="search__clear" aria-label="Clear search">
                    <svg class="icon icon--xs" aria-hidden="true"><use href="#i-x"></use></svg>
                </button>
            </div>

            <select class="select filter-select" id="bookingStatus" aria-label="Filter by status">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="canceled">Cancelled</option>
            </select>

            <select class="select filter-select" id="bookingStaff" aria-label="Filter by barber">
                <option value="">All barbers</option>
            </select>

            <input class="input filter-date" type="date" id="bookingFrom" aria-label="From date" title="From date">
            <input class="input filter-date" type="date" id="bookingTo" aria-label="To date" title="To date">

            <button type="button" class="btn btn--quiet btn--sm" id="bookingClear">
                <svg class="icon" aria-hidden="true"><use href="#i-refresh-cw"></use></svg>
                Reset
            </button>

            <div class="toolbar__spacer"></div>
            <span class="toolbar__count" id="bookingCount">—</span>
        </div>

        <div class="ledger-wrap">
            <table class="ledger">
                <thead>
                    <tr>
                        <th scope="col">Ref</th>
                        <th scope="col">Client</th>
                        <th scope="col">Service</th>
                        <th scope="col">Barber</th>
                        <th scope="col" class="cell-num">Date &amp; time</th>
                        <th scope="col" class="cell-num">Value</th>
                        <th scope="col">Status</th>
                        <th scope="col"><span class="text-smoke">Actions</span></th>
                    </tr>
                </thead>
                <tbody id="bookingRows">
                    <tr>
                        <td colspan="8">
                            <div class="loading-row">
                                <span class="spinner"></span>
                                Opening the ledger
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="panel__foot">
            <div class="pager">
                <span class="pager__info" id="bookingPagerInfo">—</span>
                <div class="pager__size">
                    <label for="bookingPageSize">Rows</label>
                    <select class="select" id="bookingPageSize">
                        <option value="12">12</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            <div class="pager__controls" id="bookingPagerControls"></div>
        </div>
    </section>

@endsection
