<?php
require_once '../includes/db_connect.php';

// Keamanan: Cek sesi login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Mengambil daftar tahun unik dari kedua tabel
$years_stmt = $db->query("
    (SELECT DISTINCT tahun FROM data_penduduk)
    UNION
    (SELECT DISTINCT tahun FROM dokumen_uploads)
    ORDER BY tahun DESC
");
$available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($available_years)) {
    $available_years[] = date('Y');
}

// Logika filter
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : $available_years[0];

// Query utama untuk mengambil semua data lengkap
$sql = "
    SELECT 
        d.id_desa, 
        d.nama_desa,
        p.penduduk_bulan_lalu, p.lahir, p.mati, p.pindah, p.datang, 
        p.penduduk_bulan_ini, p.kartu_keluarga, p.ktp,
        (SELECT id_upload FROM dokumen_uploads WHERE id_desa = d.id_desa AND tipe_dokumen = 'perkembangan_penduduk' AND bulan = :bulan AND tahun = :tahun ORDER BY tgl_upload DESC LIMIT 1) as id_upload_perkembangan,
        (SELECT nama_file FROM dokumen_uploads WHERE id_desa = d.id_desa AND tipe_dokumen = 'perkembangan_penduduk' AND bulan = :bulan AND tahun = :tahun ORDER BY tgl_upload DESC LIMIT 1) as file_perkembangan,
        (SELECT id_upload FROM dokumen_uploads WHERE id_desa = d.id_desa AND tipe_dokumen = 'kelompok_umur' AND bulan = :bulan AND tahun = :tahun ORDER BY tgl_upload DESC LIMIT 1) as id_upload_kelompok,
        (SELECT nama_file FROM dokumen_uploads WHERE id_desa = d.id_desa AND tipe_dokumen = 'kelompok_umur' AND bulan = :bulan AND tahun = :tahun ORDER BY tgl_upload DESC LIMIT 1) as file_kelompok_umur
    FROM desa d
    LEFT JOIN data_penduduk p ON d.id_desa = p.id_desa AND p.bulan = :bulan AND p.tahun = :tahun
    ORDER BY d.nama_desa ASC
";
$stmt = $db->prepare($sql);
$stmt->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$desa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Laporan Desa - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .no-data { color: var(--text-secondary-slate); font-style: italic; font-size: 0.9em; }
        .table-responsive { overflow-x: auto; }
        table th, table td { white-space: nowrap; }
        table th[rowspan="2"] { vertical-align: middle; }

        .download-link {
            display: inline-block;
            padding: 6px 12px;
            font-size: 0.9em;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            color: var(--accent-indigo-dark);
            background-color: var(--light-accent-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            transition: all var(--transition-speed);
        }
        .download-link:hover {
            background-color: var(--accent-indigo);
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        /* Menambahkan ikon unduh sebelum teks */
        .download-link::before {
            content: '↓ ';
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>
    <div class="content">
        <div class="content-header">
            <h1>Monitoring Laporan Desa</h1>
            <p>Pantau data kependudukan dan status upload dokumen dari setiap desa.</p>
        </div>

        <div class="card">
            <h2>Filter Data</h2>
            <form method="GET" action="" class="filter-form">
                <select name="bulan">
                    <?php for($i=1; $i<=12; $i++) echo "<option value='$i' ".($i==$bulan?'selected':'').">".strftime('%B', mktime(0,0,0,$i,10))."</option>"; ?>
                </select>
                <select name="tahun">
                    <?php foreach ($available_years as $year_option): ?>
                        <option value="<?php echo $year_option; ?>" <?php echo ($year_option == $tahun ? 'selected' : ''); ?>>
                            <?php echo $year_option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">Filter</button>
            </form>
        </div>

        <div class="card">
            <h2>Laporan Desa - Periode <?php echo strftime('%B', mktime(0,0,0,$bulan,10)) . " " . $tahun; ?></h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Nama Desa</th>
                            <th colspan="8">Data Kependudukan (Input Angka)</th>
                            <th colspan="2">Status Dokumen (Upload File)</th>
                        </tr>
                        <tr>
                            <th>Pddk. Lalu</th><th>Lahir</th><th>Mati</th><th>Pindah</th><th>Datang</th><th>Pddk. Kini</th><th>KK</th><th>KTP</th>
                            <th>Perkembangan Penduduk</th><th>Kelompok Umur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($desa_list as $desa): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($desa['nama_desa']); ?></td>
                            <td><?php echo isset($desa['penduduk_bulan_lalu']) ? number_format($desa['penduduk_bulan_lalu']) : '<span class="no-data">-</span>'; ?></td>
                            <td><?php echo $desa['lahir'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td><?php echo $desa['mati'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td><?php echo $desa['pindah'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td><?php echo $desa['datang'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td><strong><?php echo isset($desa['penduduk_bulan_ini']) ? number_format($desa['penduduk_bulan_ini']) : '<span class="no-data">-</span>'; ?></strong></td>
                            <td><?php echo $desa['kartu_keluarga'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td><?php echo $desa['ktp'] ?? '<span class="no-data">-</span>'; ?></td>
                            <td>
                                <?php if ($desa['file_perkembangan']): ?>
                                    <a href="download_dokumen.php?id_upload=<?php echo $desa['id_upload_perkembangan']; ?>" class="download-link">Unduh</a>
                                <?php else: ?>
                                    <span class="no-data">Belum Upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                 <?php if ($desa['file_kelompok_umur']): ?>
                                    <a href="download_dokumen.php?id_upload=<?php echo $desa['id_upload_kelompok']; ?>" class="download-link">Unduh</a>
                                <?php else: ?>
                                    <span class="no-data">Belum Upload</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>