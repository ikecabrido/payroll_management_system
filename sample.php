<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../controllers/PayslipController.php';
require_once __DIR__ . '/../controllers/PayrollController.php';

// Auth check
$auth = new Auth();
if (!$auth->check()) {
    header("Location: index.php");
    exit;
}

$payslipController = new PayslipController();
$payrollController = new PayrollController();

// Filters
$periodId   = $_GET['period_id'] ?? null;
$employeeId = $_GET['employee_id'] ?? null;

$periodId   = $periodId ? (int)$periodId : null;
$employeeId = $employeeId ? (int)$employeeId : null;

// Data
$periods  = $payrollController->getPeriods();
$employees = $payrollController->getEmployees(); // Make sure this exists
$payslips = $payslipController->index($periodId, $employeeId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payslips | Payroll System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include 'partials/navbar.php'; ?>

        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">

            <!-- Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <h1>Payslips</h1>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <!-- Filters -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                <i class="fas fa-filter"></i> Filters
                            </h3>
                        </div>

                        <div class="card-body">
                            <form method="GET" class="row">

                                <div class="col-md-4">
                                    <label>Payroll Period</label>
                                    <select name="period_id" class="form-control">
                                        <option value="">All Periods</option>
                                        <?php foreach ($periods as $p): ?>
                                            <option value="<?= $p['id'] ?>"
                                                <?= ($periodId == $p['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['period_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Employee</label>
                                    <select name="employee_id" class="form-control">
                                        <option value="">All Employees</option>
                                        <?php foreach ($employees as $e): ?>
                                            <option value="<?= $e['id'] ?>"
                                                <?= ($employeeId == $e['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($e['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Apply
                                    </button>

                                    <a href="payslips.php" class="btn btn-secondary">
                                        <i class="fas fa-sync"></i> Reset
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card mt-3">

                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-receipt"></i> Payslip Records
                            </h3>
                        </div>

                        <div class="card-body">
                            <table id="payslipTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Period</th>
                                        <th>Gross Pay</th>
                                        <th>Deductions</th>
                                        <th>Net Pay</th>
                                        <th>Date Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php if (empty($payslips)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No payslips found
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($payslips as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['name']) ?></td>
                                            <td><?= htmlspecialchars($p['period_name']) ?></td>
                                            <td>₱<?= number_format($p['gross_pay'], 2) ?></td>
                                            <td>₱<?= number_format($p['total_deductions'], 2) ?></td>
                                            <td>
                                                <strong>
                                                    ₱<?= number_format($p['net_pay'], 2) ?>
                                                </strong>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($p['generated_at'])) ?></td>
                                            <td>
                                                <a href="view_payslip.php?id=<?= $p['id'] ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="print_payslip.php?id=<?= $p['id'] ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </section>

        </div>

        <!-- Footer -->
        <?php include 'partials/footer.php'; ?>

    </div>

    <!-- Scripts -->
    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/dist/js/adminlte.min.js"></script>

    <script>
        $(function() {
            $('#payslipTable').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [5, 'desc']
                ]
            });
        });
    </script>

</body>

</html>