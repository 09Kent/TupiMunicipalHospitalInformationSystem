@extends('layouts.register')

@section('title', 'Appointments & Consultations | Tupi Municipal Hospital Information Management System')

@section('content')
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
        Clinical Schedule & Appointments
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Consultation Schedule</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage daily physician consultations, reschedule, and update patient session statuses.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <a href="<?= route('register.registration.index') ?>" 
         class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-600/20 transition flex items-center gap-2">
        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
        <span>+ Book Consultation</span>
      </a>

      <button type="button" onclick="window.print()" class="no-print px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Schedule</span>
      </button>
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="no-print bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs">
    <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      
      <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Appointment Date</label>
        <input type="date" name="date" value="<?= e($date) ?>" onchange="this.form.submit()" 
               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Attending Doctor</label>
        <select name="doctor" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
          <option value="">-- All Doctors --</option>
          <?php foreach ($allDoctors as $doc): ?>
            <option value="<?= $doc['DoctorID'] ?>" <?= $doctor == $doc['DoctorID'] ? 'selected' : '' ?>>
              Dr. <?= e($doc['FirstName'] . ' ' . $doc['LastName']) ?> (<?= e($doc['Specialty']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</label>
        <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
          <option value="">-- All Statuses --</option>
          <option value="Scheduled" <?= $status === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
          <option value="Waiting" <?= $status === 'Waiting' ? 'selected' : '' ?>>Waiting in Queue</option>
          <option value="In Consultation" <?= $status === 'In Consultation' ? 'selected' : '' ?>>In Consultation</option>
          <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
          <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-2xs">
          Filter
        </button>
        <?php if (!empty($date) || !empty($doctor) || !empty($status)): ?>
          <a href="<?= route('register.appointments.index') ?>" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold" title="Clear">
            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
          </a>
        <?php endif; ?>
      </div>

    </form>
  </div>

  <!-- Appointments Table Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs">
      <div class="font-bold text-slate-700">
        Total <strong class="text-blue-700 font-extrabold"><?= $totalAppointments ?></strong> appointments recorded
      </div>
      <div class="text-slate-400 font-medium">
        Showing 10 records per page
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 font-bold">
            <th class="pb-3.5">Date & Time</th>
            <th class="pb-3.5">Patient Details</th>
            <th class="pb-3.5">Attending Specialist</th>
            <th class="pb-3.5">Consultation Type</th>
            <th class="pb-3.5">Reason / Notes</th>
            <th class="pb-3.5">Status</th>
            <th class="pb-3.5 text-right no-print">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $app): ?>
              <tr class="hover:bg-slate-50/70 transition">
                
                <td class="py-3.5">
                  <div class="font-mono font-bold text-blue-700"><?= format_date($app['AppointmentDate']) ?></div>
                  <div class="text-slate-500 font-semibold mt-0.5"><?= e($app['AppointmentTime']) ?></div>
                </td>

                <td class="py-3.5">
                  <a href="<?= base_url('views/patients/view.php?id=' . $app['PatientID']) ?>" class="font-bold text-slate-800 hover:text-blue-600 text-sm">
                    <?= e($app['PatFirst'] . ' ' . $app['PatLast']) ?>
                  </a>
                  <div class="text-[10px] text-slate-400"><?= e($app['PatientCode']) ?> • <?= e($app['Age']) ?>y, <?= e($app['Gender']) ?></div>
                </td>

                <td class="py-3.5">
                  <div class="font-bold text-slate-800">Dr. <?= e($app['DocFirst'] . ' ' . $app['DocLast']) ?></div>
                  <div class="text-[10px] text-slate-500"><?= e($app['Specialty']) ?> (<?= e($app['ClinicRoom']) ?>)</div>
                </td>

                <td class="py-3.5 text-slate-600 font-medium">
                  <?= e($app['ConsultationType']) ?>
                </td>

                <td class="py-3.5 text-slate-600 max-w-[200px] truncate" title="<?= e($app['Reason']) ?>">
                  <?= e($app['Reason']) ?>
                </td>

                <td class="py-3.5">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold <?= get_status_badge($app['Status']) ?>">
                    <?= e($app['Status']) ?>
                  </span>
                </td>

                <td class="py-3.5 text-right no-print">
                  <div class="flex items-center justify-end gap-1.5">
                    
                    <?php if ($app['Status'] !== 'Completed' && $app['Status'] !== 'Cancelled'): ?>
                      <button type="button" onclick="openRescheduleModal(<?= $app['AppointmentID'] ?>, '<?= $app['AppointmentDate'] ?>', '<?= $app['AppointmentTime'] ?>', <?= $app['DoctorID'] ?>)" 
                              class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition">
                        Reschedule
                      </button>

                      <button type="button" onclick="cancelAppointment(<?= $app['AppointmentID'] ?>)" 
                              class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-[11px] font-bold transition">
                        Cancel
                      </button>
                    <?php else: ?>
                      <span class="text-[11px] text-slate-400 font-medium">Archived</span>
                    <?php endif; ?>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400 italic">No appointments match the criteria.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs">
        <div class="text-slate-500">Page <?= $page ?> of <?= $totalPages ?></div>
        <div class="flex items-center gap-1">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&date=<?= urlencode($date) ?>&doctor=<?= urlencode($doctor) ?>&status=<?= urlencode($status) ?>" 
               class="w-8 h-8 rounded-lg flex items-center justify-center font-bold <?= $i === $page ? 'bg-blue-600 text-white' : 'border border-slate-200 text-slate-700' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>

</main>

<!-- Reschedule Modal -->
<div id="reschedule-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-blue-100 animate-in zoom-in-95">
    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
      <h3 class="text-sm font-extrabold text-slate-900">Reschedule Consultation</h3>
      <button type="button" onclick="closeRescheduleModal()" class="text-slate-400 hover:text-slate-600">✕</button>
    </div>

    <form id="form-reschedule" onsubmit="submitReschedule(event)" class="space-y-3 text-xs">
      <input type="hidden" id="modal-reschedule-id" />

      <div>
        <label class="block font-bold text-slate-700 mb-1">New Appointment Date</label>
        <input type="date" id="modal-reschedule-date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" />
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">New Time Slot</label>
        <select id="modal-reschedule-time" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
          <option value="09:00 AM">09:00 AM</option>
          <option value="10:30 AM">10:30 AM</option>
          <option value="01:45 PM">01:45 PM</option>
          <option value="02:30 PM">02:30 PM</option>
          <option value="04:15 PM">04:15 PM</option>
          <option value="05:00 PM">05:00 PM</option>
        </select>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">Reason for Rescheduling</label>
        <input type="text" id="modal-reschedule-reason" placeholder="e.g. Patient schedule conflict" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" />
      </div>

      <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
        <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2 bg-slate-100 rounded-xl font-bold text-slate-700">Cancel</button>
        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-md shadow-blue-600/20">Confirm Reschedule</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openRescheduleModal(id, date, time, docId) {
    document.getElementById('modal-reschedule-id').value = id;
    document.getElementById('modal-reschedule-date').value = date;
    document.getElementById('modal-reschedule-time').value = time;
    document.getElementById('reschedule-modal').classList.remove('hidden');
  }

  function closeRescheduleModal() {
    document.getElementById('reschedule-modal').classList.add('hidden');
  }

  async function submitReschedule(e) {
    e.preventDefault();
    const id = document.getElementById('modal-reschedule-id').value;
    const date = document.getElementById('modal-reschedule-date').value;
    const time = document.getElementById('modal-reschedule-time').value;
    const reason = document.getElementById('modal-reschedule-reason').value;

    try {
      const res = await fetch('<?= base_url('api/appointments/index.php') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'reschedule',
          appointment_id: id,
          appointment_date: date,
          appointment_time: time,
          reason: reason
        })
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Failed to reschedule.');
      }
    } catch (err) {
      alert('Error connecting to server.');
    }
  }

  async function cancelAppointment(id) {
    const reason = prompt('Please specify cancellation reason:');
    if (!reason) return;

    try {
      const res = await fetch('<?= base_url('api/appointments/index.php') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'cancel',
          appointment_id: id,
          reason: reason
        })
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Failed to cancel.');
      }
    } catch (err) {
      alert('Error connecting to server.');
    }
  }
</script>
@endsection
