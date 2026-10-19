-- CyberRisk Database Schema & Initial Seed Data

CREATE DATABASE IF NOT EXISTS `cyberrisk` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cyberrisk`;

-- Drop tables if they already exist to ensure clean setup
DROP TABLE IF EXISTS `assessments`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `users`;

-- 1. Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Questions Table
CREATE TABLE `questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(50) NOT NULL,
    `question` TEXT NOT NULL,
    `option_a` TEXT NOT NULL,
    `option_b` TEXT NOT NULL,
    `option_c` TEXT NOT NULL,
    `option_d` TEXT NOT NULL,
    `score_a` INT NOT NULL DEFAULT 10,
    `score_b` INT NOT NULL DEFAULT 7,
    `score_c` INT NOT NULL DEFAULT 3,
    `score_d` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Assessments Table
CREATE TABLE `assessments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `password_score` INT NOT NULL,
    `device_score` INT NOT NULL,
    `privacy_score` INT NOT NULL,
    `phishing_score` INT NOT NULL,
    `network_score` INT NOT NULL,
    `social_score` INT NOT NULL,
    `overall_score` INT NOT NULL,
    `risk_level` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_assessments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Users
-- Default Passwords:
-- Admin:   AdminPassword#2026
-- Student: StudentPassword#2026
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
('System Administrator', 'admin@cyberrisk.local', '$2y$12$eSwzEEOqPVNZwO3FBI.7Ru1RUZkjMrJ/pcM40hdgZ7FiOySkc3VXe', 'admin'),
('Alex Taylor', 'student@cyberrisk.local', '$2y$12$3fO9.w4DM1.ZFf9voe/CaO/zZsCcLGrAAmn37pbMzexVL47qTXFPW', 'student'),
('Jordan Lee', 'jordan@cyberrisk.local', '$2y$12$3fO9.w4DM1.ZFf9voe/CaO/zZsCcLGrAAmn37pbMzexVL47qTXFPW', 'student'),
('Sam Casey', 'sam@cyberrisk.local', '$2y$12$3fO9.w4DM1.ZFf9voe/CaO/zZsCcLGrAAmn37pbMzexVL47qTXFPW', 'student');

-- Seed 24 Assessment Questions (4 per category)
INSERT INTO `questions` (`category`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `score_a`, `score_b`, `score_c`, `score_d`) VALUES
-- 1. Password Security
('Password', 'How do you create and manage passwords across your different accounts?',
 'I use a dedicated password manager to generate and store unique, complex passwords',
 'I create strong passwords using personal patterns and memorize them',
 'I use 2-3 variations of the same password across multiple sites',
 'I use the same simple password for almost everything', 10, 7, 3, 0),

('Password', 'What is your current policy regarding Multi-Factor Authentication (MFA / 2FA)?',
 'Enabled on all accounts that support it, using an authenticator app or hardware key',
 'Enabled on critical accounts (bank, primary email) via SMS or email codes',
 'Only enabled when an application or service strictly mandates it',
 'Disabled or never set up because it adds extra steps', 10, 7, 3, 0),

('Password', 'How do you respond when a platform warns that your password may have been in a data breach?',
 'Immediately change it and review other services for password reuse',
 'Change it within a few days when I have spare time',
 'Ignore it unless I notice suspicious account activity',
 'Dismiss the notification and continue using the password', 10, 7, 3, 0),

('Password', 'Where do you store account backup recovery codes or login secrets?',
 'In an encrypted digital vault or an offline fireproof safe',
 'In a password-protected digital document on my personal computer',
 'In unencrypted notes apps or on a physical sticky note',
 'In an email draft or shared messaging thread with myself', 10, 6, 2, 0),

-- 2. Device Security
('Device', 'How do you handle operating system and software security updates?',
 'Automatic updates are enabled; patches are installed immediately',
 'I review and install updates within a week of release',
 'I postpone updates for weeks or months until forced to restart',
 'I disable updates because restarts interrupt my work', 10, 7, 3, 0),

('Device', 'How is your primary computer and mobile phone locked when not in use?',
 'Strong alphanumeric passcode or biometrics with auto-lock set under 2 minutes',
 'Standard PIN or fingerprint with auto-lock set to 5-10 minutes',
 'Simple 4-digit PIN with long or disabled timeout',
 'No screen lock or password protection at all', 10, 7, 3, 0),

('Device', 'What endpoint protection or antivirus software is active on your device?',
 'Active, real-time antivirus/EDR with automated scans and up-to-date virus signatures',
 'Built-in OS security (e.g. Windows Defender) left on default settings',
 'A free scanner run manually only when the computer feels slow',
 'No antivirus or endpoint security software installed', 10, 8, 3, 0),

('Device', 'How do you manage USB thumb drives and external media?',
 'Only use encrypted personal drives and scan them before opening files',
 'Use standard USB drives and avoid plugging into untrusted public computers',
 'Plug in borrowed or found USB drives without scanning',
 'I will plug in any USB drive found in a public space to see what is on it', 10, 7, 2, 0),

-- 3. Privacy
('Privacy', 'What privacy configuration do you enforce on your social media profiles?',
 'Strictly private; only verified personal friends and contacts can view my profile',
 'Semi-private; some details are public, but personal contact info is private',
 'Default public settings with little customization',
 'Completely open profile sharing daily locations, phone number, and routines', 10, 7, 3, 0),

('Privacy', 'How do you handle cookie banners and browser tracking permissions?',
 'Use privacy-focused browsers/extensions and reject non-essential tracking cookies',
 'Rely on standard browser tracking protection and clear history periodically',
 'Click Accept All on every banner to get rid of popups quickly',
 'Allow all tracking and install various unverified browser extensions', 10, 7, 3, 0),

('Privacy', 'What personal information do you disclose on public online forums or apps?',
 'Never disclose sensitive identifiers (full birth date, address, phone, financial details)',
 'Occasionally share birth date without year, keeping location and family private',
 'Regularly post live locations, travel schedules, and workplace details',
 'Post photos of boarding passes, identification cards, or official documents', 10, 7, 2, 0),

('Privacy', 'How do you handle mobile application permission requests (camera, mic, location)?',
 'Grant permissions only while using the app and periodically audit permissions',
 'Review permissions during installation and deny unnecessary requests',
 'Accept all permission requests without reviewing them',
 'Never check or manage app permissions', 10, 7, 3, 0),

-- 4. Phishing Awareness
('Phishing', 'How do you react to an urgent email claiming your bank or account is suspended?',
 'Inspect the sender header, avoid clicking links, and verify directly on the official site',
 'Hover over the link to verify the target URL before deciding',
 'Click the link out of concern, but leave if the login page looks odd',
 'Immediately click the link and enter my credentials to prevent suspension', 10, 7, 3, 0),

('Phishing', 'How do you handle links sent via SMS, WhatsApp, or instant messages from unknown numbers?',
 'Never click unsolicited links; block and report the sender',
 'Search for the sender phone number online before interacting',
 'Click shortened links (e.g., bit.ly) if the offer or tracking alert looks interesting',
 'Open any prize, parcel, or delivery notification link immediately', 10, 7, 2, 0),

('Phishing', 'What do you do when an unexpected email attachment (.zip, .exe, .html) arrives from a known contact?',
 'Contact the person via a separate channel (call/chat) to verify before opening',
 'Scan the file with antivirus software before opening',
 'Open the attachment directly assuming the contact is safe',
 'Download and run the file without checking', 10, 7, 2, 0),

('Phishing', 'How do you confirm that a website login form is genuine?',
 'Verify the exact domain spelling, SSL/TLS certificate, and check for homograph attacks',
 'Look for the HTTPS padlock and check the general brand name in the address bar',
 'Look at the page styling; if the logo and colors look authentic, I trust it',
 'I do not check URLs and assume search engine links are always genuine', 10, 7, 3, 0),

-- 5. Network Security
('Network', 'How do you connect to public Wi-Fi networks in cafes, airports, or hotels?',
 'Always connect through a trustworthy VPN or use mobile phone hotspot data',
 'Connect to Wi-Fi but avoid banking, shopping, or logging into sensitive accounts',
 'Connect freely and rely solely on HTTPS website encryption',
 'Connect automatically to any open, unencrypted Wi-Fi hotspot available', 10, 7, 4, 0),

('Network', 'How is your home Wi-Fi network secured?',
 'WPA3 or WPA2-AES encryption, default router admin credentials changed, firmware up to date',
 'Strong Wi-Fi password, but default router admin password left unchanged',
 'Weak or easily guessable Wi-Fi password shared freely with neighbors',
 'Unsecured open Wi-Fi network or factory default ISP router configuration', 10, 6, 3, 0),

('Network', 'How do you handle Bluetooth and file-sharing (AirDrop, Quick Share) in public spaces?',
 'Turned off when not in active use, and visibility set to non-discoverable',
 'Bluetooth kept on for headphones, but file discovery limited to contacts only',
 'Left discoverable to all devices at all times',
 'Accept incoming file transfer prompts from nearby unknown devices', 10, 7, 3, 0),

('Network', 'How do you access remote campus or workplace internal systems?',
 'Exclusively through an institutional VPN configured with Multi-Factor Authentication',
 'Via authenticated secure web portals with HTTPS and 2FA',
 'Direct remote desktop (RDP) or exposed ports across the public internet',
 'Through unapproved third-party screen sharing tools without access controls', 10, 8, 2, 0),

-- 6. Social Engineering
('Social', 'What do you do if a friend or colleague urgently messages requesting money or gift cards?',
 'Call them directly on phone or meet in person to confirm identity before doing anything',
 'Ask a personal challenge question that only the real individual could answer',
 'Hesitate but comply if the situation appears genuinely critical',
 'Immediately purchase the cards or transfer money to help them out', 10, 8, 2, 0),

('Social', 'How do you handle recruiters or investors approaching you on social media with lucrative offers?',
 'Independently verify their company domain, corporate email, and legitimate identity',
 'Chat cautiously, but refuse to download files or pay any application fees',
 'Accept files and questionnaires sent by them to learn more about the role',
 'Send complete personal CV, ID copy, and banking details right away', 10, 7, 2, 0),

('Social', 'How do you protect your screen from observation in public areas or shared libraries?',
 'Use a privacy screen filter and position screens away from foot traffic',
 'Stay alert to nearby individuals and cover keyboard when typing credentials',
 'Occasionally check surroundings if anyone looks suspicious',
 'Leave screens unlocked and unattended while stepping away in public spaces', 10, 7, 3, 0),

('Social', 'How do you dispose of physical documents containing personal or financial information?',
 'Shred them using a cross-cut or micro-cut paper shredder',
 'Tear them up by hand into small pieces before recycling',
 'Throw them directly into the trash intact',
 'Leave them in public recycling bins or shared document trays', 10, 6, 2, 0);

-- Seed Sample Assessments for Analytics
INSERT INTO `assessments` (`user_id`, `password_score`, `device_score`, `privacy_score`, `phishing_score`, `network_score`, `social_score`, `overall_score`, `risk_level`, `created_at`) VALUES
(2, 90, 85, 80, 85, 90, 80, 85, 'Low Risk', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 65, 70, 60, 65, 70, 60, 65, 'Moderate Risk', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 45, 50, 40, 45, 45, 45, 45, 'High Risk', NOW());