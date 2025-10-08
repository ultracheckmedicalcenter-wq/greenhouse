<?php require_once '../auth.php'; ?>

<?php
require_once __DIR__ . '/../config.php';

$readings = $pdo->query("SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 20")->fetchAll();
$lastCmd = $pdo->query("SELECT * FROM commands ORDER BY id DESC LIMIT 1")->fetch();

$latest = $readings ? $readings[0] : null; // newest row

// prepare chart data
$timestamps = [];
$tempData = [];
$humData = [];
$soilData = [];
$lightData = [];

foreach (array_reverse($readings) as $r) {
    $timestamps[] = $r['created_at'];
    $tempData[] = (float)$r['temp'];
    $humData[] = (float)$r['humidity'];
    $soilData[] = (float)$r['soil_moisture'];
    $lightData[] = (float)$r['light_intensity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Greenhouse Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- AdminLTE & dependencies via CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Dashboard</a>
      </li>
      
    </ul>
    <ul class="navbar-nav ml-auto">
  <!-- Right navbar links -->
<ul class="navbar-nav ml-auto">

  <!-- User Dropdown Menu -->
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
      <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
      <span class="dropdown-header">Account</span>
      <div class="dropdown-divider"></div>
      <a href="change_password.php" class="dropdown-item">
        <i class="fas fa-key mr-2 text-primary"></i> Change Password
      </a>
      <div class="dropdown-divider"></div>
      <a href="logout.php" class="dropdown-item text-danger">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </div>
  </li>
</ul>

  
</ul>

  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <i class="fas fa-seedling brand-image img-circle elevation-3"></i>
      <span class="brand-text font-weight-light">Greenhouse</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Greenhouse Monitoring</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <!-- Summary Cards -->
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-danger">
              <span class="info-box-icon"><i class="fas fa-thermometer-half"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Temperature</span>
                <span class="info-box-number"><?= $latest ? $latest['temp']." °C" : "N/A" ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-primary">
              <span class="info-box-icon"><i class="fas fa-tint"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Humidity</span>
                <span class="info-box-number"><?= $latest ? $latest['humidity']." %" : "N/A" ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-success">
              <span class="info-box-icon"><i class="fas fa-seedling"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Soil Moisture</span>
                <span class="info-box-number"><?= $latest ? $latest['soil_moisture']." %" : "N/A" ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-warning">
              <span class="info-box-icon"><i class="fas fa-sun"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Light</span>
                <span class="info-box-number"><?= $latest ? $latest['light_intensity'] : "N/A" ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Last Command -->
        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-cogs"></i> Last Command</h3>
          </div>
          <div class="card-body">
            <?php if($lastCmd): ?>
              <p><strong>ID:</strong> <?= $lastCmd['id'] ?> | 
                 <strong>Source:</strong> <?= $lastCmd['source'] ?> | 
                 <strong>Time:</strong> <?= $lastCmd['created_at'] ?></p>
              <ul>
                <li>Heater: <?= $lastCmd['heater'] ? '<span class="badge badge-danger">ON</span>' : '<span class="badge badge-secondary">OFF</span>' ?></li>
                <li>Fan: <?= $lastCmd['fan'] ? '<span class="badge badge-info">ON</span>' : '<span class="badge badge-secondary">OFF</span>' ?></li>
                <li>Pump: <?= $lastCmd['pump'] ? '<span class="badge badge-primary">ON</span>' : '<span class="badge badge-secondary">OFF</span>' ?></li>
                <li>Light: <?= $lastCmd['light_act'] ? '<span class="badge badge-warning">ON</span>' : '<span class="badge badge-secondary">OFF</span>' ?></li>
              </ul>
            <?php else: ?>
              <p>No commands yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Manual Controls -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-sliders-h"></i> Manual Controls</h3>
          </div>
          <div class="card-body">
            <form id="manualForm">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="heater">
                <label class="form-check-label" for="heater">Heater</label>
              </div>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="fan">
                <label class="form-check-label" for="fan">Fan</label>
              </div>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="pump">
                <label class="form-check-label" for="pump">Pump</label>
              </div>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="light_act">
                <label class="form-check-label" for="light_act">Light</label>
              </div>
              <button type="button" class="btn btn-success mt-3" onclick="sendManual()">Send Manual Command</button>
            </form>
          </div>
        </div>

        <!-- Sensor Readings Table -->
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-table"></i> Recent Sensor Readings</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Temp (°C)</th>
                  <th>Humidity (%)</th>
                  <th>Soil Moisture (%)</th>
                  <th>Light</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($readings as $r): ?>
                  <tr>
                    <td><?= $r['created_at'] ?></td>
                    <td><?= $r['temp'] ?></td>
                    <td><?= $r['humidity'] ?></td>
                    <td><?= $r['soil_moisture'] ?></td>
                    <td><?= $r['light_intensity'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Charts -->
        <div class="card card-warning">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line"></i> Sensor Trends</h3>
          </div>
          <div class="card-body">
            <canvas id="sensorChart" height="100"></canvas>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">IoT Greenhouse</div>
    <strong>&copy; <?= date('Y') ?> Greenhouse System.</strong>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
const MODE_CHECK_INTERVAL = 5000;
let overrideEndTime = null;

// Mode display check
async function checkMode() {
  const res = await fetch('../api/get_command.php');
  const cmd = await res.json();

  if (cmd.source === 'manual') {
    const createdAt = new Date(cmd.created_at);
    const expires = new Date(createdAt.getTime() + 5 * 60000);
    const now = new Date();
    const diffMs = expires - now;

    if (diffMs > 0) {
      const mins = Math.floor(diffMs / 60000);
      const secs = Math.floor((diffMs % 60000) / 1000);
      document.getElementById('modeDisplay').innerHTML =
        `<span class="override-active">Manual Override Active</span> (expires in ${mins}:${secs.toString().padStart(2, '0')})`;
      document.getElementById('cancelOverrideBtn').classList.remove('d-none');
      overrideEndTime = expires;
    } else {
      document.getElementById('modeDisplay').innerHTML = `<span class="auto-mode">Auto Mode (KNN)</span>`;
      document.getElementById('cancelOverrideBtn').classList.add('d-none');
    }
  } else {
    document.getElementById('modeDisplay').innerHTML = `<span class="auto-mode">Auto Mode (KNN)</span>`;
    document.getElementById('cancelOverrideBtn').classList.add('d-none');
  }
}
setInterval(checkMode, MODE_CHECK_INTERVAL);
checkMode();

// Manual command sender
async function sendManual() {
  const data = {
    heater: document.getElementById('heater').checked ? 1 : 0,
    fan: document.getElementById('fan').checked ? 1 : 0,
    pump: document.getElementById('pump').checked ? 1 : 0,
    light_act: document.getElementById('light_act').checked ? 1 : 0
  };
  const res = await fetch('../api/manual_command.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(data)
  });
  const json = await res.json();
  if (json.status === 'ok') {
    alert('Manual override sent!');
    checkMode();
  }
}

// Cancel override
async function cancelOverride() {
  const res = await fetch('../api/save_reading.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({"temp":25,"humidity":50,"soil_moisture":50,"light_intensity":500})
  });
  await res.json();
  alert('Manual override cancelled. System back to KNN mode.');
  checkMode();
}

// ==================== LIVE CHARTS ====================

// Chart setup
const tempCtx = document.getElementById('tempHumChart').getContext('2d');
const soilCtx = document.getElementById('soilLightChart').getContext('2d');

let tempHumChart = new Chart(tempCtx, {
  type: 'line',
  data: {
    labels: [],
    datasets: [
      { label: 'Temperature (°C)', data: [], borderColor: 'red', fill: false },
      { label: 'Humidity (%)', data: [], borderColor: 'blue', fill: false }
    ]
  },
  options: { responsive: true, animation: false }
});

let soilLightChart = new Chart(soilCtx, {
  type: 'line',
  data: {
    labels: [],
    datasets: [
      { label: 'Soil Moisture (%)', data: [], borderColor: 'green', fill: false },
      { label: 'Light Intensity (lux)', data: [], borderColor: 'orange', fill: false }
    ]
  },
  options: { responsive: true, animation: false }
});

// Fetch and update chart data
async function updateCharts() {
  const res = await fetch('../api/latest_readings.php');
  const data = await res.json();

  const labels = data.map(d => d.created_at.substring(11, 16));
  const temps = data.map(d => parseFloat(d.temp));
  const hums = data.map(d => parseFloat(d.humidity));
  const soils = data.map(d => parseFloat(d.soil_moisture));
  const lights = data.map(d => parseFloat(d.light_intensity));

  tempHumChart.data.labels = labels;
  tempHumChart.data.datasets[0].data = temps;
  tempHumChart.data.datasets[1].data = hums;
  tempHumChart.update();

  soilLightChart.data.labels = labels;
  soilLightChart.data.datasets[0].data = soils;
  soilLightChart.data.datasets[1].data = lights;
  soilLightChart.update();
}

updateCharts();
setInterval(updateCharts, 10000); // every 10 seconds
</script>

</body>
</html>
