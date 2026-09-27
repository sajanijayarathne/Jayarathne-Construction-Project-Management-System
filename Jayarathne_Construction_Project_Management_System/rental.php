<?php
// File Path: rentals.php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Rental Request
if (isset($_POST['add_rental'])) {
    $req_id   = "REQ-" . rand(1000, 9999);
    $equip_id = $_POST['equipment_id'];
    $proj_id  = $_POST['project_id'];
    $start    = $_POST['start_date'];
    $end      = $_POST['end_date'];
    $status   = "Pending";

    $stmt = $conn->prepare("INSERT INTO rentals (request_id, equipment_id, project_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisss", $req_id, $equip_id, $proj_id, $start, $end, $status);
    $stmt->execute();
    
    header("Location: rentals.php?msg=added");
    exit();
}

// Fetch Rentals with Joined Equipment & Project Data
$rentals = $conn->query("
    SELECT r.*, e.equipment_name, p.project_name 
    FROM rentals r
    JOIN equipment e ON r.equipment_id = e.id
    JOIN projects p ON r.project_id = p.id
    ORDER BY r.created_at DESC
");

$equipment_list = $conn->query("SELECT id, equipment_name FROM equipment WHERE status = 'Available'");
$project_list   = $conn->query("SELECT id, project_name FROM projects WHERE status != 'Completed'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rentals - Jayarathne Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { width: 250px; background-color: #1e2530; min-height: 100vh; color: #a6b0cf; }
        .sidebar .brand { padding: 18px; font-weight: 700; color: #fff; border-bottom: 1px solid #273142; }
        .sidebar .nav-link { color: #a6b0cf; padding: 10px 20px; font-size: 0.9rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { color: #fff; background-color: #2b3548; }
        .main-content { flex: 1; }
    </style>
</head>
<body class="d-flex">

    <div class="sidebar d-flex flex-column">
        <div class="brand"><i class="fa-solid fa-truck-monster text-warning me-2"></i> Jayarathne</div>
        <div class="nav flex-column my-2">
            <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="projects.php" class="nav-link"><i class="fa-solid fa-city me-2"></i> Projects</a>
            <a href="equipment.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Equipment</a>
            <a href="rentals.php" class="nav-link active"><i class="fa-solid fa-truck-ramp-box me-2"></i> Equipment Rental</a>
        </div>
    </div>

    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-truck-ramp-box me-2 text-primary"></i>Rental Requests</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRentalModal">
                <i class="fa-solid fa-plus me-1"></i> New Rental Request
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-12 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Request ID</th>
                            <th>Equipment</th>
                            <th>Assigned Project</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rentals && $rentals->num_rows > 0): ?>
                            <?php while($row = $rentals->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo $row['request_id']; ?></code></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                    <td><?php echo $row['start_date']; ?></td>
                                    <td><?php echo $row['end_date']; ?></td>
                                    <td>
                                        <?php 
                                            $b = 'bg-warning text-dark';
                                            if($row['status'] == 'Approved') $b = 'bg-success';
                                            if($row['status'] == 'Returned') $b = 'bg-secondary';
                                        ?>
                                        <span class="badge <?php echo $b; ?>"><?php echo $row['status']; ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">No rental requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addRentalModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Rental Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Equipment</label>
                        <select name="equipment_id" class="form-select" required>
                            <?php while($e = $equipment_list->fetch_assoc()): ?>
                                <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['equipment_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Target Project</label>
                        <select name="project_id" class="form-select" required>
                            <?php while($p = $project_list->fetch_assoc()): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['project_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_rental" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>