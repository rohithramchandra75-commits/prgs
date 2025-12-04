<?php
// --- PHP Logic for Processing & Routing ---
$is_submitted = false;
$fullName = $email = $programName = $appId = "";
$programKey = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $fullName = sanitize_input($_POST['fullName'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $programKey = sanitize_input($_POST['program'] ?? '');

    // Simple validation: Ensure required fields are present
    if (!empty($fullName) && !empty($email) && !empty($programKey) && $programKey != '') {
        $is_submitted = true;
        $programMap = [
            'CS' => ['name' => 'Computer Science', 'emoji' => '🖥️', 'accent' => 'indigo', 'bg' => 'indigo-50', 'border' => 'indigo-500', 'text' => 'indigo-700'],
            'IT' => ['name' => 'Information Technology', 'emoji' => '🛠️', 'accent' => 'emerald', 'bg' => 'emerald-50', 'border' => 'emerald-500', 'text' => 'emerald-700'],
            'EC' => ['name' => 'Electronics & Communication', 'emoji' => '📡', 'accent' => 'amber', 'bg' => 'amber-50', 'border' => 'amber-500', 'text' => 'amber-700'],
        ];
        $programDef = $programMap[$programKey] ?? ["name" => "Unknown Program", "emoji"=>"🎓", "accent"=>'slate', "bg"=>"slate-50", "border"=>"slate-400", "text"=>"slate-700"];
        $programName = $programDef['name'];
        $programEmoji = $programDef['emoji'];
        $accent = $programDef['accent'];
        $accentBg = $programDef['bg'];
        $accentBorder = $programDef['border'];
        $accentText = $programDef['text'];

        $appId = "REG-" . rand(10000, 99999);
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo $is_submitted ? "Submission Success" : "Online Registration Form"; ?></title>
    <!-- Tailwind & jQuery -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f4f8; }
        .error-message { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
        .success-card { animation: fadeInUp 0.5s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<?php if ($is_submitted): // Display SUCCESS page ?>

    <?php
    // Use the server-side accent variables to theme success page
    if (!isset($programEmoji)) { $programEmoji = '🎉'; $accent = 'indigo'; $accentBg = 'indigo-50'; $accentBorder = 'indigo-500'; $accentText = 'indigo-700'; }
    // Tailwind classes composed from accent variables
    $borderColorClass = "border-4 border-{$accentBorder}";
    $bgClass = "bg-white";
    $accentPanelBg = "bg-{$accentBg}";
    $accentPanelBorder = "border-l-4 border-{$accentBorder}";
    $accentTextClass = "text-{$accentText}";
    ?>

    <div class="w-full max-w-xl <?php echo $bgClass; ?> p-8 md:p-10 rounded-xl shadow-2xl <?php echo $borderColorClass; ?> success-card">
        <div class="text-center mb-8">
            <span class="text-6xl mb-4 block"><?php echo $programEmoji; ?></span>
            <h1 class="text-3xl font-extrabold <?php echo $accentTextClass; ?> mt-4">Woohoo! Application Submitted!</h1>
            <p class="text-gray-600 mt-2">You've successfully registered for <strong><?php echo htmlspecialchars($programName); ?></strong>.</p>
        </div>

        <div class="<?php echo $accentPanelBg; ?> p-4 rounded-lg mb-6 <?php echo $accentPanelBorder; ?>">
            <p class="text-sm font-semibold <?php echo $accentTextClass; ?>">Your Reference ID:</p>
            <p class="text-2xl font-black <?php echo $accentTextClass; ?> mt-1"><?php echo htmlspecialchars($appId); ?></p>
        </div>

        <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Summary of Your Registration:</h3>

        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-100 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Name:</span>
                <span class="text-base font-semibold text-gray-900"><?php echo htmlspecialchars($fullName); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-100 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Email:</span>
                <span class="text-base font-semibold text-gray-900"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 <?php echo "bg-{$accentBg}"; ?> rounded-lg <?php echo $accentPanelBorder; ?>">
                <span class="text-sm font-medium text-gray-600">Program:</span>
                <span class="text-base font-bold <?php echo $accentTextClass; ?>"><?php echo htmlspecialchars($programName); ?></span>
            </div>
        </div>

        <div class="mt-8 text-center">
             <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="inline-block py-2 px-6 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition duration-150">Register Another Application</a>
        </div>
    </div>

<?php else: // Display REGISTRATION FORM ?>

    <div id="formCard" class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 id="formTitle" class="text-3xl font-extrabold text-gray-800">Online Registration Form</h1>
                <p id="formSubtitle" class="text-gray-500">Please fill out your details below to register for the program.</p>
            </div>
            <div id="programBadge" class="text-3xl">🎓</div>
        </div>

        <!-- The Form: action points to the same file (PHP_SELF) -->
        <form id="registrationForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="space-y-6">

            <!-- Full Name -->
            <div>
                <label for="fullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="fullName" name="fullName" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                       placeholder="e.g., John Doe">
                <div id="fullNameError" class="error-message"></div>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                       placeholder="you@example.com">
                <div id="emailError" class="error-message"></div>
            </div>

            <!-- Program Selection -->
            <div>
                <label for="program" class="block text-sm font-medium text-gray-700">Program Applied For</label>
                <select id="program" name="program" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                        >
                    <option value="">Select a Program</option>
                    <option value="CS" data-emoji="🖥️" data-accent="indigo">Computer Science</option>
                    <option value="IT" data-emoji="🛠️" data-accent="emerald">Information Technology</option>
                    <option value="EC" data-emoji="📡" data-accent="amber">Electronics & Communication</option>
                </select>
                <div id="programError" class="error-message"></div>
            </div>

            <!-- Submission Button -->
            <div class="pt-4">
                <button id="submitBtn" type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    Submit Application
                </button>
            </div>
        </form>
        <div class="mt-4 text-xs text-gray-400">Pro tip: select a program to preview the theme.</div>
    </div>

    <!-- JavaScript Validation Logic & Theming -->
    <script>
        $(document).ready(function() {
            // Program styles map (must mirror PHP map for consistent behavior)
            const programStyles = {
                'CS': { name: 'Computer Science', emoji: '🖥️', accent: 'indigo', bg: 'indigo-50', border: 'indigo-500', text: 'indigo-700' },
                'IT': { name: 'Information Technology', emoji: '🛠️', accent: 'emerald', bg: 'emerald-50', border: 'emerald-500', text: 'emerald-700' },
                'EC': { name: 'Electronics & Communication', emoji: '📡', accent: 'amber', bg: 'amber-50', border: 'amber-500', text: 'amber-700' }
            };

            function applyTheme(key) {
                // Reset to default
                const $card = $('#formCard');
                $card.removeClass(function (index, className) {
                    return (className.match(/(^|\s)(border-|bg-|ring-|text-)\S+/g) || []).join(' ');
                });

                // Reset button and input focus ring classes
                $('#submitBtn').removeClass(function (index, className) {
                    return (className.match(/(^|\s)(bg-|hover:bg-|focus:ring-)\S+/g) || []).join(' ');
                });

                // If no key => default styling
                if (!key || !programStyles[key]) {
                    $('#programBadge').text('🎓');
                    $('#formTitle').text('Online Registration Form');
                    $('#formSubtitle').text('Please fill out your details below to register for the program.');
                    $('#submitBtn').addClass('bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500');
                    return;
                }

                const def = programStyles[key];
                // update badge and title
                $('#programBadge').text(def.emoji);
                $('#formTitle').text(def.name + ' - Registration');
                $('#formSubtitle').text('You are applying for ' + def.name + '. Please provide your details.');

                // Compose classes dynamically (Tailwind JIT will apply them)
                const borderClass = 'border-2 border-' + def.border;
                const panelBgClass = 'bg-' + def.bg;
                const accentTextClass = 'text-' + def.text;
                const btnBg = 'bg-' + def.accent + '-600';
                const btnHover = 'hover:bg-' + def.accent + '-700';
                const ringClass = 'focus:ring-' + def.accent + '-500';

                // Apply to card
                $card.addClass(panelBgClass + ' ' + borderClass);
                // Apply to submit button
                $('#submitBtn').addClass(btnBg + ' ' + btnHover + ' ' + ringClass);
            }

            // When program changes, apply theme immediately
            $('#program').on('change', function() {
                const key = $(this).val();
                applyTheme(key);
            });

            // Form submit validation
            $('#registrationForm').on('submit', function(e) {
                let isValid = true;
                $('.error-message').text('');

                // 1. Validate Full Name
                const fullName = $('#fullName').val().trim();
                const nameRegex = /^[a-zA-Z\s]+$/;
                if (!fullName) {
                    $('#fullNameError').text('Full Name is required.');
                    isValid = false;
                } else if (!nameRegex.test(fullName)) {
                     $('#fullNameError').text('Name should only contain letters and spaces.');
                    isValid = false;
                }

                // 2. Validate Email
                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    $('#emailError').text('Email Address is required.');
                    isValid = false;
                } else if (!emailRegex.test(email)) {
                    $('#emailError').text('Please enter a valid email address.');
                    isValid = false;
                }

                // 3. Validate Program Selection
                const program = $('#program').val();
                if (!program) {
                    $('#programError').text('Please select a program.');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to the first error
                    $('html, body').animate({
                        scrollTop: $('.error-message:not(:empty)').first().closest('div').offset().top - 20
                    }, 500);
                }
            });

            // Optionally, pre-apply if the program select has a value (useful for repopulated forms)
            const initialProgram = $('#program').val();
            if (initialProgram) applyTheme(initialProgram);
        });
    </script>

<?php endif; ?>

</body>
</html>
