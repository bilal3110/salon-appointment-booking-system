<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garison Dashboard - Login</title>
    <link rel="shortcut icon" href="{{ asset('Frontend/assets/icon.png') }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #d4c5a9;
            --accent: #8b7355;
            --highlight: #141414;
            --text: #d4c5a9;
            --text-muted: #a89168;
            --border: #2a2520;
            --footer-bg: #000;
            --copyright: #6b5d4f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--highlight);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .logo-section {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .logo {
            width: 150px;
        }

        .logo img {
            width: 100%;
            display: block;
        }

        .logo-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            letter-spacing: 1px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 35px;
        }

        .welcome-text h2 {
            color: var(--secondary);
            font-size: 24px;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: var(--text);
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: var(--primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: var(--accent);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: var(--text);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .login-btn:hover {
            background: var(--secondary);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 197, 169, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            padding: 0 15px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--secondary);
        }

        footer {
            background: var(--footer-bg);
            color: var(--copyright);
            text-align: center;
            padding: 20px;
            font-size: 13px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 25px;
            }

            .welcome-text h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ asset('Frontend/assets/logo.png') }}" alt="The Garison Logo">
                </div>
                <div class="logo-subtitle">DASHBOARD</div>
            </div>

            <div class="welcome-text">
                <h2>Welcome Back</h2>
                <p>Sign in to access your dashboard</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required
                        minlength="8">
                </div>

                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; EST. 1919 — BY ORDER OF THE PEAKY BLINDERS</p>
    </footer>

    <script>
        // LOGIN
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                alert('Please fill in all fields');
                return;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        email,
                        password
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    console.log('Login successful!');
                    window.location.href = '/admin-panel';
                } else {
                    alert(data.message || 'Invalid credentials');
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    </script>

</body>

</html>
