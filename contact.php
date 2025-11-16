<?php
session_start();

if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

$errors = [];
$success = '';
$editingIndex = null;

$formData = [
    'name'    => '',
    'phone'   => '',
    'email'   => '',
    'address' => ''
];

// ---------------------------
// DELETE CONTACT (action=delete)
// ---------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if (isset($_SESSION['contacts'][$id])) {
        unset($_SESSION['contacts'][$id]);
        // reorganize array index
        $_SESSION['contacts'] = array_values($_SESSION['contacts']);
        $success = 'Contact successfully deleted.';
    }
}

// ---------------------------
// PROCESS FORM (Add / Edit)
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $mode    = $_POST['mode'] ?? 'create';
    $idx     = isset($_POST['index']) ? (int) $_POST['index'] : null;

    // Simple validation
    if ($name === '') {
        $errors['name'] = 'Full name is required.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9+ ]+$/', $phone)) {
        $errors['phone'] = 'Phone number can only contain digits, spaces, and +.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email format is invalid.';
    }

    // If no errors, save to session
    if (empty($errors)) {
        $data = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'address' => $address
        ];

        if ($mode === 'edit' && $idx !== null && isset($_SESSION['contacts'][$idx])) {
            $_SESSION['contacts'][$idx] = $data;
            $success = 'Contact successfully updated.';
        } else {
            $_SESSION['contacts'][] = $data;
            $success = 'New contact successfully added.';
        }

        // reset form after success
        $formData = [
            'name'    => '',
            'phone'   => '',
            'email'   => '',
            'address' => ''
        ];
        $editingIndex = null;

        // (Optional) To avoid resubmit on refresh:
        // header('Location: contact.php');
        // exit;
    } else {
        // if there are errors, refill form with submitted data
        $formData = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'address' => $address
        ];
        if ($mode === 'edit') {
            $editingIndex = $idx;
        }
    }
}

// ---------------------------
// EDIT MODE (prefill form)
// ---------------------------
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if (isset($_SESSION['contacts'][$id])) {
        $editingIndex = $id;
        $formData = $_SESSION['contacts'][$id];
    }
}

$isEditing = $editingIndex !== null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h1 class="text-3xl font-bold text-white">Contact Management System</h1>
            <p class="text-blue-100 mt-2">Store and manage your contacts efficiently</p>
            <p class="text-blue-100 text-sm mt-3">
                Data is stored in <strong>PHP session</strong> (no database).
                Total contacts: <strong class="text-white"><?= count($_SESSION['contacts']); ?></strong>
            </p>
        </div>

        <div class="p-8">
            <!-- Messages -->
            <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 text-green-800">
                    <h3 class="font-semibold mb-1">✓ Success</h3>
                    <p><?= htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
                    <h3 class="font-semibold text-red-800 mb-2">⚠️ Validation Errors</h3>
                    <ul class="space-y-1">
                        <?php foreach ($errors as $msg): ?>
                            <li class="text-red-700">• <?= htmlspecialchars($msg); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- FORM ADD / EDIT CONTACT -->
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $isEditing ? 'Edit Contact' : 'Add New Contact'; ?></h2>
            <form method="post" action="" class="space-y-5">
                <input type="hidden" name="mode" value="<?= $isEditing ? 'edit' : 'create'; ?>">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="index" value="<?= (int)$editingIndex; ?>">
                <?php endif; ?>

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= htmlspecialchars($formData['name']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition"
                           placeholder="Enter full name" required>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" name="phone"
                           value="<?= htmlspecialchars($formData['phone']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition"
                           placeholder="Enter phone number" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address (Optional)</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($formData['email']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition"
                           placeholder="Enter email address">
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address (Optional)</label>
                    <textarea id="address" name="address"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition resize-none"
                              placeholder="Enter address"
                              rows="4"><?= htmlspecialchars($formData['address']); ?></textarea>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 rounded-lg transition shadow-md">
                        <?= $isEditing ? '💾 Save Changes' : '✚ Add Contact'; ?>
                    </button>
                    <?php if ($isEditing): ?>
                        <a href="contact.php" class="px-6 py-2.5 text-gray-700 font-semibold border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- CONTACT LIST -->
            <h2 class="text-2xl font-bold text-gray-800 mb-6 mt-10">Contact List</h2>

            <?php if (empty($_SESSION['contacts'])): ?>
                <div class="text-center py-16 text-gray-600">
                    <svg class="mx-auto mb-4 w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 108 0 4 4 0 00-8 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                    </svg>
                    <p class="text-lg font-semibold">No contacts yet</p>
                    <p class="mt-2 text-gray-500">Start by adding your first contact using the form above</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                        <?php foreach ($_SESSION['contacts'] as $i => $c): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700"><?= $i + 1; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= htmlspecialchars($c['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['phone']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['email']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs"><?= nl2br(htmlspecialchars($c['address'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <a href="?action=edit&id=<?= $i; ?>" class="inline-block px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 font-semibold transition">✎ Edit</a>
                                    <a href="?action=delete&id=<?= $i; ?>"
                                       onclick="return confirm('Are you sure you want to delete this contact?');"
                                       class="inline-block px-4 py-2 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 font-semibold transition">
                                        🗑 Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>