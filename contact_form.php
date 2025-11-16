<?php
require_once __DIR__ . '/contact_store.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$contact = null;
$mode = 'create';
if ($id !== null) {
    $contact = contact_get($id);
    if ($contact) $mode = 'edit';
}

// If there is flash old input/errors (from process), use that
$old = $_SESSION['old_input'] ?? null;
$errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['old_input'], $_SESSION['flash_errors']);
if ($old) {
    $contact = array_merge($contact ?? ['name'=>'','phone'=>'','email'=>'','address'=>''], $old);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $mode === 'edit' ? 'Edit' : 'Add' ?> Contact</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
  <div class="max-w-2xl mx-auto py-12 px-4">
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
      <!-- Header -->
      <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
        <h1 class="text-2xl font-bold text-white"><?= $mode === 'edit' ? 'Edit Contact' : 'Add New Contact' ?></h1>
        <p class="text-blue-100 mt-2"><?= $mode === 'edit' ? 'Update contact information here.' : 'Fill in the form to add a new contact.' ?></p>
      </div>

      <!-- Form Content -->
      <div class="p-8">
        <?php if (!empty($errors)): ?>
          <div class="mb-6 rounded-lg bg-red-50 border-l-4 border-red-500 p-4">
            <h3 class="font-semibold text-red-800 mb-2">⚠️ Validation Errors</h3>
            <ul class="space-y-1">
              <?php foreach ($errors as $e) : ?>
                <li class="text-red-700">• <?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="contact_process.php" method="post" novalidate class="space-y-5">
          <input type="hidden" name="mode" value="<?= $mode ?>">
          <?php if ($mode === 'edit'): ?><input type="hidden" name="index" value="<?= $id ?>"><?php endif; ?>

          <!-- Name Field -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
            <input name="name" type="text" value="<?= htmlspecialchars($contact['name'] ?? '') ?>" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition" 
              placeholder="Enter full name" required>
          </div>

          <!-- Phone Field -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
            <input name="phone" type="tel" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition" 
              placeholder="Enter phone number" required>
          </div>

          <!-- Email Field -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <input name="email" type="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition" 
              placeholder="Enter email address">
          </div>

          <!-- Address Field -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
            <textarea name="address" rows="4" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition resize-none" 
              placeholder="Enter address"><?= htmlspecialchars($contact['address'] ?? '') ?></textarea>
          </div>

          <!-- Buttons -->
          <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 rounded-lg transition shadow-md">
              <?= $mode === 'edit' ? '💾 Save Changes' : '✚ Add Contact' ?>
            </button>
            <a href="contact_list.php" class="px-6 py-2.5 text-gray-700 font-semibold border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>