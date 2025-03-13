<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Load current data
$data_file = __DIR__ . '/data.json';
$data = json_decode(file_get_contents($data_file), true);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                // Handle profile image upload
                if (isset($_FILES['profile_image_upload']) && $_FILES['profile_image_upload']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/uploads/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_extension = strtolower(pathinfo($_FILES['profile_image_upload']['name'], PATHINFO_EXTENSION));
                    $file_name = 'profile_' . time() . '.' . $file_extension;
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['profile_image_upload']['tmp_name'], $target_path)) {
                        $data['profile']['profile_image'] = 'uploads/' . $file_name;
                    }
                }

                // Handle background image upload
                if (isset($_FILES['background_image_upload']) && $_FILES['background_image_upload']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/uploads/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_extension = strtolower(pathinfo($_FILES['background_image_upload']['name'], PATHINFO_EXTENSION));
                    $file_name = 'background_' . time() . '.' . $file_extension;
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['background_image_upload']['tmp_name'], $target_path)) {
                        $data['profile']['background_image'] = 'uploads/' . $file_name;
                    }
                }

                // Update profile data
                $data['profile'] = array_merge($data['profile'], [
                    'name' => $_POST['name'],
                    'bio' => $_POST['bio'],
                    'profile_image_settings' => [
                        'position' => $_POST['profile_position'] ?? 'center center',
                        'size' => $_POST['profile_size'] ?? 'cover',
                        'border_radius' => $_POST['profile_border_radius'] ?? '50%'
                    ],
                    'background_settings' => [
                        'position' => $_POST['background_position'] ?? 'center center',
                        'size' => $_POST['background_size'] ?? 'cover',
                        'opacity' => $_POST['background_opacity'] ?? '1'
                    ],
                    'social_links' => [
                        'instagram' => $_POST['instagram'],
                        'twitter' => $_POST['twitter'],
                        'github' => $_POST['github']
                    ]
                ]);

                // If URL is provided and no file was uploaded, use the URL
                if (!empty($_POST['profile_image']) && !isset($_FILES['profile_image_upload'])) {
                    $data['profile']['profile_image'] = $_POST['profile_image'];
                }
                if (!empty($_POST['background_image']) && !isset($_FILES['background_image_upload'])) {
                    $data['profile']['background_image'] = $_POST['background_image'];
                }
                break;
            
            case 'update_theme':
                $data['theme'] = [
                    'primary_color' => $_POST['primary_color'],
                    'background_color' => $_POST['background_color'],
                    'text_color' => $_POST['text_color'],
                    'card_bg' => $_POST['card_bg'],
                    'shadow_color' => $_POST['shadow_color']
                ];
                break;
            
            case 'add_link':
                $data['links'][] = [
                    'title' => $_POST['title'],
                    'url' => $_POST['url'],
                    'icon' => $_POST['icon']
                ];
                break;
            
            case 'delete_link':
                $index = $_POST['link_index'];
                if (isset($data['links'][$index])) {
                    array_splice($data['links'], $index, 1);
                }
                break;
        }
        
        // Save the updated data
        file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: dashboard.php?success=1');
        exit;
    }
}

// Common Font Awesome icons for quick selection
$common_icons = [
    'fas fa-globe' => 'Website',
    'fas fa-shopping-bag' => 'Shop',
    'fas fa-book' => 'Blog',
    'fas fa-envelope' => 'Contact',
    'fas fa-user' => 'Profile',
    'fas fa-camera' => 'Camera',
    'fas fa-music' => 'Music',
    'fas fa-video' => 'Video',
    'fas fa-palette' => 'Art',
    'fas fa-code' => 'Code',
    'fas fa-gamepad' => 'Games',
    'fas fa-newspaper' => 'News',
    'fas fa-podcast' => 'Podcast',
    'fas fa-store' => 'Store',
    'fas fa-calendar' => 'Calendar'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LinkTree</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: <?php echo $data['theme']['primary_color']; ?>;
            --bg-color: <?php echo $data['theme']['background_color']; ?>;
            --text-color: <?php echo $data['theme']['text_color']; ?>;
            --card-bg: <?php echo $data['theme']['card_bg']; ?>;
            --shadow-color: <?php echo $data['theme']['shadow_color']; ?>;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        .admin-dashboard {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px var(--shadow-color);
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--shadow-color);
        }
        .dashboard-title {
            color: var(--text-color);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logout-btn {
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }
        .success-message {
            background-color: #10b981;
            color: white;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: none;
            animation: slideIn 0.3s ease-in-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        .error-message {
            background-color: #ef4444;
            color: white;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: none;
            animation: slideIn 0.3s ease-in-out;
        }
        .section {
            background-color: var(--bg-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px var(--shadow-color);
        }
        .section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--shadow-color);
            border-radius: 6px;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(var(--primary-color), 0.1);
        }
        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #dc2626;
        }
        .message {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            animation: slideIn 0.3s ease-out;
        }
        .message.success {
            background-color: #10b981;
            color: white;
        }
        .message.error {
            background-color: #ef4444;
            color: white;
        }
        .links-list {
            list-style: none;
            padding: 0;
        }
        .links-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background-color: var(--bg-color);
            border-radius: 6px;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 4px var(--shadow-color);
        }
        .links-list li:last-child {
            margin-bottom: 0;
        }
        .link-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .link-title i {
            color: var(--primary-color);
        }
        .theme-presets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .theme-preset {
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            border: 2px solid transparent;
        }
        .theme-preset:hover {
            transform: translateY(-2px);
        }
        .theme-preset.active {
            border-color: var(--primary-color);
        }
        .theme-preset .preview {
            height: 60px;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--bg-color));
        }
        .color-picker {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .color-input {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .color-input input[type="color"] {
            width: 40px;
            height: 40px;
            padding: 0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .icon-picker {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            max-height: 300px;
            overflow-y: auto;
            padding: 1rem;
            background-color: var(--bg-color);
            border-radius: 8px;
        }
        .icon-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .icon-option:hover {
            background-color: var(--card-bg);
            transform: translateY(-2px);
        }
        .icon-option.selected {
            background-color: var(--primary-color);
            color: white;
        }
        .icon-option i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .icon-option span {
            font-size: 0.75rem;
            text-align: center;
            word-break: break-word;
        }
        .icon-search {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--shadow-color);
            border-radius: 6px;
            margin-bottom: 1rem;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .icon-categories {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--shadow-color);
            border-radius: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .category-btn:hover {
            background-color: var(--card-bg);
        }
        .category-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        .image-upload-section {
            background-color: var(--bg-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .image-upload-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .image-preview {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .image-settings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        input[type="range"] {
            width: 100%;
            height: 6px;
            background: var(--card-bg);
            border-radius: 3px;
            outline: none;
            -webkit-appearance: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: var(--primary-color);
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>
<body class="light-theme">
    <div class="container">
        <div class="admin-dashboard">
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-cog"></i>
                    Admin Dashboard
                </h1>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2>Profile Settings</h2>
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($data['profile']['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($data['profile']['bio']); ?></textarea>
                    </div>
                    
                    <div class="image-upload-section">
                        <h3>Profile Image</h3>
                        <div class="image-preview">
                            <img id="profile-preview" src="<?php echo htmlspecialchars($data['profile']['profile_image']); ?>" alt="Profile Preview">
                        </div>
                        <div class="form-group">
                            <label for="profile_image">Profile Image URL</label>
                            <input type="url" id="profile_image" name="profile_image" class="form-control" value="<?php echo htmlspecialchars($data['profile']['profile_image']); ?>">
                            <input type="file" id="profile_image_upload" accept="image/*" class="form-control">
                        </div>
                        <div class="image-settings">
                            <div class="form-group">
                                <label for="profile_position">Position</label>
                                <select id="profile_position" name="profile_position" class="form-control">
                                    <option value="center center" <?php echo ($data['profile']['profile_image_settings']['position'] ?? '') === 'center center' ? 'selected' : ''; ?>>Center</option>
                                    <option value="top center" <?php echo ($data['profile']['profile_image_settings']['position'] ?? '') === 'top center' ? 'selected' : ''; ?>>Top</option>
                                    <option value="bottom center" <?php echo ($data['profile']['profile_image_settings']['position'] ?? '') === 'bottom center' ? 'selected' : ''; ?>>Bottom</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="profile_size">Size</label>
                                <select id="profile_size" name="profile_size" class="form-control">
                                    <option value="cover" <?php echo ($data['profile']['profile_image_settings']['size'] ?? '') === 'cover' ? 'selected' : ''; ?>>Cover</option>
                                    <option value="contain" <?php echo ($data['profile']['profile_image_settings']['size'] ?? '') === 'contain' ? 'selected' : ''; ?>>Contain</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="profile_border_radius">Border Radius</label>
                                <input type="range" id="profile_border_radius" name="profile_border_radius" class="form-control" 
                                       min="0" max="50" value="<?php echo str_replace('%', '', $data['profile']['profile_image_settings']['border_radius'] ?? '50'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="image-upload-section">
                        <h3>Background Image</h3>
                        <div class="image-preview">
                            <img id="background-preview" src="<?php echo htmlspecialchars($data['profile']['background_image'] ?? ''); ?>" alt="Background Preview">
                        </div>
                        <div class="form-group">
                            <label for="background_image">Background Image URL</label>
                            <input type="url" id="background_image" name="background_image" class="form-control" value="<?php echo htmlspecialchars($data['profile']['background_image'] ?? ''); ?>">
                            <input type="file" id="background_image_upload" accept="image/*" class="form-control">
                        </div>
                        <div class="image-settings">
                            <div class="form-group">
                                <label for="background_position">Position</label>
                                <select id="background_position" name="background_position" class="form-control">
                                    <option value="center center" <?php echo ($data['profile']['background_settings']['position'] ?? '') === 'center center' ? 'selected' : ''; ?>>Center</option>
                                    <option value="top center" <?php echo ($data['profile']['background_settings']['position'] ?? '') === 'top center' ? 'selected' : ''; ?>>Top</option>
                                    <option value="bottom center" <?php echo ($data['profile']['background_settings']['position'] ?? '') === 'bottom center' ? 'selected' : ''; ?>>Bottom</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="background_size">Size</label>
                                <select id="background_size" name="background_size" class="form-control">
                                    <option value="cover" <?php echo ($data['profile']['background_settings']['size'] ?? '') === 'cover' ? 'selected' : ''; ?>>Cover</option>
                                    <option value="contain" <?php echo ($data['profile']['background_settings']['size'] ?? '') === 'contain' ? 'selected' : ''; ?>>Contain</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="background_opacity">Opacity</label>
                                <input type="range" id="background_opacity" name="background_opacity" class="form-control" 
                                       min="0" max="100" value="<?php echo ($data['profile']['background_settings']['opacity'] ?? '1') * 100; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="instagram">Instagram URL</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" value="<?php echo htmlspecialchars($data['profile']['social_links']['instagram'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="twitter">Twitter URL</label>
                        <input type="url" id="twitter" name="twitter" class="form-control" value="<?php echo htmlspecialchars($data['profile']['social_links']['twitter'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="github">GitHub URL</label>
                        <input type="url" id="github" name="github" class="form-control" value="<?php echo htmlspecialchars($data['profile']['social_links']['github'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn">Save Profile</button>
                </form>
            </div>

            <div class="section">
                <h2>Theme Settings</h2>
                <form method="post" action="" id="themeForm">
                    <input type="hidden" name="action" value="update_theme">
                    
                    <div class="theme-presets">
                        <div class="theme-preset" data-theme="default">
                            <div class="preview"></div>
                            <span>Default Blue</span>
                        </div>
                        <div class="theme-preset" data-theme="forest">
                            <div class="preview"></div>
                            <span>Forest Green</span>
                        </div>
                        <div class="theme-preset" data-theme="sunny">
                            <div class="preview"></div>
                            <span>Sunny Yellow</span>
                        </div>
                        <div class="theme-preset" data-theme="cherry">
                            <div class="preview"></div>
                            <span>Cherry Blossom</span>
                        </div>
                        <div class="theme-preset" data-theme="royal">
                            <div class="preview"></div>
                            <span>Royal Purple</span>
                        </div>
                        <div class="theme-preset" data-theme="ruby">
                            <div class="preview"></div>
                            <span>Ruby Red</span>
                        </div>
                        <div class="theme-preset" data-theme="dark">
                            <div class="preview"></div>
                            <span>Dark Mode</span>
                        </div>
                        <div class="theme-preset" data-theme="dark-forest">
                            <div class="preview"></div>
                            <span>Dark Forest</span>
                        </div>
                    </div>

                    <div class="color-picker">
                        <div class="color-input">
                            <label for="primary_color">Primary Color</label>
                            <input type="color" id="primary_color" name="primary_color" value="<?php echo $data['theme']['primary_color']; ?>">
                        </div>
                        <div class="color-input">
                            <label for="background_color">Background Color</label>
                            <input type="color" id="background_color" name="background_color" value="<?php echo $data['theme']['background_color']; ?>">
                        </div>
                        <div class="color-input">
                            <label for="text_color">Text Color</label>
                            <input type="color" id="text_color" name="text_color" value="<?php echo $data['theme']['text_color']; ?>">
                        </div>
                        <div class="color-input">
                            <label for="card_bg">Card Background</label>
                            <input type="color" id="card_bg" name="card_bg" value="<?php echo $data['theme']['card_bg']; ?>">
                        </div>
                        <div class="color-input">
                            <label for="shadow_color">Shadow Color</label>
                            <input type="color" id="shadow_color" name="shadow_color" value="<?php echo $data['theme']['shadow_color']; ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn">Save Theme</button>
                </form>
            </div>

            <div class="section">
                <h2>Links Management</h2>
                <form method="post" action="" class="add-link-form">
                    <input type="hidden" name="action" value="add_link">
                    <div class="form-group">
                        <label for="link_title">Link Title</label>
                        <input type="text" id="link_title" name="link_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="link_url">Link URL</label>
                        <input type="url" id="link_url" name="link_url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="link_icon">Icon</label>
                        <input type="text" id="link_icon" name="link_icon" class="form-control" placeholder="e.g., fas fa-link">
                        <div class="icon-picker-container">
                            <input type="text" class="icon-search" placeholder="Search icons...">
                            <div class="icon-categories">
                                <button type="button" class="category-btn active" data-category="all">All</button>
                                <button type="button" class="category-btn" data-category="social">Social</button>
                                <button type="button" class="category-btn" data-category="website">Website</button>
                                <button type="button" class="category-btn" data-category="contact">Contact</button>
                                <button type="button" class="category-btn" data-category="media">Media</button>
                                <button type="button" class="category-btn" data-category="other">Other</button>
                            </div>
                            <div class="icon-picker">
                                <?php
                                $icon_categories = [
                                    'social' => [
                                        ['class' => 'fab fa-facebook', 'label' => 'Facebook'],
                                        ['class' => 'fab fa-twitter', 'label' => 'Twitter'],
                                        ['class' => 'fab fa-instagram', 'label' => 'Instagram'],
                                        ['class' => 'fab fa-linkedin', 'label' => 'LinkedIn'],
                                        ['class' => 'fab fa-github', 'label' => 'GitHub'],
                                        ['class' => 'fab fa-youtube', 'label' => 'YouTube'],
                                        ['class' => 'fab fa-tiktok', 'label' => 'TikTok'],
                                        ['class' => 'fab fa-pinterest', 'label' => 'Pinterest']
                                    ],
                                    'website' => [
                                        ['class' => 'fas fa-globe', 'label' => 'Website'],
                                        ['class' => 'fas fa-shopping-bag', 'label' => 'Shop'],
                                        ['class' => 'fas fa-store', 'label' => 'Store'],
                                        ['class' => 'fas fa-blog', 'label' => 'Blog'],
                                        ['class' => 'fas fa-newspaper', 'label' => 'News'],
                                        ['class' => 'fas fa-book', 'label' => 'Book'],
                                        ['class' => 'fas fa-bookmark', 'label' => 'Bookmark'],
                                        ['class' => 'fas fa-calendar', 'label' => 'Calendar']
                                    ],
                                    'contact' => [
                                        ['class' => 'fas fa-envelope', 'label' => 'Email'],
                                        ['class' => 'fas fa-phone', 'label' => 'Phone'],
                                        ['class' => 'fas fa-map-marker-alt', 'label' => 'Location'],
                                        ['class' => 'fas fa-comment', 'label' => 'Message'],
                                        ['class' => 'fas fa-address-card', 'label' => 'Contact Card'],
                                        ['class' => 'fas fa-calendar-check', 'label' => 'Schedule']
                                    ],
                                    'media' => [
                                        ['class' => 'fas fa-camera', 'label' => 'Camera'],
                                        ['class' => 'fas fa-video', 'label' => 'Video'],
                                        ['class' => 'fas fa-music', 'label' => 'Music'],
                                        ['class' => 'fas fa-podcast', 'label' => 'Podcast'],
                                        ['class' => 'fas fa-film', 'label' => 'Film'],
                                        ['class' => 'fas fa-photo-video', 'label' => 'Photos & Videos']
                                    ],
                                    'other' => [
                                        ['class' => 'fas fa-user', 'label' => 'Profile'],
                                        ['class' => 'fas fa-cog', 'label' => 'Settings'],
                                        ['class' => 'fas fa-gamepad', 'label' => 'Games'],
                                        ['class' => 'fas fa-palette', 'label' => 'Art'],
                                        ['class' => 'fas fa-code', 'label' => 'Code'],
                                        ['class' => 'fas fa-heart', 'label' => 'Heart']
                                    ]
                                ];

                                foreach ($icon_categories as $category => $icons) {
                                    foreach ($icons as $icon) {
                                        echo "<div class='icon-option' data-category='$category' data-icon='{$icon['class']}'>";
                                        echo "<i class='{$icon['class']}'></i>";
                                        echo "<span>{$icon['label']}</span>";
                                        echo "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn">Add Link</button>
                </form>

                <div class="links-list">
                    <?php foreach ($data['links'] as $index => $link): ?>
                        <li>
                            <div class="link-title">
                                <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                                <span><?php echo htmlspecialchars($link['title']); ?></span>
                            </div>
                            <form method="post" action="" style="display: inline;">
                                <input type="hidden" name="action" value="delete_link">
                                <input type="hidden" name="link_index" value="<?php echo $index; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../script.js"></script>
    <script>
        // Theme presets
        const themePresets = {
            default: {
                primary_color: '#3b82f6',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            forest: {
                primary_color: '#10b981',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            sunny: {
                primary_color: '#f59e0b',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            cherry: {
                primary_color: '#ec4899',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            royal: {
                primary_color: '#8b5cf6',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            ruby: {
                primary_color: '#ef4444',
                background_color: '#ffffff',
                text_color: '#1f2937',
                card_bg: '#f3f4f6',
                shadow_color: 'rgba(0, 0, 0, 0.1)'
            },
            dark: {
                primary_color: '#3b82f6',
                background_color: '#1f2937',
                text_color: '#f3f4f6',
                card_bg: '#374151',
                shadow_color: 'rgba(0, 0, 0, 0.3)'
            },
            'dark-forest': {
                primary_color: '#10b981',
                background_color: '#111827',
                text_color: '#f3f4f6',
                card_bg: '#1f2937',
                shadow_color: 'rgba(0, 0, 0, 0.3)'
            }
        };

        // Apply theme preset
        document.querySelectorAll('.theme-preset').forEach(preset => {
            preset.addEventListener('click', () => {
                const theme = themePresets[preset.dataset.theme];
                document.getElementById('primary_color').value = theme.primary_color;
                document.getElementById('background_color').value = theme.background_color;
                document.getElementById('text_color').value = theme.text_color;
                document.getElementById('card_bg').value = theme.card_bg;
                document.getElementById('shadow_color').value = theme.shadow_color;
                
                // Update preview
                document.documentElement.style.setProperty('--primary-color', theme.primary_color);
                document.documentElement.style.setProperty('--bg-color', theme.background_color);
                document.documentElement.style.setProperty('--text-color', theme.text_color);
                document.documentElement.style.setProperty('--card-bg', theme.card_bg);
                document.documentElement.style.setProperty('--shadow-color', theme.shadow_color);
                
                // Update preview gradients
                document.querySelectorAll('.theme-preset .preview').forEach(preview => {
                    const parentTheme = themePresets[preview.parentElement.dataset.theme];
                    preview.style.background = `linear-gradient(45deg, ${parentTheme.primary_color}, ${parentTheme.background_color})`;
                });
                
                // Update active state
                document.querySelectorAll('.theme-preset').forEach(p => p.classList.remove('active'));
                preset.classList.add('active');
            });
            
            // Set initial preview gradient
            const theme = themePresets[preset.dataset.theme];
            preset.querySelector('.preview').style.background = `linear-gradient(45deg, ${theme.primary_color}, ${theme.background_color})`;
        });

        // Live preview for color inputs
        document.querySelectorAll('.color-input input[type="color"]').forEach(input => {
            input.addEventListener('input', () => {
                const property = input.id.replace('_', '-');
                document.documentElement.style.setProperty(`--${property}`, input.value);
            });
        });

        // Icon picker functionality
        const iconSearch = document.querySelector('.icon-search');
        const iconOptions = document.querySelectorAll('.icon-option');
        const iconInput = document.getElementById('link_icon');
        const categoryButtons = document.querySelectorAll('.category-btn');

        iconSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            iconOptions.forEach(option => {
                const iconText = option.querySelector('span').textContent.toLowerCase();
                option.style.display = iconText.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                const category = button.dataset.category;
                iconOptions.forEach(option => {
                    if (category === 'all' || option.dataset.category === category) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        });

        iconOptions.forEach(option => {
            option.addEventListener('click', () => {
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                iconInput.value = option.dataset.icon;
            });
        });

        // Image upload preview
        document.getElementById('profile_image_upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                    document.getElementById('profile_image').value = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('background_image_upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('background-preview').src = e.target.result;
                    document.getElementById('background_image').value = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Live preview for image settings
        document.getElementById('profile_position').addEventListener('change', function() {
            document.getElementById('profile-preview').style.objectPosition = this.value;
        });

        document.getElementById('profile_size').addEventListener('change', function() {
            document.getElementById('profile-preview').style.objectFit = this.value;
        });

        document.getElementById('profile_border_radius').addEventListener('input', function() {
            document.getElementById('profile-preview').style.borderRadius = this.value + '%';
        });

        document.getElementById('background_position').addEventListener('change', function() {
            document.getElementById('background-preview').style.objectPosition = this.value;
        });

        document.getElementById('background_size').addEventListener('change', function() {
            document.getElementById('background-preview').style.objectFit = this.value;
        });

        document.getElementById('background_opacity').addEventListener('input', function() {
            document.getElementById('background-preview').style.opacity = this.value / 100;
        });
    </script>
</body>
</html> 