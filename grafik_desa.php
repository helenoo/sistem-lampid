<?php
require_once '../includes/db_connect.php';

// Keamanan & ambil data awal
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php"); exit();
}
$id_desa = $_SESSION['id_desa'];
$stmt_nama_desa = $db->prepare("SELECT nama_desa FROM desa WHERE id_desa = ?");
$stmt_nama_desa->execute([$id_desa]);
$nama_desa = $stmt_nama_desa->fetchColumn();

// Ambil data untuk grafik spesifik desa ini
$chart_data_stmt = $db->prepare("
    SELECT 
        SUM(lahir) as total_lahir, SUM(mati) as total_mati,
        SUM(pindah) as total_pindah, SUM(datang) as total_datang
    FROM data_penduduk
    WHERE id_desa = ?
");
$chart_data_stmt->execute([$id_desa]);
$chart_data = $chart_data_stmt->fetch(PDO::FETCH_ASSOC);

$labels = ["Lahir", "Mati", "Pindah", "Datang"];
$data_values = $chart_data ? array_values($chart_data) : array_fill(0, count($labels), 0);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Desa - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>
    <div class="content">
        <div class="content-header">
            <h1>Grafik Akumulasi Data</h1>
            <p>Ini adalah rekapitulasi data keseluruhan untuk <strong><?php echo htmlspecialchars($nama_desa); ?></strong>.</p>
        </div>

        <div class="card">
            <h2>Grafik Akumulasi Data Kependudukan</h2>
            <canvas id="desaChart"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('desaChart').getContext('2d');
    const desaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Total Akumulasi Data di <?php echo htmlspecialchars($nama_desa); ?>',
                data: <?php echo json_encode($data_values); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { color: '#1e293b' } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#64748b' }, grid: { color: '#e2e8f0' } },
                x: { ticks: { color: '#64748b' }, grid: { display: false } }
            }
        }
    });
</script>

</body>
</html>