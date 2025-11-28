<?php
require_once '../includes/db_connect.php';

// Keamanan & ambil data awal
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php"); exit();
}
$id_desa = $_SESSION['id_desa'];
$stmt = $db->prepare("SELECT nama_desa FROM desa WHERE id_desa = ?");
$stmt->execute([$id_desa]);
$nama_desa = $stmt->fetchColumn();

// Ambil riwayat upload dokumen
$stmt_history_upload = $db->prepare("SELECT id_upload, bulan, tahun, tipe_dokumen, nama_file, tgl_upload FROM dokumen_uploads WHERE id_desa = ? ORDER BY tahun DESC, bulan DESC");
$stmt_history_upload->execute([$id_desa]);
$upload_history = $stmt_history_upload->fetchAll(PDO::FETCH_ASSOC);

// Ambil riwayat input data
$stmt_history_data = $db->prepare("SELECT * FROM data_penduduk WHERE id_desa = ? ORDER BY tahun DESC, bulan DESC");
$stmt_history_data->execute([$id_desa]);
$data_history = $stmt_history_data->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Desa - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .accordion-toggle { cursor: pointer; display: block; padding: 15px; background-color: var(--surface-white); border-radius: var(--border-radius-md); margin-bottom: 20px; font-weight: 600; color: var(--text-dark-slate); transition: background-color 0.3s; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .accordion-toggle:hover { background-color: var(--light-accent-bg); color: var(--accent-indigo-dark); }
        .accordion-content { display: none; padding: 0; margin-top: -20px; margin-bottom: 20px; }
        .accordion-content .card { margin: 0; box-shadow: none; border-top: none; border-radius: 0 0 var(--border-radius-md) var(--border-radius-md); }
        .action-buttons a { margin-right: 5px; }
        .btn-edit { display: inline-block; padding: 4px 10px; font-size: 0.85em; text-align: center; text-decoration: none; color: var(--accent-indigo-dark); background-color: var(--light-accent-bg); border: 1px solid var(--accent-indigo); border-radius: 6px; transition: all 0.2s; }
        .btn-edit:hover { background-color: var(--accent-indigo); color: #fff; }
        .btn-hapus { display: inline-block; padding: 4px 10px; font-size: 0.85em; text-align: center; text-decoration: none; color: #991b1b; background-color: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; transition: all 0.2s; }
        .btn-hapus:hover { background-color: #b91c1c; color: #fff; }
        .table-responsive { overflow-x: auto; }
        table th, table td { white-space: nowrap; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>
    <div class="content">
        <div class="content-header">
            <h1>Dashboard <?php echo htmlspecialchars($nama_desa); ?></h1>
            <p>Selamat datang! Kelola data kependudukan dan laporan desa Anda di sini.</p>
        </div>

        <?php if (isset($_SESSION['notification'])): ?>
            <div class="notification <?php echo htmlspecialchars($_SESSION['notification_type']); ?>"><?php echo htmlspecialchars($_SESSION['notification']); ?></div>
            <?php unset($_SESSION['notification'], $_SESSION['notification_type']); ?>
        <?php endif; ?>

        <h3 class="accordion-toggle" onclick="toggleAccordion('form-input-data')">▶ Input Data Kependudukan Bulanan</h3>
        <div id="form-input-data" class="accordion-content">
             <div class="card">
                <form action="proses_input_data.php" method="POST" class="dashboard-form">
                    <div class="form-group"><label>Bulan & Tahun Laporan</label><select name="bulan" required><?php for($i = 1; $i <= 12; $i++) echo "<option value='$i'>" . strftime('%B', mktime(0, 0, 0, $i, 10)) . "</option>"; ?></select><input type="number" name="tahun" placeholder="Ketik Tahun" value="<?php echo date('Y'); ?>" required min="2020"></div>
                    <div class="form-group"><label>Penduduk Bulan Lalu</label><input type="number" name="penduduk_bulan_lalu" required placeholder="0"></div>
                    <div class="form-group"><label>Lahir</label><input type="number" name="lahir" required placeholder="0"></div>
                    <div class="form-group"><label>Mati</label><input type="number" name="mati" required placeholder="0"></div>
                    <div class="form-group"><label>Pindah</label><input type="number" name="pindah" required placeholder="0"></div>
                    <div class="form-group"><label>Datang</label><input type="number" name="datang" required placeholder="0"></div>
                    <div class="form-group"><label>Penduduk Bulan Ini</label><input type="number" name="penduduk_bulan_ini" required placeholder="0"></div>
                    <div class="form-group"><label>Kartu Keluarga</label><input type="number" name="kartu_keluarga" required placeholder="0"></div>
                    <div class="form-group"><label>KTP</label><input type="number" name="ktp" required placeholder="0"></div>
                    <button type="submit" class="btn" style="width: auto;">Simpan Data</button>
                </form>
            </div>
        </div>

        <h3 class="accordion-toggle" onclick="toggleAccordion('form-upload-dokumen')">▶ Upload Dokumen Laporan</h3>
        <div id="form-upload-dokumen" class="accordion-content">
            <div class="card">
                <form action="upload_dokumen.php" method="POST" enctype="multipart/form-data" class="dashboard-form">
                    <div class="form-group"><label>Bulan & Tahun Laporan</label><select name="bulan" required><?php for($i = 1; $i <= 12; $i++) echo "<option value='$i'>" . strftime('%B', mktime(0, 0, 0, $i, 10)) . "</option>"; ?></select><input type="number" name="tahun" placeholder="Ketik Tahun" value="<?php echo date('Y'); ?>" required min="2020"></div>
                    <div class="form-group"><label>Jenis Laporan</label><select name="tipe_dokumen" required><option value="">-- Pilih Jenis --</option><option value="perkembangan_penduduk">Data Perkembangan Penduduk (.xlsx)</option><option value="kelompok_umur">Laporan Kelompok Umur (.docx)</option></select></div>
                    <div class="form-group"><label>Pilih File</label><input type="file" name="dokumen_laporan" required accept=".xlsx,.docx"></div>
                    <button type="submit" class="btn" style="width: auto;">Upload Dokumen</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <h2>Riwayat Upload Dokumen</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr><th>Periode</th><th>Jenis Dokumen</th><th>Nama File</th><th>Tgl Upload</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upload_history)): ?>
                            <tr><td colspan="5" style="text-align:center;">Belum ada riwayat.</td></tr>
                        <?php else: foreach ($upload_history as $row): ?>
                            <tr>
                                <td><?php echo strftime('%B %Y', strtotime("{$row['tahun']}-{$row['bulan']}-01")); ?></td>
                                <td><?php echo ($row['tipe_dokumen'] == 'perkembangan_penduduk' ? 'Perkembangan Penduduk' : 'Kelompok Umur'); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_file']); ?></td>
                                <td><?php echo strftime('%d-%m-%Y %H:%M', strtotime($row['tgl_upload'])); ?></td>
                                <td class="action-buttons">
                                    <a href="edit_upload.php?id_upload=<?php echo $row['id_upload']; ?>" class="btn-edit">Ganti</a>
                                    <a href="hapus_dokumen.php?id_upload=<?php echo $row['id_upload']; ?>" class="btn-hapus" onclick="return confirm('Anda yakin ingin menghapus dokumen ini secara permanen?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>Riwayat Input Data Kependudukan</h2>
             <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Pddk. Lalu</th>
                            <th>Lahir</th>
                            <th>Mati</th>
                            <th>Pindah</th>
                            <th>Datang</th>
                            <th>Pddk. Kini</th>
                            <th>KK</th>
                            <th>KTP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if (empty($data_history)): ?>
                            <tr><td colspan="10" style="text-align:center;">Belum ada riwayat input data.</td></tr>
                        <?php else: foreach ($data_history as $row): ?>
                            <tr>
                                <td><?php echo strftime('%B %Y', strtotime("{$row['tahun']}-{$row['bulan']}-01")); ?></td>
                                <td><?php echo number_format($row['penduduk_bulan_lalu']); ?></td>
                                <td><?php echo $row['lahir']; ?></td>
                                <td><?php echo $row['mati']; ?></td>
                                <td><?php echo $row['pindah']; ?></td>
                                <td><?php echo $row['datang']; ?></td>
                                <td><strong><?php echo number_format($row['penduduk_bulan_ini']); ?></strong></td>
                                <td><?php echo $row['kartu_keluarga']; ?></td>
                                <td><?php echo $row['ktp']; ?></td>
                                <td class="action-buttons">
                                    <a href="edit_data.php?id_data=<?php echo $row['id_data']; ?>" class="btn-edit">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAccordion(id) {
        var content = document.getElementById(id);
        var toggle = content.previousElementSibling;
        if (content.style.display === "block") {
            content.style.display = "none";
            toggle.innerHTML = '▶ ' + toggle.innerHTML.substring(2);
        } else {
            content.style.display = "block";
            toggle.innerHTML = '▼ ' + toggle.innerHTML.substring(2);
        }
    }
</script>

</body>
</html>