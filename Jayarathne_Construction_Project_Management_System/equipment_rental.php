<?php
// Start session or handle database connection here if needed
// include('db_connection.php');

// Sample Equipment Data Array (You can replace this with a SQL query loop later)
$equipments = [
    [
        'id' => 1,
        'name' => 'CAT 320 Hydraulic Excavator',
        'category' => 'Heavy Machinery',
        'rate' => 250,
        'status' => 'Available',
        'badge' => 'bg-success',
        'image' => 'https://images.unsplash.com/photo-1579412690850-bd41cd0af397?auto=format&fit=crop&w=600&q=80',
        'specs' => '20 Ton Capacity • Diesel'
    ],
    [
        'id' => 2,
        'name' => 'JCB 3CX Backhoe Loader',
        'category' => 'Heavy Machinery',
        'rate' => 180,
        'status' => 'Available',
        'badge' => 'bg-success',
        'image' => 'https://images.unsplash.com/photo-1581094288338-2314dddb7ece?auto=format&fit=crop&w=600&q=80',
        'specs' => '74 HP • 4WD'
    ],
    [
        'id' => 3,
        'name' => 'Komatsu Tower Crane',
        'category' => 'Cranes',
        'rate' => 450,
        'status' => 'Rented',
        'badge' => 'bg-danger',
        'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=600&q=80',
        'specs' => '50m Max Reach • 8 Ton'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Rental - Jayarathne Construction</title>
    
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Embed Styles Directly in Header -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Metric Cards Accent Borders */
        .card-stat {
            border: none;
            border-left: 5px solid;
            border-radius: 8px;
        }
        .stat-available { border-left-color: #198754; }
        .stat-rented { border-left-color: #dc3545; }
        .stat-maintenance { border-left-color: #ffc107; }

        /* Equipment Cards Styling */
        .equipment-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .equipment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12) !important;
        }
        
        .equipment-img-container {
            height: 200px;
            overflow: hidden;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            position: relative;
        }
        .equipment-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-4">

    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fa-solid fa-truck-monster text-primary me-2"></i>Equipment Rental Catalog</h2>
            <p class="text-muted small mb-0">Browse, manage, and assign construction machinery to active project sites.</p>
        </div>
        <button class="btn btn-primary fw-bold"><i class="fa-solid fa-plus me-2"></i>Add New Machinery</button>
    </div>

    <!-- Summary Metrics Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat stat-available shadow-sm p-3 bg-white">
                <span class="text-muted small fw-bold">AVAILABLE FOR RENT</span>
                <h3 class="fw-bold text-success mb-0">2 Equipment</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat stat-rented shadow-sm p-3 bg-white">
                <span class="text-muted small fw-bold">ON RENTAL / DEPLOYED</span>
                <h3 class="fw-bold text-danger mb-0">1 Equipment</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat stat-maintenance shadow-sm p-3 bg-white">
                <span class="text-muted small fw-bold">UNDER MAINTENANCE</span>
                <h3 class="fw-bold text-warning mb-0">0 Units</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search machine name, spec, or category...">
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active">All</button>
                <button type="button" class="btn btn-outline-primary">Heavy Machinery</button>
                <button type="button" class="btn btn-outline-primary">Cranes</button>
                <button type="button" class="btn btn-outline-primary">Vehicles</button>
            </div>
        </div>
    </div>

    <!-- Equipment Cards Grid -->
    <div class="row g-4">
        <?php foreach ($equipments as $item): ?>
            <div class="col-md-4">
                <div class="card equipment-card h-100 shadow-sm">
                    <!-- Image & Status Badge -->
                    <div class="equipment-img-container">
                        <img src="<?php echo $item['image']; ?>" class="equipment-img" alt="<?php echo $item['name']; ?>">
                        <span class="badge <?php echo $item['badge']; ?> status-badge px-3 py-2 rounded-pill"><?php echo $item['status']; ?></span>
                    </div>

                    <!-- Card Body Details -->
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-light text-secondary border w-auto align-self-start mb-2"><?php echo $item['category']; ?></span>
                        <h5 class="fw-bold text-dark mb-1"><?php echo $item['name']; ?></h5>
                        <p class="text-muted small mb-3"><i class="fa-solid fa-gears me-1"></i><?php echo $item['specs']; ?></p>

                        <!-- Price Tag & Rental Action Button -->
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-4 fw-bold text-primary">$<?php echo $item['rate']; ?></span>
                                <small class="text-muted"> / day</small>
                            </div>
                            <?php if ($item['status'] === 'Available'): ?>
                                <button class="btn btn-primary fw-bold px-3" data-bs-toggle="modal" data-bs-target="#rentalModal" onclick="setRentalData('<?php echo $item['name']; ?>', <?php echo $item['rate']; ?>)">
                                    <i class="fa-solid fa-key me-1"></i> Rent Now
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary fw-bold px-3" disabled>Currently Rented</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Booking Action Modal Dialog -->
<div class="modal fade" id="rentalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-check me-2"></i>Create Equipment Rental Agreement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">SELECTED EQUIPMENT</label>
                        <input type="text" id="modalEquipmentName" class="form-control fw-bold bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">ASSIGN TO PROJECT</label>
                        <select class="form-select" required>
                            <option value="" selected disabled>Select active construction project...</option>
                            <option value="1">Colombo Port Expansion Project</option>
                            <option value="2">Kandy Highway Development</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">START DATE</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">END DATE</label>
                            <input type="date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Confirm & Reserve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Pass card details directly into the modal popup dynamically
    function setRentalData(name, rate) {
        document.getElementById('modalEquipmentName').value = name + ' ($' + rate + '/day)';
    }
</script>

</body>
</html>