@extends('frontend.layout.main')
@section('main-section')
    <style>
        .testimonial-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
            flex: 1;
        }

        header {
            text-align: center;
            margin-bottom: 60px;
        }

        h1 {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .testimonial-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .testimonial-form-section {
            background: var(--highlight);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 60px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .testimonial-form-section h2 {
            color: var(--secondary);
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .testimonial-form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            background: var(--primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .testimonial-rating-input {
            display: flex;
            gap: 10px;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .testimonial-rating-input input {
            display: none;
        }

        .testimonial-rating-input label {
            cursor: pointer;
            font-size: 2rem;
            color: var(--border);
            transition: color 0.2s;
        }

        .testimonial-rating-input input:checked~label,
        .testimonial-rating-input label:hover,
        .testimonial-rating-input label:hover~label {
            color: var(--accent);
        }

        .testimonial-btn {
            background: var(--accent);
            color: var(--text);
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .testimonial-btn:hover {
            background: var(--secondary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .testimonial-testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .testimonial-testimonial-card {
            background: var(--highlight);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .testimonial-testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(212, 197, 169, 0.1);
        }

        .testimonial-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }

        .testimonial-user-info h3 {
            color: var(--secondary);
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .testimonial-profession {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .testimonial-stars {
            color: var(--accent);
            font-size: 1.2rem;
        }

        .testimonial-review-text {
            color: var(--text);
            line-height: 1.8;
            font-size: 1rem;
        }

        .testimonial-empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .testimonial-empty-state p {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .testimonial-form-section {
                padding: 25px;
            }

            .testimonial-testimonials-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    </head>

    <body>
        <div class="testimonial-container">

            <a href="{{ route('frontend.index') }}" class="back-link">← Back to Home</a>

            <div class="header">
                <h1>Share Your Vibe</h1>
                <p class="subtitle">Share your experience with us</p>
            </div>

            <div class="testimonial-form-section">
                <h2>Submit Your Review</h2>

                <form id="testimonial-form">
                    <div class="testimonial-form-group">
                        <label for="testimonial-name">Your Name *</label>
                        <input type="text" id="testimonial-name" required placeholder="John Doe">
                    </div>

                    <div class="testimonial-form-group">
                        <label for="testimonial-profession">Profession *</label>
                        <input type="text" id="testimonial-profession" required placeholder="Software Engineer">
                    </div>

                    <div class="testimonial-form-group">
                        <label for="testimonial-review">Your Review *</label>
                        <textarea id="testimonial-review" required placeholder="Share your experience..."></textarea>
                    </div>

                    <div class="testimonial-form-group">
                        <label>Rating *</label>

                        <div class="testimonial-rating-input">
                            <input type="radio" name="testimonial-rating" id="testimonial-star5" value="5" required>
                            <label for="testimonial-star5">★</label>

                            <input type="radio" name="testimonial-rating" id="testimonial-star4" value="4">
                            <label for="testimonial-star4">★</label>

                            <input type="radio" name="testimonial-rating" id="testimonial-star3" value="3">
                            <label for="testimonial-star3">★</label>

                            <input type="radio" name="testimonial-rating" id="testimonial-star2" value="2">
                            <label for="testimonial-star2">★</label>

                            <input type="radio" name="testimonial-rating" id="testimonial-star1" value="1">
                            <label for="testimonial-star1">★</label>
                        </div>
                    </div>

                    <button type="submit" class="testimonial-btn">Submit Review</button>
                </form>
            </div>
        </div>
        <script>
            // Store API
            async function submitTestimonial(testimonialData) {
                try {
                    const response = await fetch('{{ route('testimonials.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(testimonialData)
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    return await response.json();
                } catch (error) {
                    console.error('Error submitting testimonial:', error);
                    throw error;
                }
            }
            // Handle Form Submission
            document.getElementById('testimonial-form').addEventListener('submit', async function (e) {
                e.preventDefault();
                const name = document.getElementById('testimonial-name').value.trim();
                const profession = document.getElementById('testimonial-profession').value.trim();
                const review = document.getElementById('testimonial-review').value.trim();
                const rating = document.querySelector('input[name="testimonial-rating"]:checked').value;
                const testimonialData = {
                    name: name,
                    user_profession: profession,
                    message: review,
                    rating: parseInt(rating)
                };
                try {
                    await submitTestimonial(testimonialData);
                    alert('Thank you for your review!');
                    document.getElementById('testimonial-form').reset();
                } catch (error) {
                    alert('There was an error submitting your review. Please try again later.');
                }
            });
        </script>
    @endsection
