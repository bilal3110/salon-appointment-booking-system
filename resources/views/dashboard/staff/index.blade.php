@extends('dashboard.layout.main')

@section('title', 'Staff')
@section('section-name', 'The House')
@section('page-name', 'Staff')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Staff</h1>
            <p class="page-lede">
                The barbers on the floor, the services they hold, and the work on their chairs.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.staff.create') }}" class="btn btn--primary">
                <svg class="icon" aria-hidden="true"><use href="#i-user-plus"></use></svg>
                Add barber
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="toolbar">
            <div class="search">
                <svg class="icon" aria-hidden="true"><use href="#i-search"></use></svg>
                <input class="input" type="search" id="staffSearch" placeholder="Name, email or contact…"
                    aria-label="Search staff" autocomplete="off">
                <button type="button" class="search__clear" aria-label="Clear search">
                    <svg class="icon icon--xs" aria-hidden="true"><use href="#i-x"></use></svg>
                </button>
            </div>

            <div class="toolbar__spacer"></div>
            <span class="toolbar__count" id="staffCount">—</span>
        </div>

        <div class="ledger-wrap">
            <table class="ledger">
                <thead>
                    <tr>
                        <th scope="col">Ref</th>
                        <th scope="col">Barber</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Services</th>
                        <th scope="col" class="cell-num">Bookings</th>
                        <th scope="col"><span class="text-smoke">Actions</span></th>
                    </tr>
                </thead>
                <tbody id="staffRows">
                    <tr>
                        <td colspan="6">
                            <div class="loading-row">
                                <span class="spinner"></span>
                                Calling the floor
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

@endsection
