<?php 
    include_once "../init.php";
    include_once '../connection.php';

    // if logged in redirect user to dashboard
    if (isset($_SESSION['UserId'])) {
        header('Location: 3-Dashboard.php');
    }

    $errors = []; // Array untuk menyimpan pesan kesalahan

    // Fungsi untuk membersihkan input
    function clean_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if(isset($_POST['submit'])) {
        // Validasi nama lengkap
        if(empty($_POST['fullname'])) {
            $errors['fullname'] = "Full Name is required";
        } else {
            $fullname = clean_input($_POST['fullname']);
            // Validasi panjang nama
            if(strlen($fullname) > 40) {
                $errors['fullname'] = "Full Name must be less than 40 characters";
            }
        }

        // Validasi email
        if(empty($_POST['email'])) {
            $errors['email'] = "Email is required";
        } else {
            $email = clean_input($_POST['email']);
            // Validasi format email
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Invalid email format";
            } else {
                // Validasi apakah email sudah ada
                if($getFromU->checkEmail($email)) {
                    $errors['email'] = "Email already exists";
                }
            }
        }

        // Validasi username
        if(empty($_POST['username'])) {
            $errors['username'] = "Username is required";
        } else {
            $username = clean_input($_POST['username']);
            // Validasi panjang username
            if(strlen($username) > 255) {
                $errors['username'] = "Username must be less than 255 characters";
            } else {
                // Validasi apakah username sudah ada
                if($getFromU->checkUsername($username)) {
                    $errors['username'] = "Username already exists";
                }
            }
        }

        // Validasi password
        if(empty($_POST['password'])) {
            $errors['password'] = "Password is required";
        } else {
            $password = clean_input($_POST['password']);
            // Validasi panjang password
            if(strlen($password) < 6) {
                $errors['password'] = "Password must be at least 6 characters long";
            }
        }

        // Validasi konfirmasi password
        if(empty($_POST['confirm-password'])) {
            $errors['confirm-password'] = "Confirm Password is required";
        } else {
            $confirm_password = clean_input($_POST['confirm-password']);
            // Validasi kesamaan password dan konfirmasi password
            if($password !== $confirm_password) {
                $errors['confirm-password'] = "Passwords do not match";
            }
        }

        // Jika tidak ada kesalahan, lanjutkan dengan proses pendaftaran
        if(empty($errors)) {
            $fullname = $getFromU->checkInput($_POST['fullname']);
            $email = $getFromU->checkInput($_POST['email']);
            $username = $getFromU->checkInput($_POST['username']);
            $password = $getFromU->checkInput($_POST['password']);

            // Storing image path in database
            if(empty($_FILES['profile-image']['name'])) {
                $target = '../static/images/userlogo.png';
            } else {
                // Unique profile image name for each user
                $profileImageName = time() .'_'. $_FILES['profile-image']['name'];
                $target = '../static/profileImages/' . $profileImageName;

                // Pindahkan file yang diunggah ke direktori target
                if (move_uploaded_file($_FILES['profile-image']['tmp_name'], $target)) {
                    // File berhasil dipindahkan, simpan path target di database
                } else {
                    // Gagal memindahkan file yang diunggah, gunakan gambar default
                    $target = '../static/images/userlogo.png';
                }
            }
            
            // Buat entri pengguna baru dalam database
            $user_id = $getFromU->create('user', array(
                'Email' => $email,
                'Password' => md5($password),
                'Full_Name' => $fullname,
                'Username' => $username,
                'Photo' =>$target, // Simpan path gambar profil dalam database
                'RegDate' => date("Y-m-d H:i:s")
            ));
            
            // Set session UserId
            $_SESSION['UserId'] = $user_id; 
            
            // Tampilkan pesan sukses menggunakan SweetAlert
            $_SESSION['swal'] = "<script>
                Swal.fire({
                    title: 'Yay!',
                    text: 'Congrats! You are now a registered user',
                    icon: 'success',
                    confirmButtonText: 'Done'
                })
                </script>";
            
            // Redirect ke halaman dashboard
            header('Location: 3-Dashboard.php');            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../static/images/wallet.png" sizes="16x16" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Expense Tracker</title>    
</head>
<body>
    <div class="min-h-screen bg-white shadow flex items-center justify-around flex-1">
        <div class="flex-1 flex flex-col items-center">
            <h1 class="text-2xl xl:text-3xl font-extrabold">
                Sign up
            </h1>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="w-full flex-1 mt-8">
                    <div class="mx-auto max-w-sm flex flex-col gap-5">
                        <!-- profile image -->
                        <label for="profile-image" class="flex items-center justify-center rounded-full cursor-pointer">
                            <img id="profile-preview" src="../static/images/userlogo.png" alt="" class="w-24 h-24 rounded-full object-cover">
                            <input id="profile-image" type="file" accept="image/*" class="hidden" name="profile-image" onchange="previewProfileImage(event)" />
                        </label>

                        <!-- fullname -->
                        <div>
                            <input id="fullname" class="w-full p-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="text" placeholder="Full Name" name="fullname" value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : ''; ?>" />
                            <?php if(isset($errors['fullname'])) { ?>
                                <small class="text-red-500"><?php echo $errors['fullname']; ?></small>
                            <?php } ?>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- email -->
                            <div>
                                <input id="email" class="w-full p-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="email" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                                <?php if(isset($errors['email'])) { ?>
                                    <small class="text-red-500"><?php echo $errors['email']; ?></small>
                                <?php } ?>
                            </div>

                            <!-- username -->
                            <div>
                                <input id="username" class="w-full p-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="text" placeholder="Username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" />
                                <small id="username-error" class="text-red-500"><?php echo isset($errors['username']) ? $errors['username'] : ''; ?></small>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- password -->
                            <div>
                                <input id="password" class="w-full p-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="password" placeholder="Password" name="password" />
                                <?php if(isset($errors['password'])) { ?>
                                    <small class="text-red-500"><?php echo $errors['password']; ?></small>
                                <?php } ?>
                            </div>

                            <!-- confirm password -->
                            <div>
                                <input id="confirm-password" class="w-full p-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white" type="password" placeholder="Confirm Password" name="confirm-password" />
                                <?php if(isset($errors['confirm-password'])) { ?>
                                    <small class="text-red-500"><?php echo $errors['confirm-password']; ?></small>
                                <?php } ?>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="tracking-wide font-semibold bg-[#F27474] text-gray-100 w-full py-4 rounded-lg hover:bg-[#f16161] transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                            <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <path d="M20 8v6M23 11h-6" />
                            </svg>
                            <span class="ml-3">
                                Sign Up
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="items-center justify-center hidden lg:flex flex-1">
            <img src="../static/images/registration.svg" alt="registration-illustration" class="w-auto h-96 object-cover" >
        </div>
    </div>

    <!-- check username availability -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const usernameInput = document.getElementById('username');
            const errorContainer = document.getElementById('username-error');
        
            usernameInput.addEventListener('input', function() {
                const username = this.value.trim();
            
                // Lakukan panggilan AJAX untuk memeriksa ketersediaan username
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../check_username.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.available) {
                                errorContainer.textContent = ''; // Kosongkan pesan kesalahan jika username tersedia
                            } else {
                                errorContainer.textContent = 'Username already exists'; // Tampilkan pesan kesalahan jika username sudah digunakan
                            }
                        } else {
                            console.error('Error:', xhr.status);
                        }
                    }
                };
                xhr.send('username=' + encodeURIComponent(username));
            });
        });
    </script>

    <!-- profile image preview -->
    <script>
        function previewProfileImage(event) {
            const fileInput = event.target;
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewImage = document.getElementById('profile-preview');
                    previewImage.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
