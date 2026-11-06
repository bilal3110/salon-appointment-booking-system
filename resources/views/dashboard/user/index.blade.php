@extends('dashboard.layout.main')

@section('title', 'Accounts')
@section('section-name', 'The House')
@section('page-name', 'Accounts')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Accounts</h1>
            <p class="page-lede">
                Every sign-in to the management office — proprietors and barbers alike.
            </p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.users.create') }}" class="btn btn--primary">
                <svg class="icon" aria-hidden="true"><use href="#i-user-plus"></use></svg>
                New account
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="toolbar">
            <div class="search">
                <svg class="icon" aria-hidden="true"><use href="#i-search"></use></svg>
                <input class="input" type="search" id="userSearch" placeholder="Name, email or contact…"
                    aria-label="Search accounts" autocomplete="off">
                <button type="button" class="search__clear" aria-label="Clear search">
                    <svg class="icon icon--xs" aria-hidden="true"><use href="#i-x"></use></svg>
                </button>
            </div>

            <select class="select filter-select" id="userRoleFilter" aria-label="Filter by role">
                <option value="">All roles</option>
                <option value="admin">Proprietor</option>
                <option value="staff">Barber</option>
            </select>

            <div class="toolbar__spacer"></div>
            <span class="toolbar__count" id="userCount">—</span>
        </div>

        <div class="ledger-wrap">
            <table class="ledger">
                <thead>
                    <tr>
                        <th scope="col">Ref</th>
                        <th scope="col">Account</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="cell-num">Joined</th>
                        <th scope="col"><span class="text-smoke">Actions</span></th>
                    </tr>
                </thead>
                <tbody id="usersRows">
                    <tr>
                        <td colspan="6">
                            <div class="loading-row">
                                <span class="spinner"></span>
                                Checking the register
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

@endsection
