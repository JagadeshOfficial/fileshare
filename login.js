document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = e.target.email.value;
    const password = e.target.password.value;

    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
            alert('Login successful!');
            localStorage.setItem('token', data.token);
            window.location.href = '/dashboard.html';
        } else {
            alert(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('An error occurred during login.');
    }
});
