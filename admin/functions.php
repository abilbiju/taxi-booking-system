<?php


// Get count of records in a specific table
function getCount($conn, $table) {
    $query = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

// Get count of records by role
function getCountByRole($conn, $role) {
    $query = "SELECT COUNT(*) as count FROM users WHERE role='$role'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

// Fetch all records from a table
function fetchAll($conn, $table) {
    $query = "SELECT * FROM $table";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
