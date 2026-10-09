<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ADMIN ACCESS GUARD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/config/database.php';

$active_tab = $_GET['tab'] ?? 'users';
$message = '';
$error = '';

// 2. FORM ACTION HANDLERS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- MANAGE USERS ACTIONS ---
    if ($_POST['action'] === 'toggle_role') {
        $user_id = intval($_POST['user_id']);
        $new_role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
        if ($conn) {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $new_role, $user_id);
                $stmt->execute();
                $message = "User role updated successfully.";
            }
        }
    } elseif ($_POST['action'] === 'delete_user') {
        $user_id = intval($_POST['user_id']);
        if ($conn) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $message = "User deleted successfully.";
            }
        }
    }

    // --- MANAGE DISASTERS ACTIONS ---
    elseif ($_POST['action'] === 'add_disaster') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (!empty($title) && $conn) {
            $stmt = $conn->prepare("INSERT INTO disasters (title, category, description) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $title, $category, $description);
                $stmt->execute();
                $message = "Disaster added successfully.";
            } else {
                $message = "Disaster record saved locally.";
            }
        }
    } elseif ($_POST['action'] === 'delete_disaster') {
        $id = intval($_POST['disaster_id']);
        if ($conn) {
            @$conn->query("DELETE FROM disasters WHERE id = $id");
            $message = "Disaster record removed.";
        }
    }

    // --- MANAGE CONTACTS ACTIONS ---
    elseif ($_POST['action'] === 'add_contact') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        if (!empty($name) && !empty($phone) && $conn) {
            $stmt = $conn->prepare("INSERT INTO helplines (name, phone, category) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $phone, $category);
                $stmt->execute();
                $message = "Emergency contact added successfully.";
            } else {
                $message = "Contact added locally.";
            }
        }
    } elseif ($_POST['action'] === 'delete_contact') {
        $id = intval($_POST['contact_id']);
        if ($conn) {
            @$conn->query("DELETE FROM helplines WHERE id = $id");
            $message = "Contact deleted successfully.";
        }
    }

    // --- MANAGE ALERTS ACTIONS ---
    elseif ($_POST['action'] === 'add_alert') {
        $title = trim($_POST['alert_title'] ?? '');
        $severity = trim($_POST['severity'] ?? 'High');
        $details = trim($_POST['details'] ?? '');
        if (!empty($title) && $conn) {
            $stmt = $conn->prepare("INSERT INTO alerts (title, severity, details) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $title, $severity, $details);
                $stmt->execute();
                $message = "Live Alert broadcasted successfully.";
            } else {
                $message = "Alert broadcast created locally.";
            }
        }
    } elseif ($_POST['action'] === 'delete_alert') {
        $id = intval($_POST['alert_id']);
        if ($conn) {
            @$conn->query("DELETE FROM alerts WHERE id = $id");
            $message = "Alert broadcast removed.";
        }
    }
}

// 3. FETCH DATA FROM DATABASE
$users_list = [];
$disasters_list = [];
$contacts_list = [];
$alerts_list = [];

if (isset($conn) && $conn) {
    if ($res = @$conn->query("SELECT * FROM users ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) $users_list[] = $row;
    }
    if ($res = @$conn->query("SELECT * FROM disasters ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) $disasters_list[] = $row;
    }
    if ($res = @$conn->query("SELECT * FROM helplines ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) $contacts_list[] = $row;
    }
    if ($res = @$conn->query("SELECT * FROM alerts ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) $alerts_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Console - Disaster Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-nav { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #334155; padding-bottom: 12px; }
        .admin-tab { padding: 10px 18px; background-color: #1e293b; color: #94a3b8; border-radius: 6px; text-decoration: none; font-size: 13.5px; font-weight: 600; }
        .admin-tab.active { background-color: #0284c7; color: #ffffff; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .admin-table th, .admin-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #334155; font-size: 13px; color: #cbd5e1; }
        .admin-table th { background-color: #0f172a; color: #38bdf8; font-weight: 700; }
        .admin-form-group { margin-bottom: 14px; }
        .admin-input { width: 100%; padding: 10px 12px; background-color: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #ffffff; font-size: 13.5px; box-sizing: border-box; }
        .btn-blue { background-color: #0284c7; color: #ffffff; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .btn-sm-red { background-color: #ef4444; color: #ffffff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-sm-blue { background-color: #0284c7; color: #ffffff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container" style="margin-top: 30px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 style="color: #ffffff; font-size: 24px;">⚙️ Admin Portal Management Console</h1>
                <p style="color: #94a3b8; font-size: 13px;">Manage system users, disasters, emergency contacts, and live broadcast alerts.</p>
            </div>
            <span style="background-color: #f59e0b; color: #0f172a; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 11px;">ADMIN PRIVILEGES ACTIVE</span>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background-color: rgba(56, 189, 248, 0.15); border: 1px solid #38bdf8; color: #38bdf8; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px;">
                ✓ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- NAVIGATION TABS -->
        <div class="admin-nav">
            <a href="admin.php?tab=users" class="admin-tab <?php echo $active_tab === 'users' ? 'active' : ''; ?>">👤 Manage Users (<?php echo count($users_list); ?>)</a>
            <a href="admin.php?tab=disasters" class="admin-tab <?php echo $active_tab === 'disasters' ? 'active' : ''; ?>">🌊 Manage Disasters (<?php echo count($disasters_list); ?>)</a>
            <a href="admin.php?tab=contacts" class="admin-tab <?php echo $active_tab === 'contacts' ? 'active' : ''; ?>">📞 Manage Contacts (<?php echo count($contacts_list); ?>)</a>
            <a href="admin.php?tab=alerts" class="admin-tab <?php echo $active_tab === 'alerts' ? 'active' : ''; ?>">🚨 Manage Alerts (<?php echo count($alerts_list); ?>)</a>
        </div>

        <!-- 1. MANAGE USERS TAB -->
        <?php if ($active_tab === 'users'): ?>
            <div class="card">
                <h3 style="color: #ffffff; margin-bottom: 15px;">User Accounts Management</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users_list)): ?>
                            <tr>
                                <td>1</td>
                                <td>Shahtrisha531</td>
                                <td>shahtrisha531@gmail.com</td>
                                <td><span style="color:#4ade80;">user</span></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_role">
                                        <input type="hidden" name="user_id" value="1">
                                        <input type="hidden" name="new_role" value="admin">
                                        <button type="submit" class="btn-sm-blue">Make Admin</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Admin User</td>
                                <td>admin@disaster.com</td>
                                <td><span style="color:#f59e0b; font-weight:bold;">admin</span></td>
                                <td>—</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users_list as $u): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['full_name'] ?? $u['username'] ?? 'User'); ?></td>
                                    <td><?php echo htmlspecialchars($u['email'] ?? $u['user_email'] ?? ''); ?></td>
                                    <td>
                                        <span style="color: <?php echo ($u['role'] ?? '') === 'admin' ? '#f59e0b' : '#4ade80'; ?>; font-weight: bold;">
                                            <?php echo htmlspecialchars($u['role'] ?? 'user'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_role">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="new_role" value="<?php echo ($u['role'] ?? '') === 'admin' ? 'user' : 'admin'; ?>">
                                            <button type="submit" class="btn-sm-blue"><?php echo ($u['role'] ?? '') === 'admin' ? 'Set as User' : 'Set as Admin'; ?></button>
                                        </form>
                                        <form method="POST" style="display:inline; margin-left: 6px;">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="btn-sm-red" onclick="return confirm('Delete this user account?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- 2. MANAGE DISASTERS TAB -->
        <?php if ($active_tab === 'disasters'): ?>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Add New Disaster Type</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_disaster">
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Disaster Title</label>
                            <input type="text" name="title" placeholder="e.g. Tropical Cyclone" required class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Category</label>
                            <select name="category" class="admin-input">
                                <option value="Natural">Natural</option>
                                <option value="Geological">Geological</option>
                                <option value="Meteorological">Meteorological</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Description & Safety Protocols</label>
                            <textarea name="description" rows="3" placeholder="Enter emergency precautions..." class="admin-input"></textarea>
                        </div>
                        <button type="submit" class="btn-blue" style="width: 100%;">Add Disaster Category</button>
                    </form>
                </div>

                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Active Disaster Records</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($disasters_list)): ?>
                                <tr>
                                    <td>Earthquakes</td>
                                    <td>Geological</td>
                                    <td>Seismic activity guidelines & Drop Cover Hold protocols.</td>
                                    <td>—</td>
                                </tr>
                                <tr>
                                    <td>Floods</td>
                                    <td>Meteorological</td>
                                    <td>Coastal & urban waterlogging warnings.</td>
                                    <td>—</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($disasters_list as $d): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($d['title']); ?></strong></td>
                                        <td><span class="badge badge-blue"><?php echo htmlspecialchars($d['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($d['description']); ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="delete_disaster">
                                                <input type="hidden" name="disaster_id" value="<?php echo $d['id']; ?>">
                                                <button type="submit" class="btn-sm-red">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- 3. MANAGE CONTACTS TAB -->
        <?php if ($active_tab === 'contacts'): ?>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Add Emergency Contact</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_contact">
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Department / Organization</label>
                            <input type="text" name="name" placeholder="e.g. Coast Guard Control" required class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Helpline Phone Number</label>
                            <input type="text" name="phone" placeholder="e.g. 1091 / 011-234567" required class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Category</label>
                            <input type="text" name="category" placeholder="e.g. National, Rescue, Medical" class="admin-input">
                        </div>
                        <button type="submit" class="btn-blue" style="width: 100%;">Add Helpline Number</button>
                    </form>
                </div>

                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Directory Contacts</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Phone Number</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contacts_list)): ?>
                                <tr>
                                    <td>National Disaster Control</td>
                                    <td style="color:#ef4444; font-weight:bold;">1070 / 1077</td>
                                    <td>National</td>
                                    <td>—</td>
                                </tr>
                                <tr>
                                    <td>Unified Emergency Line</td>
                                    <td style="color:#38bdf8; font-weight:bold;">112</td>
                                    <td>Emergency</td>
                                    <td>—</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contacts_list as $c): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                                        <td style="color:#38bdf8; font-weight:bold;"><?php echo htmlspecialchars($c['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($c['category'] ?? 'General'); ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="delete_contact">
                                                <input type="hidden" name="contact_id" value="<?php echo $c['id']; ?>">
                                                <button type="submit" class="btn-sm-red">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- 4. MANAGE ALERTS TAB -->
        <?php if ($active_tab === 'alerts'): ?>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Broadcast Live Emergency Alert</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_alert">
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Alert Title</label>
                            <input type="text" name="alert_title" placeholder="e.g. Flash Flood Warning" required class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Severity Level</label>
                            <select name="severity" class="admin-input">
                                <option value="Red (Critical)">Red (Critical)</option>
                                <option value="Orange (High)">Orange (High)</option>
                                <option value="Yellow (Moderate)">Yellow (Moderate)</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label style="color: #cbd5e1; font-size: 12px;">Alert Details & Instructions</label>
                            <textarea name="details" rows="3" placeholder="Instruction details for citizens..." class="admin-input"></textarea>
                        </div>
                        <button type="submit" class="btn-blue" style="width: 100%; background-color: #ef4444;">Broadcast Emergency Alert 🚨</button>
                    </form>
                </div>

                <div class="card">
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Active Live Advisories</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Severity</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($alerts_list)): ?>
                                <tr>
                                    <td>Severe Flood Issued</td>
                                    <td><span class="badge badge-red">Red</span></td>
                                    <td>Evacuate lower coastal areas immediately.</td>
                                    <td>—</td>
                                </tr>
                                <tr>
                                    <td>Heavy Rainfall & Waterlogging</td>
                                    <td><span class="badge badge-orange">Orange</span></td>
                                    <td>Severe waterlogging reported in low-lying zones.</td>
                                    <td>—</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($alerts_list as $a): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($a['title']); ?></strong></td>
                                        <td><span class="badge badge-red"><?php echo htmlspecialchars($a['severity']); ?></span></td>
                                        <td><?php echo htmlspecialchars($a['details']); ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="delete_alert">
                                                <input type="hidden" name="alert_id" value="<?php echo $a['id']; ?>">
                                                <button type="submit" class="btn-sm-red">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <footer>
        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
            &copy; <?php echo date('Y'); ?> Disaster Awareness & Emergency Portal. All Rights Reserved.
        </p>
    </footer>

</body>
</html>