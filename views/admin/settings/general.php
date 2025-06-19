<!-- /resources/views/admin/setup/general.php -->
<h1><?= htmlspecialchars($title) ?></h1>

<form action="/admin/setup/general/save" method="post">
    <label for="site_name">Site Name</label>
    <input type="text" name="site_name" id="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>

    <label for="site_description">Site Description</label>
    <textarea name="site_description" id="site_description"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>

    <button type="submit">Save Settings</button>
</form>