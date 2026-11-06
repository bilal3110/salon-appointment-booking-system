@extends('dashboard.layout.main')

@section('title', 'Reviews')
@section('section-name', 'Standing')
@section('page-name', 'Reviews')

@section('main-section')

    @php
        $total = count($testimonials);
    @endphp

    <div class="page-head">
        <div>
            <p class="eyebrow">Standing</p>
            <h1 class="page-title">Reviews</h1>
            <p class="page-lede">
                What clients have said about the house, as published on the public site.
            </p>
        </div>
    </div>

    <div class="stack">

        <section class="band" aria-label="Review figures">
            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-message-square-quote"></use></svg>
                    Published reviews
                </p>
                <p class="band__value">{{ number_format($total) }}</p>
                <p class="band__note">On the public site</p>
            </div>

            <div class="band__cell">
                <p class="band__label">
                    <svg class="icon" aria-hidden="true"><use href="#i-star"></use></svg>
                    Average rating
                </p>
                <p class="band__value band__value--sm">
                    {{ $total ? number_format((float) $averageRating, 1) : '—' }}<span class="unit">/ 5</span>
                </p>
                <p class="band__note">Across every review</p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Published Reviews</h2>
                    <p class="panel__sub">Newest first</p>
                </div>
            </div>

            @if ($total)
                <div class="ledger-wrap">
                    <table class="ledger">
                        <thead>
                            <tr>
                                <th scope="col">Client</th>
                                <th scope="col">Profession</th>
                                <th scope="col">Review</th>
                                <th scope="col">Rating</th>
                                <th scope="col"><span class="text-smoke">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($testimonials as $testimonial)
                                @php $rating = (int) $testimonial->rating; @endphp
                                <tr>
                                    <td class="row-mark cell-strong">{{ $testimonial->name }}</td>
                                    <td class="cell-ref">{{ $testimonial->user_profession ?: '—' }}</td>
                                    <td>
                                        <span style="color:var(--ivory-dim);font-size:13.5px;line-height:1.6">
                                            {{ \Illuminate\Support\Str::limit($testimonial->message, 150) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="stars" role="img"
                                            aria-label="{{ $rating }} out of 5 stars">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <svg class="icon {{ $star <= $rating ? '' : 'icon--off' }}"
                                                    aria-hidden="true"><use href="#i-star"></use></svg>
                                            @endfor
                                        </span>
                                    </td>
                                    <td>
                                        <div class="ledger__actions">
                                            <button type="button" class="icon-btn icon-btn--danger"
                                                data-delete-testimonial="{{ $testimonial->id }}"
                                                title="Remove review" aria-label="Remove review by {{ $testimonial->name }}">
                                                <svg class="icon" aria-hidden="true"><use href="#i-trash-2"></use></svg>
                                            </button>
                                        </div>
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
                            <svg class="icon" aria-hidden="true"><use href="#i-message-square-quote"></use></svg>
                        </span>
                        <h3 class="empty__title">No reviews yet</h3>
                        <p class="empty__text">
                            When clients leave a word on the public site, it will appear here for approval.
                        </p>
                    </div>
                </div>
            @endif
        </section>

    </div>

@endsection
