<?php
// Mengambil nama file saat ini untuk menandai menu aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Menentukan prefix path secara dinamis agar link selalu benar
$path_prefix = (in_array(basename(dirname($_SERVER['PHP_SELF'])), ['admin', 'desa'])) ? '../' : '';

?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $path_prefix; ?>assets/img/logo_cibungbulang.png" alt="Logo">
        <h3>SISTEM LAMPID</h3>
        <p><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></p>
    </div>
    <nav>
        <ul>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li><a href="<?php echo $path_prefix . 'admin/dashboard.php'; ?>" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo $path_prefix . 'admin/monitoring.php'; ?>" class="<?php echo ($current_page == 'monitoring.php') ? 'active' : ''; ?>">Monitoring Desa</a></li>
                <li><a href="<?php echo $path_prefix . 'admin/manajemen_user.php'; ?>" class="<?php echo ($current_page == 'manajemen_user.php') ? 'active' : ''; ?>">Manajemen User</a></li>
                <li><a href="<?php echo $path_prefix . 'admin/update_web_info.php'; ?>" class="<?php echo ($current_page == 'update_web_info.php') ? 'active' : ''; ?>">Update Info Web</a></li>

            <?php else: // Menu untuk role 'desa' ?>
                <li><a href="<?php echo $path_prefix . 'desa/dashboard.php'; ?>" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard Desa</a></li>
                
                <li><a href="<?php echo $path_prefix . 'desa/grafik_desa.php'; ?>" class="<?php echo ($current_page == 'grafik_desa.php') ? 'active' : ''; ?>">Lihat Grafik</a></li>
                
                <li><a href="<?php echo $path_prefix . 'desa/download_template.php'; ?>" class="<?php echo ($current_page == 'download_template.php') ? 'active' : ''; ?>">Download Template</a></li>
            <?php endif; ?>
             
             <li><a href="<?php echo $path_prefix; ?>profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">Update Profile</a></li>
        </ul>
    </nav>
    <div class="logout-link">
        <a href="<?php echo $path_prefix; ?>logout.php" class="btn" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a>
    </div>
</aside>