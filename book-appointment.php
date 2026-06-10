<?php
require_once 'config.php';

$page_title = "Book Appointment - Bhosale Opticians";
$active_page = "appointment";
$base_url = "";

$success = '';
$error = '';

// Auto-create table if not exists
$conn = getDBConnection();
$conn->query("CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_type ENUM('Eye Check-up', 'Frame Selection', 'Lens Consultation', 'Contact Lens Trial', 'Follow-up') NOT NULL DEFAULT 'Eye Check-up',
    patient_name VARCHAR(100) NOT NULL,
    patient_phone VARCHAR(15) NOT NULL,
    patient_age INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=book-appointment.php');
        exit();
    }

    // Handle cancellation
    if (isset($_POST['cancel_appointment'])) {
        $cancel_id = intval($_POST['cancel_id']);
        $uid = $_SESSION['user_id'];
        // Only allow cancel if it belongs to this user and is pending/confirmed
        $check = $conn->query("SELECT id FROM appointments WHERE id=$cancel_id AND user_id=$uid AND status IN ('pending','confirmed')");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE appointments SET status='cancelled' WHERE id=$cancel_id");
            $success = 'Appointment #' . $cancel_id . ' has been cancelled successfully.';
        } else {
            $error = 'Cannot cancel this appointment.';
        }
    } else {
        $user_id        = $_SESSION['user_id'];
        $patient_name   = $conn->real_escape_string(trim($_POST['patient_name']));
        $patient_phone  = $conn->real_escape_string(trim($_POST['patient_phone']));
        $patient_age    = intval($_POST['patient_age'] ?? 0);
        $appt_date      = $conn->real_escape_string($_POST['appointment_date']);
        $appt_time      = $conn->real_escape_string($_POST['appointment_time']);
        $appt_type      = $conn->real_escape_string($_POST['appointment_type']);
        $notes          = $conn->real_escape_string(trim($_POST['notes'] ?? ''));

        // Validate date is not in the past
        if (strtotime($appt_date) < strtotime(date('Y-m-d'))) {
            $error = 'Please select a future date.';
        } else {
            // Check if slot is already booked
            $check = $conn->query("SELECT id FROM appointments WHERE appointment_date='$appt_date' AND appointment_time='$appt_time' AND status != 'cancelled'");
            if ($check->num_rows > 0) {
                $error = 'This time slot is already booked. Please choose a different time.';
            } else {
                $sql = "INSERT INTO appointments (user_id, patient_name, patient_phone, patient_age, appointment_date, appointment_time, appointment_type, notes)
                        VALUES ($user_id, '$patient_name', '$patient_phone', $patient_age, '$appt_date', '$appt_time', '$appt_type', '$notes')";
                if ($conn->query($sql)) {
                    $appt_id = $conn->insert_id;
                    $success = "Appointment booked successfully! Your appointment ID is <strong>#$appt_id</strong>. We will confirm shortly.";
                } else {
                    $error = 'Failed to book appointment. Please try again.';
                }
            }
        }
    } // end else (new booking)
}

// Fetch booked slots for next 30 days (to disable them in JS)
$booked_slots = [];
$slots_result = $conn->query("SELECT appointment_date, appointment_time FROM appointments WHERE appointment_date >= CURDATE() AND status != 'cancelled'");
while ($row = $slots_result->fetch_assoc()) {
    $booked_slots[$row['appointment_date']][] = $row['appointment_time'];
}

// Fetch user's appointments if logged in
$my_appointments = [];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $my_result = $conn->query("SELECT * FROM appointments WHERE user_id = $uid ORDER BY appointment_date DESC, appointment_time DESC LIMIT 10");
    while ($row = $my_result->fetch_assoc()) $my_appointments[] = $row;
}

$conn->close();

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bold mb-3">📅 Book an Appointment</h1>
        <p class="lead mb-0">Schedule your eye check-up at Bhosale Opticians — quick, easy, no waiting!</p>
    </div>
</section>

<!-- Why Book Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 py-3">
                    <div class="card-body">
                        <div style="font-size:2.5rem;">👁️</div>
                        <h6 class="fw-bold mt-2">Professional Eye Check-up</h6>
                        <p class="text-muted small mb-0">Complete eye examination by certified optometrists</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 py-3">
                    <div class="card-body">
                        <div style="font-size:2.5rem;">⏰</div>
                        <h6 class="fw-bold mt-2">No Waiting Time</h6>
                        <p class="text-muted small mb-0">Book your slot in advance and walk in on time</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 py-3">
                    <div class="card-body">
                        <div style="font-size:2.5rem;">🆓</div>
                        <h6 class="fw-bold mt-2">Free Consultation</h6>
                        <p class="text-muted small mb-0">Eye check-up is completely free of charge</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section class="container my-5">
    <div class="row g-5">
        <!-- Form -->
        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <h4 class="text-white mb-0">📋 Fill Appointment Details</h4>
                </div>
                <div class="card-body p-4">

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Please <a href="login.php?redirect=book-appointment.php" class="fw-bold">login</a> to book an appointment.
                    </div>
                    <?php endif; ?>

                    <form method="POST" id="appointmentForm">
                        <div class="row g-3">
                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_name" class="form-control"
                                       value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>"
                                       placeholder="Full name" required>
                            </div>
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="patient_phone" class="form-control"
                                       placeholder="10-digit mobile" pattern="[0-9]{10}" required>
                            </div>
                            <!-- Age -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Age</label>
                                <input type="number" name="patient_age" class="form-control" placeholder="Age" min="1" max="120">
                            </div>
                            <!-- Appointment Type -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Appointment Type <span class="text-danger">*</span></label>
                                <select name="appointment_type" class="form-select" required>
                                    <option value="Eye Check-up">👁️ Eye Check-up</option>
                                    <option value="Frame Selection">🕶️ Frame Selection</option>
                                    <option value="Lens Consultation">🔬 Lens Consultation</option>
                                    <option value="Contact Lens Trial">👁️ Contact Lens Trial</option>
                                    <option value="Follow-up">🔄 Follow-up Visit</option>
                                </select>
                            </div>
                            <!-- Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" id="apptDate" class="form-control"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       max="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                                <small class="text-muted">Available Mon–Sat only</small>
                            </div>
                            <!-- Time -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred Time <span class="text-danger">*</span></label>
                                <select name="appointment_time" id="apptTime" class="form-select" required>
                                    <option value="">-- Select date first --</option>
                                </select>
                            </div>
                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Additional Notes</label>
                                <textarea name="notes" class="form-control" rows="3"
                                          placeholder="Any specific concerns, existing prescriptions, or special requirements..."></textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-lg text-white fw-bold py-3"
                                    style="background: linear-gradient(135deg, #667eea, #764ba2);"
                                    <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>>
                                📅 Confirm Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-5">
            <!-- Shop Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">🏪 Shop Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex gap-3">
                            <span style="font-size:1.4rem;">📍</span>
                            <div><strong>Address</strong><br><span class="text-muted">1st Floor, Silver Springs, Hotgi Road, Solapur, Maharashtra 413003</span></div>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <span style="font-size:1.4rem;">📞</span>
                            <div><strong>Phone</strong><br><span class="text-muted">9960815363</span></div>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <span style="font-size:1.4rem;">✉️</span>
                            <div><strong>Email</strong><br><span class="text-muted">bhosaleopticians@gmail.com</span></div>
                        </li>
                        <li class="d-flex gap-3">
                            <span style="font-size:1.4rem;">🕐</span>
                            <div>
                                <strong>Working Hours</strong><br>
                                <span class="text-muted">Mon – Sat: 10:00 AM – 7:00 PM</span><br>
                                <span class="text-danger small">Sunday: Closed</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Available Slots Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">⏰ Available Time Slots</h5>
                    <div class="row g-2">
                        <?php
                        $slots = ['10:00','10:30','11:00','11:30','12:00','12:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30'];
                        foreach($slots as $slot):
                        ?>
                        <div class="col-4">
                            <span class="badge bg-light text-dark border w-100 py-2"><?= $slot ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-muted small mt-2 mb-0">* Booked slots will be shown as unavailable</p>
                </div>
            </div>

            <!-- Steps -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">📌 How It Works</h5>
                    <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;background:#667eea;font-weight:bold;">1</div>
                        <div><strong>Fill the form</strong><br><small class="text-muted">Enter your details and choose date & time</small></div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;background:#667eea;font-weight:bold;">2</div>
                        <div><strong>Get Confirmation</strong><br><small class="text-muted">Admin confirms your appointment</small></div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;background:#667eea;font-weight:bold;">3</div>
                        <div><strong>Visit the Shop</strong><br><small class="text-muted">Walk in at your scheduled time</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Appointments -->
    <?php if (isset($_SESSION['user_id']) && !empty($my_appointments)): ?>
    <div class="mt-5">
        <h3 class="fw-bold mb-4">📋 My Appointments</h3>
        <div class="table-responsive">
            <table class="table table-hover shadow-sm rounded overflow-hidden">
                <thead style="background: linear-gradient(135deg, #667eea, #764ba2); color:white;">
                    <tr>
                        <th>#ID</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Status</th>
                        <th>Admin Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($my_appointments as $appt): ?>
                    <?php
                    $badge = match($appt['status']) {
                        'confirmed'  => 'success',
                        'completed'  => 'primary',
                        'cancelled'  => 'danger',
                        default      => 'warning'
                    };
                    $can_cancel = in_array($appt['status'], ['pending', 'confirmed']);
                    ?>
                    <tr>
                        <td><strong>#<?= $appt['id'] ?></strong></td>
                        <td><?= htmlspecialchars($appt['appointment_type']) ?></td>
                        <td><?= date('d M Y', strtotime($appt['appointment_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($appt['appointment_time'])) ?></td>
                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($appt['status']) ?></span></td>
                        <td class="text-muted small"><?= htmlspecialchars($appt['admin_notes'] ?? '—') ?></td>
                        <td>
                            <?php if($can_cancel): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmCancel(<?= $appt['id'] ?>, '<?= date('d M Y', strtotime($appt['appointment_date'])) ?>')">
                                ✕ Cancel
                            </button>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Hidden cancel form -->
    </div>
    <?php endif; ?>
</section>

<?php if (isset($_SESSION['user_id'])): ?>
<form method="POST" id="cancelForm" style="display:none;">
    <input type="hidden" name="cancel_appointment" value="1">
    <input type="hidden" name="cancel_id" id="cancelId">
</form>
<?php endif; ?>

<script>
// Booked slots from PHP
const bookedSlots = <?= json_encode($booked_slots) ?>;

const allSlots = [
    '10:00:00','10:30:00','11:00:00','11:30:00','12:00:00','12:30:00',
    '14:00:00','14:30:00','15:00:00','15:30:00','16:00:00','16:30:00',
    '17:00:00','17:30:00','18:00:00','18:30:00'
];

const slotLabels = {
    '10:00:00':'10:00 AM','10:30:00':'10:30 AM','11:00:00':'11:00 AM','11:30:00':'11:30 AM',
    '12:00:00':'12:00 PM','12:30:00':'12:30 PM','14:00:00':'02:00 PM','14:30:00':'02:30 PM',
    '15:00:00':'03:00 PM','15:30:00':'03:30 PM','16:00:00':'04:00 PM','16:30:00':'04:30 PM',
    '17:00:00':'05:00 PM','17:30:00':'05:30 PM','18:00:00':'06:00 PM','18:30:00':'06:30 PM'
};

document.getElementById('apptDate').addEventListener('change', function() {
    const date = this.value;
    const timeSelect = document.getElementById('apptTime');
    const dayOfWeek = new Date(date + 'T00:00:00').getDay(); // 0=Sun

    timeSelect.innerHTML = '';

    if (dayOfWeek === 0) {
        timeSelect.innerHTML = '<option value="">Sunday - Shop Closed</option>';
        return;
    }

    const taken = bookedSlots[date] || [];
    let hasAvailable = false;

    allSlots.forEach(slot => {
        const opt = document.createElement('option');
        opt.value = slot;
        if (taken.includes(slot)) {
            opt.textContent = slotLabels[slot] + ' — Booked';
            opt.disabled = true;
            opt.style.color = '#999';
        } else {
            opt.textContent = slotLabels[slot] + ' — Available';
            hasAvailable = true;
        }
        timeSelect.appendChild(opt);
    });

    if (!hasAvailable) {
        timeSelect.innerHTML = '<option value="">No slots available for this date</option>';
    }
});

function confirmCancel(id, date) {
    if (confirm('Are you sure you want to cancel appointment #' + id + ' on ' + date + '?\nThis cannot be undone.')) {
        document.getElementById('cancelId').value = id;
        document.getElementById('cancelForm').submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
