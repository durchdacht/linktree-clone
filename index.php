<?php
/**
 * LinkTree - A beautiful and customizable link sharing platform
 * Created with ❤️ by fld.wtf
 * 
 * @package LinkTree
 * @version 1.0.0
 */

// Load profile data from JSON
$data = json_decode(file_get_contents(__DIR__ . '/admin/data.json'), true);
$profile = $data['profile'];
$links = $data['links'];
$theme = $data['theme'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($profile['bio']); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($profile['name']); ?>">
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($profile['name']); ?> - LinkTree">
    <meta property="og:description" content="<?php echo htmlspecialchars($profile['bio']); ?>">
    <?php if (!empty($profile['profile_image'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($profile['profile_image']); ?>">
    <?php endif; ?>
    <meta property="og:type" content="profile">
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($profile['name']); ?> - LinkTree">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($profile['bio']); ?>">
    <?php if (!empty($profile['profile_image'])): ?>
    <meta name="twitter:image" content="<?php echo htmlspecialchars($profile['profile_image']); ?>">
    <?php endif; ?>
    <title><?php echo htmlspecialchars($profile['name']); ?> - LinkTree</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Theme Variables */
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
        .container {
            max-width: 680px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .profile-section {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px var(--shadow-color);
            text-align: center;
            margin-bottom: 2rem;
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
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }
        .profile-bio {
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .social-link {
            color: var(--text-color);
            font-size: 1.5rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }
        .social-link:hover {
            opacity: 1;
            transform: translateY(-2px);
            color: var(--primary-color);
        }
        .links-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .link-card {
            background-color: var(--card-bg);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px var(--shadow-color);
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
        }
        .link-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px var(--shadow-color);
            background-color: var(--primary-color);
            color: white;
        }
        .link-card i {
            font-size: 1.25rem;
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .link-card span {
            width: 100%;
            font-weight: 500;
            text-align: center;
        }
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.3;
            transition: opacity 0.3s ease;
            background-color: var(--card-bg);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px var(--shadow-color);
            backdrop-filter: blur(10px);
        }
        .admin-link:hover {
            opacity: 1;
            transform: translateY(-2px);
        }
        .admin-link a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .footer {
            text-align: center;
            padding: 0.5rem 0;
            color: var(--text-color);
            opacity: 0.4;
            font-size: 0.75rem;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: transparent;
            backdrop-filter: blur(5px);
            transition: opacity 0.3s ease;
        }
        .footer:hover {
            opacity: 0.7;
        }
        .footer p {
            margin: 0;
            padding: 0;
        }
        .footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: opacity 0.3s ease;
        }
        .footer a:hover {
            opacity: 0.8;
        }
        .credits {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .credits span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Main Container -->
    <div class="container">
        <!-- Profile Section -->
        <div class="profile-section">
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
            
            <div class="social-links">
                <?php if (!empty($profile['social_links']['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['social_links']['instagram']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($profile['social_links']['twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['social_links']['twitter']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($profile['social_links']['github'])): ?>
                    <a href="<?php echo htmlspecialchars($profile['social_links']['github']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-github"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Links Container -->
        <div class="links-container">
            <?php foreach ($links as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>" class="link-card" target="_blank">
                    <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    <span><?php echo htmlspecialchars($link['title']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>made with ❤️ by fld.wtf</p>
        </div>
    </div>

    <!-- Admin Panel Link -->
    <div class="admin-link">
        <a href="admin/">
            <i class="fas fa-cog"></i>
        </a>
    </div>
</body>
</html> 