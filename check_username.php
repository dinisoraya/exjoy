<?php
    include_once "init.php";
    
    if(isset($_POST['username'])) {
        $username = $_POST['username'];
    
        // Periksa ketersediaan username menggunakan metode dari kelas User
        $available = !$getFromU->checkUsername($username);
    
        // Kirimkan respons dalam format JSON
        header('Content-Type: application/json');
        echo json_encode(['available' => $available]);
        exit;
    }
?>