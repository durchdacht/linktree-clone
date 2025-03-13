<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

// Load theme data
$data = json_decode(file_get_contents(__DIR__ . '/data.json'), true);
$theme = $data['theme'];
$profile = $data['profile'];

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (replace with proper authentication in production)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LinkTree</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: <?php echo $theme['primary_color']; ?>;
            --bg-color: <?php echo $theme['background_color']; ?>;
            --text-color: <?php echo $theme['text_color']; ?>;
            --card-bg: <?php echo $theme['card_bg']; ?>;
            --shadow-color: <?php echo $theme['shadow_color']; ?>;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
            transition: all 0.3s ease;
            <?php if (!empty($profile['background_image'])): ?>
            background-image: url('<?php echo htmlspecialchars($profile['background_image']); ?>');
            background-position: <?php echo $profile['background_settings']['position'] ?? 'center center'; ?>;
            background-size: <?php echo $profile['background_settings']['size'] ?? 'cover'; ?>;
            background-repeat: no-repeat;
            <?php endif; ?>
        }
        .login-container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px var(--shadow-color);
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: <?php echo $profile['profile_image_settings']['border_radius'] ?? '50%'; ?>;
            margin: 0 auto 1.5rem;
            border: 3px solid var(--primary-color);
            box-shadow: 0 4px 6px var(--shadow-color);
            overflow: hidden;
            background-color: var(--bg-color);
            position: relative;
        }
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: <?php echo $profile['profile_image_settings']['size'] ?? 'cover'; ?>;
            object-position: <?php echo $profile['profile_image_settings']['position'] ?? 'center center'; ?>;
        }
        .profile-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
        }
        .profile-image-placeholder i {
            font-size: 4rem;
            color: var(--primary-color);
        }
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        .profile-bio {
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--shadow-color);
            border-radius: 8px;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-color), 0.1);
        }
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 2.5rem;
            color: var(--text-color);
            opacity: 0.5;
        }
        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--primary-color), 0.2);
        }
        .error-message {
            background-color: #ef4444;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        .back-link {
            position: fixed;
            bottom: 20px;
            left: 20px;
            opacity: 0.3;
            transition: opacity 0.3s ease;
            background-color: var(--card-bg);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px var(--shadow-color);
            backdrop-filter: blur(10px);
        }
        .back-link:hover {
            opacity: 1;
            transform: translateY(-2px);
        }
        .back-link a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        .login-subtitle {
            color: var(--text-color);
            opacity: 0.7;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="profile-image">
            <?php if (!empty($profile['profile_image'])): ?>
                <img src="<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile">
            <?php else: ?>
                <div class="profile-image-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars($profile['name']); ?></h1>
        <p class="profile-bio"><?php echo htmlspecialchars($profile['bio']); ?></p>
        
        <h2 class="login-title">Admin Login</h2>
        <p class="login-subtitle">Enter your credentials to access the dashboard</p>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                <i class="fas fa-user input-icon"></i>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                <i class="fas fa-lock input-icon"></i>
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i>
                Login to Dashboard
            </button>
        </form>
    </div>
    <div class="back-link">
        <a href="../index.php">
            <i class="fas fa-arrow-left"></i>
            Back to Profile
        </a>
    </div>
</body>
</html> 