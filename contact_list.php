<?php
require_once __DIR__ . '/contact_store.php';

// Get flash message if available
$success = $_SESSION['flash_success'] ?? '';
$errors  = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

$contacts = contact_all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact List</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
  <div class="max-w-6xl mx-auto py-12 px-4">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
      <!-- Header -->
      <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white">Contact List</h1>
          <p class="text-blue-100 mt-1">Manage your contacts efficiently</p>
        </div>
        <a href="contact_form.php" class="inline-flex items-center gap-2 bg-white hover:bg-blue-50 text-blue-600 font-semibold px-6 py-3 rounded-lg transition shadow-md">
          ✚ Add Contact
        </a>
      </div>

      <!-- Messages -->
      <div class="p-8">
        <?php if ($success): ?>
          <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 text-green-800">
            <h3 class="font-semibold mb-1">✓ Success</h3>
            <p><?= htmlspecialchars($success) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
            <h3 class="font-semibold text-red-800 mb-2">⚠️ Errors</h3>
            <ul class="space-y-1">
              <?php foreach ($errors as $e): ?>
                <li class="text-red-700">• <?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if (empty($contacts)): ?>
          <div class="text-center py-16 text-gray-600">
            <svg class="mx-auto mb-4 w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 108 0 4 4 0 00-8 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
            </svg>
            <p class="text-lg font-semibold">No contacts yet</p>
            <p class="mt-2 text-gray-500">Start by adding your first contact</p>
            <p class="mt-4"><a href="contact_form.php" class="text-blue-600 hover:text-blue-700 font-semibold">➕ Add your first contact</a></p>
          </div>
        <?php else: ?>
          <!-- Table -->
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
                <?php foreach ($contacts as $i => $c): ?>
                  <tr class="hover:bg-blue-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700"><?= $i+1 ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= htmlspecialchars($c['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['phone']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['email']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs"><?= nl2br(htmlspecialchars($c['address'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                      <a href="contact_form.php?id=<?= $i ?>" class="inline-block px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 font-semibold transition">✎ Edit</a>
                      <a href="contact_delete.php?id=<?= $i ?>" class="inline-block px-4 py-2 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 font-semibold transition" onclick="return confirm('Are you sure you want to delete this contact?')">🗑 Delete</a>
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