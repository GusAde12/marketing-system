<?php include "head.php"; 
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<script type="text/javascript">
    document.title="Dashboard";
    document.getElementById('dash').classList.add('active');
</script>
<?php
// Ambil 3 barang paling laku berdasarkan jumlah pembelian dari sub_transaksi
$query = $root->con->query("SELECT barang.nama_barang, SUM(sub_transaksi.jumlah_beli) AS total_jual 
    FROM sub_transaksi 
    JOIN barang ON sub_transaksi.id_barang = barang.id_barang 
    GROUP BY sub_transaksi.id_barang 
    ORDER BY total_jual DESC 
    LIMIT 3");

if (!$query) {
    die("Query error: " . $root->con->error);
}

$labels = [];
$values = [];

while ($row = $query->fetch_assoc()) {
    $labels[] = $row['nama_barang'];
    $values[] = $row['total_jual'];
}
?>

<style>
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    
    .card-icon {
        font-size: 24px;
        margin-right: 10px;
        color: #4e73df;
    }
    
    .card-title {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .card-value {
        font-size: 24px;
        font-weight: bold;
        color: #5a5c69;
    }
    
    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: bold;
        color: #5a5c69;
        margin-left: 10px;
    }
    
    .chart-wrapper {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-admin {
        background-color: #4e73df;
        color: white;
    }
    
    .status-manager {
        background-color: #1cc88a;
        color: white;
    }
</style>

<div class="content">
    <div class="padding">
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="card-title"><i class="fa fa-user card-icon"></i>Login sebagai</div>
                <div class="card-value">
                    <span class="status-badge <?= $_SESSION['status']==1 ? 'status-admin' : 'status-manager' ?>">
                        <?= $_SESSION['status']==1 ? 'Admin' : 'Pemilik Toko' ?>
                    </span>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title"><i class="fa fa-clock-o card-icon"></i>Waktu</div>
                <div class="card-value"><?= date("d-m-Y") ?></div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title"><i class="fa fa-bars card-icon"></i>Data Barang</div>
                <div class="card-value"><?= $root->show_jumlah_barang() ?></div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title"><i class="fa fa-book card-icon"></i>Laporan</div>
                <div class="card-value"><?= $root->show_jumlah_trans2() ?></div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-header">
                <i class="fa fa-line-chart card-icon"></i>
                <h3 class="chart-title">3 Barang Paling Laku</h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="grafikBarang" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikBarang').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Jumlah Terjual',
                data: <?= json_encode($values) ?>,
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)'
                ],
                borderColor: [
                    'rgba(78, 115, 223, 1)',
                    'rgba(28, 200, 138, 1)',
                    'rgba(54, 185, 204, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        display: true,
                        color: "rgba(0, 0, 0, 0.05)"
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

<?php include "foot.php" ?>