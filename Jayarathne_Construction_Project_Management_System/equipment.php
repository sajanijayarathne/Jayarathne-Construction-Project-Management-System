<?php
// File Path: equipment.php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Equipment
if (isset($_POST['add_equipment'])) {
    $name   = $_POST['equipment_name'];
    $serial = $_POST['serial_number'];
    $cat    = $_POST['category'];
    $rate   = $_POST['daily_rate'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO equipment (equipment_name, serial_number, category, daily_rate, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $name, $serial, $cat, $rate, $status);
    $stmt->execute();
    header("Location: equipment.php?msg=added");
    exit();
}

// Fetch All Equipment
$result = $conn->query("SELECT * FROM equipment ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment Management - Jayarathne Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .sidebar { width: 250px; background-color: #1e2530; min-height: 100vh; color: #a6b0cf; }
        .sidebar .brand { padding: 18px; font-weight: 700; color: #ffffff; border-bottom: 1px solid #273142; }
        .sidebar .nav-link { color: #a6b0cf; padding: 10px 20px; font-size: 0.9rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { color: #fff; background-color: #2b3548; }
        .main-content { flex: 1; }
    </style>
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="brand"><i class="fa-solid fa-truck-monster text-warning me-2"></i> Jayarathne</div>
        <div class="nav flex-column my-2">
            <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="projects.php" class="nav-link"><i class="fa-solid fa-city me-2"></i> Projects</a>
            <a href="equipment.php" class="nav-link active"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Equipment</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-screwdriver-wrench me-2 text-primary"></i>Equipment Inventory</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                <i class="fa-solid fa-plus me-1"></i> Add Equipment
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-12 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Equipment Name</th>
                            <th>Serial Number</th>
                            <th>Category</th>
                            <th>Daily Rate (LKR)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><code><?php echo htmlspecialchars($row['serial_number']); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td>LKR <?php echo number_format($row['daily_rate'], 2); ?></td>
                                    <td>
                                        <?php 
                                            $b = 'bg-success';
                                            if($row['status'] == 'Rented') $b = 'bg-primary';
                                            if($row['status'] == 'Maintenance') $b = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?php echo $b; ?>"><?php echo $row['status']; ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">No equipment found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Add Equipment -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Equipment Name</label>
                        <input type="text" name="equipment_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Heavy Machinery" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Daily Rate (LKR)</label>
                            <input type="number" step="0.01" name="daily_rate" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="Available">Available</option>
                            <option value="Rented">Rented</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_equipment" class="btn btn-primary">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>