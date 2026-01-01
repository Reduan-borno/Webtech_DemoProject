<? php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $phone;
    public $address;
    public $role;
    public $status;
    public $profile_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET username=:username, email=:email, password=:password, 
                      full_name=:full_name, phone=: phone, address=: address, 
                      role=:role, status=:status";

        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(": address", $this->address);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    // Login user
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username OR email = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO:: FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                if ($row['status'] !== 'approved') {
                    return ['error' => 'Account not approved yet'];
                }
                return $row;
            }
        }
        return false;
    }

    // Get user by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all users by role
    public function getByRole($role) {
        $query = "SELECT * FROM " . $this->table . " WHERE role = :role ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":role", $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get pending employees
    public function getPendingEmployees() {
        $query = "SELECT * FROM " . $this->table . " WHERE role = 'employee' AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update user status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Update profile
    public function updateProfile() {
        $query = "UPDATE " .  $this->table .  " 
                  SET full_name=:full_name, phone=:phone, address=:address, email=:email 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(": id", $this->id);

        return $stmt->execute();
    }

    // Change password
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE " .  $this->table .  " SET password = : password WHERE id = : id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": password", $hashedPassword);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Delete user
    public function delete($id) {
        $query = "DELETE FROM " .  $this->table .  " WHERE id = : id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": id", $id);
        return $stmt->execute();
    }

    // Get all users (for admin)
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }
}
?>