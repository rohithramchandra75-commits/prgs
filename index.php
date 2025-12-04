<?php
// --- PHP Logic for Processing & Routing ---
$is_submitted = false;
$fullName = $email = $programName = $appId = "";

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

    // Simple validation: Ensure required fields are present
    if (!empty($fullName) && !empty($email) && !empty($program) && $program != 'Select a Program') {
        $is_submitted = true;
        $programMap = ['CS' => 'Computer Science', 'IT' => 'Information Technology', 'EC' => 'Electronics & Communication'];
        $programName = $programMap[$program] ?? "Unknown Program";
        $appId = "REG-" . rand(10000, 99999);
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_submitted ? "Submission Success" : "Online Registration Form"; ?></title>
    <!-- Load Tailwind CSS and jQuery -->
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

    <div class="w-full max-w-xl bg-white p-8 md:p-10 rounded-xl shadow-2xl border-4 border-indigo-400 success-card">
        <div class="text-center mb-8">
            <span class="text-6xl mb-4 block animate-bounce">🎉</span>
            <h1 class="text-3xl font-extrabold text-indigo-700 mt-4">Woohoo! Application Submitted!</h1>
            <p class="text-gray-600 mt-2">You're all set! We received your details and we're so excited to have you.</p>
        </div>

        <div class="bg-indigo-50 p-4 rounded-lg mb-6 border-l-4 border-indigo-500">
            <p class="text-sm font-semibold text-indigo-700">Your Reference ID:</p>
            <p class="text-2xl font-black text-indigo-900 mt-1"><?php echo htmlspecialchars($appId); ?></p>
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
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border-l-4 border-green-400">
                <span class="text-sm font-medium text-gray-600">Program:</span>
                <span class="text-base font-bold text-green-700"><?php echo htmlspecialchars($programName); ?></span>
            </div>
        </div>

        <div class="mt-8 text-center">
             <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="inline-block py-2 px-6 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition duration-150">Register Another Application</a>
        </div>
    </div>

<?php else: // Display REGISTRATION FORM ?>

    <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl border border-gray-100">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">Online Registration Form</h1>
        <p class="text-gray-500 mb-8 text-center">Please fill out your details below to register for the program.</p>

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
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                    <option value="">Select a Program</option>
                    <option value="CS">Computer Science</option>
                    <option value="IT">Information Technology</option>
                    <option value="EC">Electronics & Communication</option>
                </select>
                <div id="programError" class="error-message"></div>
            </div>

            <!-- Submission Button -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    Submit Application
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript Validation Logic -->
    <script>
        $(document).ready(function() {
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
        });
    </script>

<?php endif; ?>

</body>
</html>
