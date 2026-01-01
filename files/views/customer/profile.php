<div class="page-header">
    <h1><i class="fas fa-user"></i> My Profile</h1>
    <p>Manage your account information</p>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h2><i class="fas fa-user-edit"></i> Profile Information</h2>
    </div>
    <form id="profile-form" onsubmit="handleUpdateProfile(event)">
        <div class="form-row">
            <div class="form-group">
                <label for="profile_name">Full Name *</label>
                <input type="text" id="profile_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="profile_username">Username</label>
                <input type="text" id="profile_username" class="form-control" readonly disabled>
            </div>
        </div>
        
        <div class="form-group">
            <label for="profile_email">Email Address *</label>
            <input type="email" id="profile_email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="profile_phone">Phone Number</label>
            <input type="tel" id="profile_phone" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="profile_address">Address</label>
            <textarea id="profile_address" class="form-control" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <button type="button" class="btn btn-secondary" onclick="openModal('change-password-modal')">
                <i class="fas fa-key"></i> Change Password
            </button>
        </div>
    </form>
</div>

<div class="card" style="max-width: 700px; margin-top: 20px;">
    <div class="card-header">
        <h2><i class="fas fa-exclamation-triangle"></i> Danger Zone</h2>
    </div>
    <div style="padding: 10px 0;">
        <p style="color: var(--gray-600); margin-bottom: 15px;">
            Once you delete your account, there is no going back.  Please be certain.
        </p>
        <button class="btn btn-danger" onclick="deleteAccount()">
            <i class="fas fa-trash"></i> Delete Account
        </button>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal-overlay" id="change-password-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <button class="modal-close" onclick="closeModal('change-password-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="change-password-form" onsubmit="handleChangePassword(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password *</label>
                    <input type="password" id="confirm_new_password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('change-password-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form