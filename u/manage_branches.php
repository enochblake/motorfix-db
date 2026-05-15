<?php
/**
 * Motor Fix - Super Admin Branch Management
 *
 * Allows super admins to Create, Read, Update, and manage company branches.
 *
 * @version 1.0
 * @date Tuesday, October 14, 2025
 */
require_once 'ic/sheader.php'; // Includes session, db, error reporting

// Initialize variables
$feedback_message = '';
$feedback_type = '';
$branches = [];
$editing_branch = null;

// Handle session feedback messages
if (isset($_SESSION['feedback_message'])) {
    $feedback_message = $_SESSION['feedback_message'];
    $feedback_type = $_SESSION['feedback_type'] ?? 'error';
    unset($_SESSION['feedback_message'], $_SESSION['feedback_type']);
}

// --- FORM PROCESSING for Add/Edit Branch ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $maps_pin = trim($_POST['maps_pin']);
    $contact_name = trim($_POST['contact_person_name']);
    $contact_phone = trim($_POST['contact_person_phone']);
    $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;

    if (empty($name) || empty($address)) {
        $_SESSION['feedback_message'] = "Branch Name and Address are required.";
        $_SESSION['feedback_type'] = 'error';
    } else {
        try {
            if ($branch_id) {
                // Update existing branch
                $stmt = $pdo->prepare("UPDATE branches SET name = ?, address = ?, maps_pin = ?, contact_person_name = ?, contact_person_phone = ? WHERE id = ?");
                $stmt->execute([$name, $address, $maps_pin, $contact_name, $contact_phone, $branch_id]);
                $_SESSION['feedback_message'] = "Branch updated successfully.";
            } else {
                // Insert new branch
                $stmt = $pdo->prepare("INSERT INTO branches (name, address, maps_pin, contact_person_name, contact_person_phone) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $address, $maps_pin, $contact_name, $contact_phone]);
                $_SESSION['feedback_message'] = "New branch created successfully.";
            }
            $_SESSION['feedback_type'] = 'success';
        } catch (PDOException $e) {
            error_log("Branch management error: " . $e->getMessage());
            $_SESSION['feedback_message'] = "A database error occurred. Operation failed.";
            $_SESSION['feedback_type'] = 'error';
        }
    }
    header("Location: manage_branches.php");
    exit();
}

// --- DATA FETCHING for Page Display ---
try {
    $branches = $pdo->query("SELECT * FROM branches ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $feedback_message = "Error fetching branch data: " . $e->getMessage();
    $feedback_type = 'error';
}

?>
<main class="mf-main-content">
    <div class="mf-page-title">
        <h1>Branch Management</h1>
        <p>Add, view, and edit your company's physical locations.</p>
    </div>

    <?php if ($feedback_message): ?>
    <div class="mf-feedback-message <?php echo $feedback_type; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
    <?php endif; ?>

    <div class="mf-content-panel">
        <div class="mf-panel-header">
            <h2><i class="fas fa-store-alt"></i> All Branches</h2>
            <button class="mf-button-primary" onclick="openBranchModal()">+ Add New Branch</button>
        </div>
        <div class="mf-table-responsive">
            <table class="mf-table">
                <thead><tr><th>Branch Name</th><th>Address</th><th>Contact Person</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($branches)): ?>
                    <tr><td colspan="4">No branches have been created yet.</td></tr>
                    <?php else: foreach ($branches as $branch): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($branch['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($branch['address']); ?></td>
                        <td><?php echo htmlspecialchars($branch['contact_person_name'] ?? 'N/A'); ?><br><small><?php echo htmlspecialchars($branch['contact_person_phone'] ?? ''); ?></small></td>
                        <td>
                            <button class="mf-button-secondary" onclick='openBranchModal(<?php echo json_encode($branch, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Edit</button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add/Edit Branch Modal -->
<div id="branchModal" class="mf-modal">
    <div class="mf-modal-content">
        <span class="mf-modal-close" onclick="closeBranchModal()">&times;</span>
        <h2 id="modalTitle">Add New Branch</h2>
        <form id="branchForm" action="manage_branches.php" method="POST">
            <input type="hidden" id="branchId" name="branch_id">
            <div class="form-group"><label for="branchName">Branch Name</label><input type="text" id="branchName" name="name" required></div>
            <div class="form-group"><label for="branchAddress">Address</label><textarea id="branchAddress" name="address" rows="3" required></textarea></div>
            <div class="form-group"><label for="mapsPin">Google Maps Pin URL</label><input type="url" id="mapsPin" name="maps_pin"></div>
            <div class="form-group"><label for="contactName">Contact Person Name</label><input type="text" id="contactName" name="contact_person_name"></div>
            <div class="form-group"><label for="contactPhone">Contact Person Phone</label><input type="tel" id="contactPhone" name="contact_person_phone"></div>
            <button type="submit" class="mf-button-primary">Save Branch</button>
        </form>
    </div>
</div>

<style>
/* Using styles from manage_users for consistency */
.mf-main-content { padding: 20px 30px; max-width: 1800px; margin: 0 auto; } .mf-page-title h1 { font-size: 1.8rem; } .mf-page-title p { font-size: 1rem; color: #6c757d; } .mf-content-panel { background-color: #fff; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; margin-top: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); } .mf-panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; } .mf-panel-header h2 { margin: 0; font-size: 1.2rem; } .mf-feedback-message { padding: 15px; border-radius: 8px; margin-bottom: 20px; } .mf-feedback-message.success { background-color: #d4edda; color: #155724; } .mf-feedback-message.error { background-color: #f8d7da; color: #721c24; } .form-group { margin-bottom: 15px; } .form-group label { display: block; margin-bottom: 8px; font-weight: 600; } .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 8px; box-sizing: border-box; font-family: inherit; } .mf-button-primary { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; } .mf-button-secondary { background-color: #6c757d; color: white; padding: 6px 12px; font-size: 0.8rem; border: none; border-radius: 6px; cursor: pointer; } .mf-table-responsive { overflow-x: auto; } .mf-table { width: 100%; border-collapse: collapse; } .mf-table th, .mf-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #dee2e6; } .mf-table th { background-color: #f8f9fa; }
/* Modal Styles */
.mf-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); } .mf-modal-content { background-color: #fefefe; margin: 10% auto; padding: 30px; border: 1px solid #888; width: 90%; max-width: 600px; border-radius: 12px; } .mf-modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
</style>

<script>
const modal = document.getElementById('branchModal');
const modalTitle = document.getElementById('modalTitle');
const branchForm = document.getElementById('branchForm');
const branchIdInput = document.getElementById('branchId');

function openBranchModal(branch = null) {
    branchForm.reset();
    if (branch) {
        modalTitle.innerText = 'Edit Branch';
        branchIdInput.value = branch.id;
        document.getElementById('branchName').value = branch.name;
        document.getElementById('branchAddress').value = branch.address;
        document.getElementById('mapsPin').value = branch.maps_pin || '';
        document.getElementById('contactName').value = branch.contact_person_name || '';
        document.getElementById('contactPhone').value = branch.contact_person_phone || '';
    } else {
        modalTitle.innerText = 'Add New Branch';
        branchIdInput.value = '';
    }
    modal.style.display = 'block';
}

function closeBranchModal() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        closeBranchModal();
    }
}
</script>

<?php require_once 'ic/sfooter.php'; ?>
