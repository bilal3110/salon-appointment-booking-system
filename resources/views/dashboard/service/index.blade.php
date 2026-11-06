@extends('dashboard.layout.main')

@section('title', 'Services')
@section('section-name', 'The House')
@section('page-name', 'Services')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Services</h1>
            <p class="page-lede">
                The treatments the establishment performs, what they cost, and which barbers can deliver them.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.services.create') }}" class="btn btn--primary">
                <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                Add service
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="toolbar">
            <div class="search">
                <svg class="icon" aria-hidden="true"><use href="#i-search"></use></svg>
                <input class="input" type="search" id="serviceSearch" placeholder="Search services…"
                    aria-label="Search services" autocomplete="off">
                <button type="button" class="search__clear" aria-label="Clear search">
                    <svg class="icon icon--xs" aria-hidden="true"><use href="#i-x"></use></svg>
                </button>
            </div>

            <select class="select filter-select" id="serviceTypeFilter" aria-label="Filter by type">
                <option value="">All types</option>
                <option value="men">Men</option>
                <option value="women">Women</option>
            </select>

            <div class="toolbar__spacer"></div>
            <span class="toolbar__count" id="serviceCount">—</span>
        </div>

        <div class="ledger-wrap">
            <table class="ledger">
                <thead>
                    <tr>
                        <th scope="col">Ref</th>
                        <th scope="col">Service</th>
                        <th scope="col">Type</th>
                        <th scope="col" class="cell-num">Price</th>
                        <th scope="col" class="cell-num">Barbers</th>
                        <th scope="col" class="cell-num">Bookings</th>
                        <th scope="col"><span class="text-smoke">Actions</span></th>
                    </tr>
                </thead>
                <tbody id="servicesRows">
                    <tr>
                        <td colspan="7">
                            <div class="loading-row">
                                <span class="spinner"></span>
                                Reading the service book
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

@endsection
