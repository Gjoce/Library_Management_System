<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Perform any server-side token cleanup if necessary

    // Invalidate token on the client-side by responding with a success message
    echo json_encode(['message' => 'Logged out successfully.']);
} else {
    echo json_encode(['error' => 'Only POST requests are allowed.']);
}
