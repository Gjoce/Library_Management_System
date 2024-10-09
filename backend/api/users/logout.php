<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    echo json_encode(['message' => 'Logged out successfully.']);
} else {
    echo json_encode(['error' => 'Only POST requests are allowed.']);
}
