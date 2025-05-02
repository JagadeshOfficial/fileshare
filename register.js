document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
  
    const name = e.target.name.value;
    const email = e.target.email.value;
    const password = e.target.password.value;
    const confirmPassword = e.target.confirm_password.value;
  
    // Validate that password and confirm password match
    if (password !== confirmPassword) {
      alert('Passwords do not match.');
      return;
    }
  
    try {
      const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name, email, password })
      });
  
      const data = await response.json();
  
      if (response.ok) {
        alert('Registration successful!');
        window.location.href = '/login.html';  // Redirect to login page
      } else {
        alert(data.message || 'Registration failed');
      }
    } catch (error) {
      console.error('Registration error:', error);
      alert('An error occurred during registration.');
    }
  });
  