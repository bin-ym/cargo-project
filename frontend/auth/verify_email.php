<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Cargo Connect</title>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .verify-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 20px;
        }
        .verify-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .otp-input {
            letter-spacing: 8px;
            font-size: 24px;
            text-align: center;
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            width: 100%;
            border: none;
            cursor: pointer;
        }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }
    </style>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<div class="verify-container">
    <div class="verify-card" id="verifyCard">
        <div class="icon-circle">
            <i data-feather="mail"></i>
        </div>
        <h2>Verify Your Email</h2>
        <p style="color: #64748b; margin-top: 10px;">We sent a 6-digit code to your email.</p>
        
        <form id="otpForm">
            <input type="text" id="otp" class="otp-input" placeholder="000000" maxlength="6" required pattern="\d{6}">
            <button type="submit" class="btn">Verify Account</button>
        </form>
        <div id="message" style="margin-top: 15px; font-size: 14px;"></div>
    </div>
</div>

<script>
    feather.replace();

    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email'); // We need email to verify against

    document.getElementById('otpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const otp = document.getElementById('otp').value;
        const btn = e.target.querySelector('button');
        const msg = document.getElementById('message');

        if (!email) {
            msg.style.color = 'red';
            msg.innerText = 'Email missing from URL. Please register again.';
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Verifying...';
        msg.innerText = '';

        try {
            const res = await fetch('/cargo-project/backend/api/auth/verify_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: otp, email: email }) // Send both token and email if needed, but backend currently just checks token. 
                // Wait, token might not be unique globally if it's just 6 digits. 
                // We SHOULD check email + token combination for safety.
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById('verifyCard').innerHTML = `
                    <div class="icon-circle" style="background: #dcfce7; color: #22c55e">
                        <i data-feather="check-circle"></i>
                    </div>
                    <h2>Verified!</h2>
                    <p style="color: #64748b; margin-top: 10px;">Your account is now active.</p>
                    <a href="login.php" class="btn" style="margin-top: 20px;">Go to Login</a>
                `;
                feather.replace();
            } else {
                msg.style.color = 'red';
                msg.innerText = data.error || 'Invalid code.';
                btn.disabled = false;
                btn.innerText = 'Verify Account';
            }
        } catch (err) {
            msg.style.color = 'red';
            msg.innerText = 'Server error.';
            btn.disabled = false;
            btn.innerText = 'Verify Account';
        }
    });
</script>

</body>
</html>
