<div class="auth-container">
    <div class="auth-card" style="max-width: 550px;">
        <div class="auth-header">
            <div class="logo-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <h2>Create Account</h2>
            <p>Join GadgetGrid today</p>
        </div>
        
        <form class="auth-form" id="register-form" onsubmit="handleRegister(event)">
            <!-- Role Selection -->
            <div class="form-group">
                <label>Register as:</label>
                <div class="role-selector">
                    <label class="role-option active" id="role-customer">
                        <input type="radio" name="role" value="customer" checked>
                        <i class="fas fa-user"></i>
                        <span>Customer</span>
                    </label>
                    <label class="role-option" id="role-employee">
                        <input type="radio" name="role" value="employee">
                        <i class="fas fa-user-tie"></i>
                        <span>Employee</span>
                    </label>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Choose a username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="Enter your phone number">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Create a password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Confirm your password" required>
                </div>
            </div>
            
            <div class="alert alert-info" id="employee-notice" style="display: none;">
                <i class="fas fa-info-circle"></i>
                <span>Employee accounts require admin approval before you can login.</span>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="index. php?page=login">Sign in here</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleOptions = document.querySelectorAll('.role-option');
    const employeeNotice = document.getElementById('employee-notice');
    
    roleOptions.forEach(option => {
        option.addEventListener('click', function() {
            roleOptions.forEach(opt => opt.classList. remove('active'));
            this.classList. add('active');
            
            const role = this.querySelector('input').value;
            employeeNotice.style.display = role === 'employee' ? 'flex' : 'none';
        });
    });
});
</script>