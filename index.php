<?php
// --- PHP Logic for Processing & Routing ---
$is_submitted = false;
$fullName = $email = $programName = $appId = $phone = $bio = $gender = $cetNumber = "";
$errors = [];
$selectedSports = $selectedHobbies = [];
$pct10 = $pct12 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $fullName = sanitize_input($_POST['fullName'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $program = sanitize_input($_POST['program'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $bio = sanitize_input($_POST['bio'] ?? '');
    $gender = sanitize_input($_POST['gender'] ?? '');
    $cetNumber = sanitize_input($_POST['cetNumber'] ?? '');
    $pct10 = sanitize_input($_POST['pct10'] ?? '');
    $pct12 = sanitize_input($_POST['pct12'] ?? '');

    // arrays (checkboxes)
    $selectedSports = isset($_POST['sports']) && is_array($_POST['sports']) ? array_map('sanitize_input', $_POST['sports']) : [];
    $selectedHobbies = isset($_POST['hobbies']) && is_array($_POST['hobbies']) ? array_map('sanitize_input', $_POST['hobbies']) : [];

    // Server-side validation
    if (empty($fullName)) {
        $errors['fullName'] = "Full Name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
        $errors['fullName'] = "Name should only contain letters and spaces.";
    }

    if (empty($email)) {
        $errors['email'] = "Email Address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (empty($program)) {
        $errors['program'] = "Please select a program.";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone Number is required.";
    } else {
        $normalizedPhone = preg_replace('/[^\d+]/', '', $phone);
        if (!preg_match('/^\+?\d{7,15}$/', $normalizedPhone)) {
            $errors['phone'] = "Please enter a valid phone number (7–15 digits, optional +).";
        } else {
            $phone = $normalizedPhone;
        }
    }

    if (empty($cetNumber)) {
        $errors['cetNumber'] = "CET / Entrance number is required.";
    }

    // percentages: optional but if provided must be numeric 0-100
    if ($pct10 !== '') {
        if (!is_numeric($pct10) || $pct10 < 0 || $pct10 > 100) {
            $errors['pct10'] = "Enter a valid 10th percentage (0 - 100).";
        }
    }
    if ($pct12 !== '') {
        if (!is_numeric($pct12) || $pct12 < 0 || $pct12 > 100) {
            $errors['pct12'] = "Enter a valid 12th percentage (0 - 100).";
        }
    }

    if (!empty($bio) && mb_strlen($bio) > 500) {
        $errors['bio'] = "Bio must be 500 characters or fewer.";
    }

    // If no errors, mark as submitted
    if (empty($errors)) {
        $is_submitted = true;
        $programMap = [
            'CS' => 'Computer Science',
            'IT' => 'Information Technology',
            'EC' => 'Electronics & Communication',
            'ME' => 'Mechanical Engineering',
            'CE' => 'Civil Engineering',
            'EEE' => 'Electrical & Electronics',
            'AI' => 'Artificial Intelligence & ML',
            'DS' => 'Data Science'
        ];
        $programName = $programMap[$program] ?? "Unknown Program";
        $appId = "APP-" . rand(10000, 99999);
    }
}
?><!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sapthagiri NPS University — Application Form</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');
        :root { --glass: rgba(255,255,255,0.04); }
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; min-height:100vh; margin:0; }

        /* dark page background + decorative blobs */
        .page-bg {
            background: radial-gradient(800px 400px at 10% 20%, rgba(99,102,241,0.06), transparent 8%),
                        radial-gradient(700px 350px at 90% 80%, rgba(16,185,129,0.04), transparent 8%),
                        linear-gradient(180deg, #0f172a 0%, #071028 50%, #021018 100%);
            padding: 48px 24px;
            color: #e6eef8;
        }

        .card-entrance { animation: floatUp 0.45s ease-out both; }
        @keyframes floatUp { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform:translateY(0); } }

        .error-message { color: #ff7b7b; font-size: 0.875rem; margin-top: 0.25rem; }
        .focus-ring:focus { outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,0.12); border-color: #6366f1; }

        .qr-box { width: 220px; height: 220px; display:flex; align-items:center; justify-content:center; border-radius:8px; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:12px; }

        @media (min-width: 1024px) { .container-grid { grid-template-columns: 1fr 460px; } }
    </style>
</head>
<body class="page-bg flex items-center justify-center">

<?php if ($is_submitted): // SUCCESS page ?>

    <div class="w-full max-w-xl relative bg-gradient-to-br from-slate-900 to-slate-800 p-6 md:p-8 rounded-2xl shadow-2xl border border-slate-700 card-entrance overflow-hidden">
        <div style="position:absolute; right:-120px; top:-80px; width:320px; height:320px; background:linear-gradient(135deg,#7c3aed,#06b6d4); filter:blur(48px); opacity:0.07; transform:rotate(25deg);"></div>

        <div class="text-center mb-5">
            <span class="text-6xl mb-2 block">🎉</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-2">Application Submitted</h1>
            <p class="text-slate-300 mt-2">Thanks for applying to Sapthagiri NPS University — we've recorded your details.</p>
        </div>

        <div class="bg-slate-900/60 backdrop-blur-sm p-4 rounded-lg mb-4 border-l-4 border-indigo-500">
            <p class="text-sm font-semibold text-indigo-300">Your Application Number</p>
            <div class="flex items-center justify-between mt-1">
                <p class="text-2xl font-black text-white" id="appNumberDisplay"><?php echo htmlspecialchars($appId); ?></p>
                <div class="flex gap-2">
                    <button id="copyAppBtn" class="py-1 px-3 border rounded-md text-sm text-indigo-300 bg-indigo-900/20 hover:bg-indigo-900/30">Copy</button>
                    <button id="downloadJsonBtn" class="py-1 px-3 border rounded-md text-sm text-slate-200 bg-slate-900/40 hover:bg-slate-900/30">Download</button>
                </div>
            </div>
            <div id="copyToast" style="display:none;" class="mt-2 text-xs text-green-400">Copied to clipboard ✓</div>
        </div>

        <h3 class="text-lg font-bold text-white border-b pb-2 mb-4">Summary</h3>

        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-4 p-3 bg-slate-900/50 rounded-lg">
                <div class="text-sm text-slate-300">Name</div>
                <div class="font-semibold text-white"><?php echo htmlspecialchars($fullName); ?></div>

                <div class="text-sm text-slate-300">Email</div>
                <div class="font-semibold text-white"><?php echo htmlspecialchars($email); ?></div>

                <div class="text-sm text-slate-300">Phone</div>
                <div class="font-semibold text-white"><?php echo htmlspecialchars($phone); ?></div>

                <div class="text-sm text-slate-300">Program</div>
                <div class="text-bold text-indigo-300"><?php echo htmlspecialchars($programName); ?></div>

                <div class="text-sm text-slate-300">CET / Entrance No.</div>
                <div class="text-white"><?php echo htmlspecialchars($cetNumber); ?></div>

                <div class="text-sm text-slate-300">10th Percentage</div>
                <div class="text-white"><?php echo ($pct10 !== '') ? htmlspecialchars($pct10) . '%' : '—'; ?></div>

                <div class="text-sm text-slate-300">12th Percentage</div>
                <div class="text-white"><?php echo ($pct12 !== '') ? htmlspecialchars($pct12) . '%' : '—'; ?></div>

                <div class="text-sm text-slate-300">Gender</div>
                <div class="text-white"><?php echo htmlspecialchars($gender ?: '—'); ?></div>

                <div class="text-sm text-slate-300">Sports</div>
                <div class="text-white"><?php echo !empty($selectedSports) ? htmlspecialchars(implode(', ', $selectedSports)) : '—'; ?></div>

                <div class="text-sm text-slate-300">Hobbies</div>
                <div class="text-white"><?php echo !empty($selectedHobbies) ? htmlspecialchars(implode(', ', $selectedHobbies)) : '—'; ?></div>

                <div class="text-sm text-slate-300">Bio</div>
                <div class="text-white"><?php echo nl2br(htmlspecialchars($bio ?: '—')); ?></div>
            </div>
        </div>

        <div class="mt-6 flex flex-col md:flex-row gap-3 justify-center">
             <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="inline-block py-2 px-6 border border-slate-700 rounded-lg text-sm font-medium text-slate-200 hover:bg-slate-900/20 transition duration-150">New Application</a>
             <a href="#" onclick="window.print();" class="inline-block py-2 px-6 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition duration-150">Print / Save</a>
        </div>
    </div>

    <script>
        // confetti
        (function(){ for (let i=0;i<18;i++){ const el=document.createElement('div'); el.className='confetti'; el.style.left=(10+Math.random()*80)+'%'; el.style.background=['#ef4444','#f59e0b','#10b981','#6366f1','#ec4899'][Math.floor(Math.random()*5)]; el.style.top=(Math.random()*10)+'%'; el.style.animationDelay=(Math.random()*300)+'ms'; el.style.transform='translateY(-10px) rotate('+(Math.random()*360)+'deg)'; document.body.appendChild(el);} })();

        document.getElementById('copyAppBtn').addEventListener('click', function(){ const txt=document.getElementById('appNumberDisplay').innerText; navigator.clipboard.writeText(txt).then(function(){ const t=document.getElementById('copyToast'); t.style.display='block'; setTimeout(()=>t.style.display='none',1800); }); });

        document.getElementById('downloadJsonBtn').addEventListener('click', function(){ const payload = {
            applicationNumber: document.getElementById('appNumberDisplay').innerText,
            name: <?php echo json_encode($fullName); ?>,
            email: <?php echo json_encode($email); ?>,
            phone: <?php echo json_encode($phone); ?>,
            program: <?php echo json_encode($programName); ?>,
            cetNumber: <?php echo json_encode($cetNumber); ?>,
            pct10: <?php echo json_encode($pct10); ?>,
            pct12: <?php echo json_encode($pct12); ?>,
            gender: <?php echo json_encode($gender); ?>,
            sports: <?php echo json_encode($selectedSports); ?>,
            hobbies: <?php echo json_encode($selectedHobbies); ?>,
            bio: <?php echo json_encode($bio); ?>
        };
        const blob = new Blob([JSON.stringify(payload, null, 2)], {type: 'application/json'});
        const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download=payload.applicationNumber+'.json'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        });
    </script>

<?php else: // REGISTRATION FORM ?>

    <div class="w-full max-w-6xl container-grid grid gap-6">
        <!-- FORM CARD -->
        <div class="bg-slate-900 p-6 md:p-8 rounded-2xl shadow-lg border border-slate-800 card-entrance">
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-2 text-center">Sapthagiri NPS University — Application Form</h1>
            <p class="text-slate-300 mb-6 text-center">Fill the form to register for your selected program. Provide CET / entrance details and academic percentages.</p>

            <form id="registrationForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="space-y-5" novalidate>

                <!-- Full Name -->
                <div>
                    <label for="fullName" class="block text-sm font-medium text-slate-300">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required
                           value="<?php echo htmlspecialchars($fullName); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                           placeholder="e.g., John Doe" aria-describedby="fullNameError">
                    <div id="fullNameError" class="error-message"><?php echo $errors['fullName'] ?? ''; ?></div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">Email Address</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($email); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                           placeholder="you@example.com" aria-describedby="emailError">
                    <div id="emailError" class="error-message"><?php echo $errors['email'] ?? ''; ?></div>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-300">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required
                           value="<?php echo htmlspecialchars($phone); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                           placeholder="+91 9876543210" aria-describedby="phoneError" inputmode="tel">
                    <div id="phoneError" class="error-message"><?php echo $errors['phone'] ?? ''; ?></div>
                </div>

                <!-- CET Number -->
                <div>
                    <label for="cetNumber" class="block text-sm font-medium text-slate-300">CET / Entrance Number</label>
                    <input type="text" id="cetNumber" name="cetNumber" required
                           value="<?php echo htmlspecialchars($cetNumber); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                           placeholder="e.g., CET2025XXXXX" aria-describedby="cetError">
                    <div id="cetError" class="error-message"><?php echo $errors['cetNumber'] ?? ''; ?></div>
                </div>

                <!-- Academic percentages -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pct10" class="block text-sm font-medium text-slate-300">10th Percentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100" id="pct10" name="pct10"
                               value="<?php echo htmlspecialchars($pct10); ?>"
                               class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                               placeholder="e.g., 85.6">
                        <div id="pct10Error" class="error-message"><?php echo $errors['pct10'] ?? ''; ?></div>
                    </div>

                    <div>
                        <label for="pct12" class="block text-sm font-medium text-slate-300">12th Percentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100" id="pct12" name="pct12"
                               value="<?php echo htmlspecialchars($pct12); ?>"
                               class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                               placeholder="e.g., 88.2">
                        <div id="pct12Error" class="error-message"><?php echo $errors['pct12'] ?? ''; ?></div>
                    </div>
                </div>

                <!-- Program Selection -->
                <div>
                    <label for="program" class="block text-sm font-medium text-slate-300">Program Applied For</label>
                    <select id="program" name="program" required
                            class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                            aria-describedby="programError">
                        <option value="">Select a Program</option>
                        <option value="CS" <?php echo (isset($_POST['program']) && $_POST['program'] === 'CS') ? 'selected' : ''; ?>>Computer Science</option>
                        <option value="AI" <?php echo (isset($_POST['program']) && $_POST['program'] === 'AI') ? 'selected' : ''; ?>>Artificial Intelligence & ML</option>
                        <option value="DS" <?php echo (isset($_POST['program']) && $_POST['program'] === 'DS') ? 'selected' : ''; ?>>Data Science</option>
                        <option value="IT" <?php echo (isset($_POST['program']) && $_POST['program'] === 'IT') ? 'selected' : ''; ?>>Information Technology</option>
                        <option value="EC" <?php echo (isset($_POST['program']) && $_POST['program'] === 'EC') ? 'selected' : ''; ?>>Electronics & Communication</option>
                        <option value="EEE" <?php echo (isset($_POST['program']) && $_POST['program'] === 'EEE') ? 'selected' : ''; ?>>Electrical & Electronics</option>
                        <option value="ME" <?php echo (isset($_POST['program']) && $_POST['program'] === 'ME') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                        <option value="CE" <?php echo (isset($_POST['program']) && $_POST['program'] === 'CE') ? 'selected' : ''; ?>>Civil Engineering</option>
                    </select>
                    <div id="programError" class="error-message"><?php echo $errors['program'] ?? ''; ?></div>
                </div>

                <!-- Gender (radio) -->
                <div>
                    <label class="block text-sm font-medium text-slate-300">Gender</label>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2 text-slate-200"><input type="radio" name="gender" value="Male" <?php echo ($gender === 'Male') ? 'checked' : ''; ?> class="focus-ring"> Male</label>
                        <label class="flex items-center gap-2 text-slate-200"><input type="radio" name="gender" value="Female" <?php echo ($gender === 'Female') ? 'checked' : ''; ?> class="focus-ring"> Female</label>
                        <label class="flex items-center gap-2 text-slate-200"><input type="radio" name="gender" value="Other" <?php echo ($gender === 'Other') ? 'checked' : ''; ?> class="focus-ring"> Other</label>
                    </div>
                </div>

                <!-- Sports (checkboxes) -->
                <div>
                    <label class="block text-sm font-medium text-slate-300">Sports (select all that apply)</label>
                    <div class="mt-2 flex gap-3 flex-wrap">
                        <?php $sportsList=['Cricket','Football','Basketball','Badminton','Athletics'];
                              foreach($sportsList as $s) {
                                $checked = in_array($s, $selectedSports) ? 'checked' : '';
                                echo "<label class=\"flex items-center gap-2 text-slate-200\"><input type=\"checkbox\" name=\"sports[]\" value=\"$s\" $checked class=\"focus-ring\"> $s</label>";
                              }
                        ?>
                    </div>
                </div>

                <!-- Hobbies (checkboxes) -->
                <div>
                    <label class="block text-sm font-medium text-slate-300">Hobbies (select all that apply)</label>
                    <div class="mt-2 flex gap-3 flex-wrap">
                        <?php $hobbiesList=['Reading','Music','Coding','Drawing','Volunteering'];
                              foreach($hobbiesList as $h) {
                                $checked = in_array($h, $selectedHobbies) ? 'checked' : '';
                                echo "<label class=\"flex items-center gap-2 text-slate-200\"><input type=\"checkbox\" name=\"hobbies[]\" value=\"$h\" $checked class=\"focus-ring\"> $h</label>";
                              }
                        ?>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-slate-300">Short Bio <span class="text-xs text-slate-400">(optional, max 500 chars)</span></label>
                    <textarea id="bio" name="bio" rows="4" maxlength="500"
                              class="mt-1 block w-full px-4 py-2 border border-slate-700 bg-slate-800 rounded-lg shadow-sm focus-ring text-slate-100 transition duration-150 ease-in-out"
                              placeholder="Tell us a bit about yourself..."><?php echo htmlspecialchars($bio); ?></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <div id="bioError" class="error-message"><?php echo $errors['bio'] ?? ''; ?></div>
                        <div class="text-sm text-slate-400"><span id="bioCount"><?php echo mb_strlen($bio); ?></span>/500</div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-slate-900 bg-emerald-400 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-300 transition duration-150">
                        Submit Application
                    </button>
                </div>
            </form>

            <p class="mt-4 text-xs text-slate-400 text-center">Live preview on the right updates as you type. All data is validated before submit.</p>
        </div>

        <!-- LIVE PREVIEW -->
        <div class="bg-gradient-to-br from-slate-900/80 to-slate-800 p-6 md:p-8 rounded-2xl shadow-lg border border-slate-800 h-fit">
            <h2 class="text-lg font-bold text-white mb-4">Live Preview</h2>

            <div id="previewCard" class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-700 to-teal-700 flex items-center justify-center text-white text-xl font-bold">S</div>
                    <div>
                        <div id="previewName" class="text-base font-semibold text-white"><?php echo htmlspecialchars($fullName ?: 'Your Name'); ?></div>
                        <div id="previewProgram" class="text-sm text-slate-300"><?php echo htmlspecialchars($programName ?: 'Program (preview)'); ?></div>
                    </div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">Email</div>
                    <div id="previewEmail" class="font-medium text-slate-200"><?php echo htmlspecialchars($email ?: 'you@example.com'); ?></div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">Phone</div>
                    <div id="previewPhone" class="font-medium text-slate-200"><?php echo htmlspecialchars($phone ?: '—'); ?></div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">CET / Entrance No.</div>
                    <div id="previewCet" class="font-medium text-slate-200"><?php echo htmlspecialchars($cetNumber ?: '—'); ?></div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">10th</div>
                    <div id="preview10" class="font-medium text-slate-200"><?php echo ($pct10 !== '') ? htmlspecialchars($pct10) . '%' : '—'; ?></div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">12th</div>
                    <div id="preview12" class="font-medium text-slate-200"><?php echo ($pct12 !== '') ? htmlspecialchars($pct12) . '%' : '—'; ?></div>
                </div>

                <div class="p-3 bg-slate-800 rounded-lg">
                    <div class="text-sm text-slate-400">Bio</div>
                    <div id="previewBio" class="text-slate-200"><?php echo htmlspecialchars($bio ?: 'A short bio will appear here.'); ?></div>
                </div>

                <div class="mt-3 text-right">
                    <div class="text-xs text-slate-400">Preview doesn't submit data — it's just for display.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript: client-side validation + live preview -->
    <script>
        $(document).ready(function() {
            function updateBioCount() { $('#bioCount').text($('#bio').val().length); }
            updateBioCount();

            function updatePreview() {
                const name = $('#fullName').val().trim() || 'Your Name';
                const email = $('#email').val().trim() || 'you@example.com';
                const phone = $('#phone').val().trim() || '—';
                const bio = $('#bio').val().trim() || 'A short bio will appear here.';
                const programText = $('#program option:selected').text() || 'Program (preview)';
                const cet = $('#cetNumber').val().trim() || '—';
                const p10 = $('#pct10').val().trim() || '—';
                const p12 = $('#pct12').val().trim() || '—';

                $('#previewName').text(name);
                $('#previewEmail').text(email);
                $('#previewPhone').text(phone);
                $('#previewBio').text(bio);
                $('#previewProgram').text(programText);
                $('#previewCet').text(cet);
                $('#preview10').text(p10 !== '—' ? p10 + '%' : '—');
                $('#preview12').text(p12 !== '—' ? p12 + '%' : '—');
            }

            $('#fullName, #email, #phone, #bio, #program, #cetNumber, #pct10, #pct12').on('input change', function() {
                updateBioCount(); updatePreview(); $('.error-message').text('');
            });

            $('#registrationForm').on('submit', function(e) {
                let isValid = true; $('.error-message').text('');

                const fullName = $('#fullName').val().trim();
                const nameRegex = /^[a-zA-Z\s]+$/;
                if (!fullName) { $('#fullNameError').text('Full Name is required.'); isValid = false; }
                else if (!nameRegex.test(fullName)) { $('#fullNameError').text('Name should only contain letters and spaces.'); isValid = false; }

                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) { $('#emailError').text('Email Address is required.'); isValid = false; }
                else if (!emailRegex.test(email)) { $('#emailError').text('Please enter a valid email address.'); isValid = false; }

                const phone = $('#phone').val().trim(); const phoneNormalized = phone.replace(/[^\d+]/g,''); const phoneRegex = /^\+?\d{7,15}$/;
                if (!phone) { $('#phoneError').text('Phone Number is required.'); isValid = false; }
                else if (!phoneRegex.test(phoneNormalized)) { $('#phoneError').text('Please enter a valid phone number (7–15 digits, optional +).'); isValid = false; }

                const program = $('#program').val(); if (!program) { $('#programError').text('Please select a program.'); isValid = false; }

                const cet = $('#cetNumber').val().trim(); if (!cet) { $('#cetError').text('CET / Entrance number is required.'); isValid = false; }

                const p10 = $('#pct10').val().trim(); if (p10 !== '' && (isNaN(p10) || p10 < 0 || p10 > 100)) { $('#pct10Error').text('Enter a valid 10th percentage (0 - 100).'); isValid = false; }
                const p12 = $('#pct12').val().trim(); if (p12 !== '' && (isNaN(p12) || p12 < 0 || p12 > 100)) { $('#pct12Error').text('Enter a valid 12th percentage (0 - 100).'); isValid = false; }

                if ($('#bio').val().length > 500) { $('#bioError').text('Bio must be 500 characters or fewer.'); isValid = false; }

                if (!isValid) { e.preventDefault(); const $firstError = $('.error-message:not(:empty)').first().closest('div'); if ($firstError.length) { $('html, body').animate({ scrollTop: $firstError.offset().top - 20 }, 400); } }
            });

            updatePreview();
        });
    </script>

<?php endif; ?>

</body>
</html>
