<?php
session_start();

class Action {
    private $db;

    // Constructor: Establish database connection
    public function __construct() {
        ob_start();
        include('../connection.php');
        $this->db = $conn;
    }

    // Destructor: Close database connection
    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    // Login method: Verify user credentials
    function login(){
        extract($_POST);

        // Use prepared statement to avoid SQL injection
        $qry = $this->db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $qry->bind_param("s", $username);  // 's' denotes the string type
        $qry->execute();
        $result = $qry->get_result();

        // Check if user exists and verify the password
        if($result->num_rows > 0){
            $user = $result->fetch_assoc();

            // Check if the password is correct
            if(password_verify($password, $user['password'])){
                // Store session variables securely
                foreach ($user as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }

                // Return user type after login
                return ($_SESSION['login_type'] == 1) ? 1 : 2; // Admin or Staff
            } else {
                return 3; // Incorrect password
            }
        } else {
            return 3; // User not found
        }
    }

    // Logout method: Destroy session and redirect
    function logout(){
        session_unset();  // Remove all session variables
        session_destroy();  // Destroy the session
        header("Location: index.php");  // Redirect to login page
        exit();  // Ensure no further code is executed
    }

    // Save user method: Create or update user details
    function save_user(){
        extract($_POST);

        // Prepare user data for insert or update
        $data = "name = ?, username = ?, password = ?, type = ?";

        // Check if admin_id is set to update or insert new user
        if (empty($admin_id)) {
            // Insert new user (use prepared statement)
            $qry = $this->db->prepare("INSERT INTO admin SET " . $data);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password
            $qry->bind_param("sssi", $name, $username, $hashed_password, $type);
        } else {
            // Update existing user (use prepared statement)
            $qry = $this->db->prepare("UPDATE admin SET " . $data . " WHERE admin_id = ?");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password
            $qry->bind_param("sssi", $name, $username, $hashed_password, $type, $admin_id);
        }

        // Execute the query and check if the action was successful
        if ($qry->execute()) {
            return 1;  // Success
        } else {
            return 0;  // Failure
        }
    }
}
?>
