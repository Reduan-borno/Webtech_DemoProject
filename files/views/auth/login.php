<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <h2>Welcome Back</h2>
            <p>Sign in to your GadgetGrid account</p>
        </div>
        
        <form class="auth-form" id="login-form" onsubmit="handleLogin(event)">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Enter your username or email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus on username field
    document.getElementById('username').focus();
});
</script>