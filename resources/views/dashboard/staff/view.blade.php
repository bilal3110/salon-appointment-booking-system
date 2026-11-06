@extends('dashboard.layout.main')

@section('title', 'Barber Record')
@section('section-name', 'The House')
@section('page-name', 'Barber Record')

@section('main-section')

    <div class="page-head">
        <div>
            <p class="eyebrow">The House</p>
            <h1 class="page-title">Barber Record</h1>
            <p class="page-lede">Record #{{ $id }} — who holds this chair, and what they are trusted with.</p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('dashboard.staff') }}" class="btn btn--ghost">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to staff
            </a>
            <a href="{{ route('dashboard.staff.edit', $id) }}" class="btn btn--primary">
                <svg class="icon" aria-hidden="true"><use href="#i-pencil"></use></svg>
                Edit record
            </a>
        </div>
    </div>

    <div id="staffDetail" data-staff-id="{{ $id }}">
        <div class="loading-row">
            <span class="spinner"></span>
            Opening the record
        </div>
    </div>

@endsection
