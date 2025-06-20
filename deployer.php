<?php
// === BASIC AUTHENTICATION ===
$valid_username = 'admin';
$valid_password = 'securepassword123';

if (
    !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $valid_username || $_SERVER['PHP_AUTH_PW'] !== $valid_password
) {

    header('WWW-Authenticate: Basic realm="Deployment Tool"');
    header('HTTP/1.0 401 Unauthorized');
    echo '⚠️ You must enter the correct username and password to access this tool.';
    exit;
}

// === CONFIGURATION ===
$scriptSelfPath = __FILE__;
$scriptSelfDir = realpath(dirname($scriptSelfPath));

$fromDir = isset($_POST['from_dir']) ? rtrim($_POST['from_dir'], '/') : $scriptSelfDir;
$toDir = isset($_POST['to_dir']) ? rtrim($_POST['to_dir'], '/') : $scriptSelfDir;

$errors = [];
$log = [];
$filesToCopy = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!is_dir($fromDir)) {
        $errors[] = "Source directory does not exist: $fromDir";
    } elseif (!is_readable($fromDir)) {
        $errors[] = "Cannot read from source directory: $fromDir";
    }

    if (!is_dir($toDir)) {
        $errors[] = "Target directory does not exist: $toDir";
    } else {
        if (!is_writable($toDir)) {
            $errors[] = "Target directory is not writable: $toDir";
        }
    }

    if (realpath($fromDir) === realpath($toDir)) {
        $errors[] = "Source and target directories cannot be the same.";
    }

    if (!count($errors)) {
        $log[] = "✅ Validated source and target directories.";

        if ($_POST['action'] === 'preview') {
            foreach ($_POST['items'] ?? [] as $relPath) {
                $sourceFile = $fromDir . '/' . $relPath;
                $targetFile = $toDir . '/' . $relPath;

                if (file_exists($sourceFile)) {
                    $filesToCopy[$relPath] = $targetFile;
                    $log[] = "Would copy: $relPath";
                }
            }
        } elseif ($_POST['action'] === 'deploy') {
            foreach ($_POST['items'] ?? [] as $relPath) {
                $sourceFile = $fromDir . '/' . $relPath;
                $targetFile = $toDir . '/' . $relPath;

                if (is_file($sourceFile)) {
                    if (copy($sourceFile, $targetFile)) {
                        $log[] = "✅ Copied file: $relPath";
                    } else {
                        $errors[] = "❌ Failed to copy file: $relPath";
                    }
                } elseif (is_dir($sourceFile)) {
                    if (!is_dir($targetFile)) {
                        if (mkdir($targetFile, 0755, true)) {
                            $log[] = "📁 Created directory: $relPath";
                        } else {
                            $errors[] = "❌ Failed to create directory: $relPath";
                        }
                    } else {
                        $log[] = "📂 Skipped (already exists): $relPath";
                    }
                }
            }
        }
    }
}

function buildTree($dir)
{
    $items = [];

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $dir . '/' . $item;
        $relativePath = str_replace(realpath(dirname(__DIR__)) . '/', '', $fullPath);

        $node = ['name' => $item, 'path' => $relativePath];

        if (is_dir($fullPath)) {
            $node['type'] = 'dir';
            $node['children'] = buildTree($fullPath);
        } else {
            $node['type'] = 'file';
        }

        $items[] = $node;
    }

    // Sort: folders first, then files — both alphabetically
    usort($items, function ($a, $b) {
        // Folders come before files
        if ($a['type'] !== $b['type']) {
            return $a['type'] === 'dir' ? -1 : 1;
        }
        // Alphabetical order within same type
        return strcmp(strtolower($a['name']), strtolower($b['name']));
    });

    return $items;
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } else {
        return '0 bytes';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Headtilts Deployment Helper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        input[type='checkbox']:checked+label::before {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        input[type='checkbox']:indeterminate+label::before {
            background-color: #f59e0b !important;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 p-6">

    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">🚀 Headtilts Deployment Helper</h1>

        <form method="post" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Source Directory</label>
                    <input type="text" name="from_dir" value="<?= htmlspecialchars($fromDir) ?>" class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block font-semibold mb-1">Target Directory</label>
                    <input type="text" name="to_dir" value="<?= htmlspecialchars($toDir) ?>" class="w-full border px-3 py-2 rounded" />
                </div>
            </div>
            <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Load Files</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])): ?>
            <?php $tree = buildTree($fromDir); ?>

            <form method="post" id="deployForm">
                <input type="hidden" name="from_dir" value="<?= htmlspecialchars($fromDir) ?>">
                <input type="hidden" name="to_dir" value="<?= htmlspecialchars($toDir) ?>">

                <h2 class="font-bold mt-6 mb-2">📂 Select Files to Deploy</h2>
                <ul class="ml-4 space-y-1">
                    <?php function renderTree($nodes, $parentPath = '', $fromDir = '')
                    {
                        foreach ($nodes as $node):
                            $currentPath = $parentPath ? $parentPath . '/' . $node['name'] : $node['name'];
                            $isDir = $node['type'] === 'dir';
                            $icon = $isDir ?
                                '<i class="fas fa-folder w-4 h-4 mr-1 text-blue-500"></i>' :
                                '';
                            if (!$isDir) {
                                $filePath = $fromDir . '/' . $currentPath;
                                $size = filesize($filePath);
                                $sizeLabel = formatSizeUnits($size);

                                // Match file type to appropriate Font Awesome icon
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                switch ($ext) {
                                    case 'php':
                                        $icon = '<i class="fas fa-file-code w-4 h-4 mr-1 text-purple-500"></i>';
                                        break;
                                    case 'js':
                                        $icon = '<i class="fas fa-file-code w-4 h-4 mr-1 text-yellow-500"></i>';
                                        break;
                                    case 'css':
                                        $icon = '<i class="fas fa-file-code w-4 h-4 mr-1 text-blue-400"></i>';
                                        break;
                                    case 'html':
                                    case 'htm':
                                        $icon = '<i class="fas fa-file-code w-4 h-4 mr-1 text-orange-500"></i>';
                                        break;
                                    case 'json':
                                        $icon = '<i class="fas fa-file-alt w-4 h-4 mr-1 text-green-500"></i>';
                                        break;
                                    case 'txt':
                                        $icon = '<i class="fas fa-file-alt w-4 h-4 mr-1 text-gray-500"></i>';
                                        break;
                                    case 'md':
                                        $icon = '<i class="fas fa-file-alt w-4 h-4 mr-1 text-gray-400"></i>';
                                        break;
                                    case 'png':
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'gif':
                                    case 'svg':
                                        $icon = '<i class="fas fa-file-image w-4 h-4 mr-1 text-pink-500"></i>';
                                        break;
                                    case 'pdf':
                                        $icon = '<i class="fas fa-file-pdf w-4 h-4 mr-1 text-red-500"></i>';
                                        break;
                                    case 'zip':
                                    case 'tar':
                                    case 'gz':
                                        $icon = '<i class="fas fa-file-archive w-4 h-4 mr-1 text-gray-600"></i>';
                                        break;
                                    default:
                                        $icon = '<i class="fas fa-file w-4 h-4 mr-1 text-gray-500"></i>';
                                }
                            }
                    ?>
                            <li class="folder-node">
                                <div class="flex items-center">
                                    <!-- Folder Toggle Button -->
                                    <?php if (!empty($node['children'])): ?>
                                        <button type="button" class="folder-toggle mr-1 text-gray-500 hover:text-blue-600 focus:outline-none">
                                            <svg class="w-3 h-3 transition-transform transform rotate-0 folder-icon" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Checkbox + Icon + Name + File Size -->
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox"
                                            name="items[]"
                                            value="<?= htmlspecialchars($currentPath) ?>"
                                            class="form-checkbox h-4 w-4 text-blue-600 mr-2"
                                            data-path="<?= htmlspecialchars($currentPath) ?>" />
                                        <span class="flex items-center">
                                            <?= $icon ?>
                                            <span class="ml-2"><?= htmlspecialchars($node['name']) ?></span>
                                            <?php if (!$isDir): ?>
                                                <span class="text-xs text-gray-500 ml-2">(<?= $sizeLabel ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                </div>

                                <!-- Children -->
                                <?php if (!empty($node['children'])): ?>
                                    <ul class="ml-6 mt-1 space-y-1 hidden folder-content">
                                        <?= renderTree($node['children'], $currentPath, $fromDir) ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                    <?php
                        endforeach;
                    } // End of function renderTree()

                    renderTree($tree, '', $fromDir);
                    ?>
                </ul>

                <div class="mt-6 flex gap-4">
                    <button type="submit" name="action" value="preview"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-full">👀 Preview Selected</button>
                    <button type="submit" name="action" value="deploy"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full">🚀 Deploy Selected</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && count($log)): ?>
            <h2 class="font-bold mt-6 mb-2">📋 Preview Log</h2>
            <pre class="mt-2 bg-gray-100 p-4 rounded text-sm text-green-800 max-h-64 overflow-auto">
<?= implode("\n", array_map('htmlspecialchars', $log)) ?>
</pre>

            <!-- Navigation Buttons -->
            <div class="mt-6 flex gap-4">
                <form method="post" class="inline">
                    <input type="hidden" name="from_dir" value="<?= htmlspecialchars($fromDir) ?>">
                    <input type="hidden" name="to_dir" value="<?= htmlspecialchars($toDir) ?>">
                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full">
                        🔙 Back to File Selection
                    </button>
                </form>

                <?php if ($_POST['action'] === 'preview'): ?>
                    <form method="post" class="inline">
                        <input type="hidden" name="from_dir" value="<?= htmlspecialchars($fromDir) ?>">
                        <input type="hidden" name="to_dir" value="<?= htmlspecialchars($toDir) ?>">
                        <?php foreach ($_POST['items'] ?? [] as $item): ?>
                            <input type="hidden" name="items[]" value="<?= htmlspecialchars($item) ?>">
                        <?php endforeach; ?>
                        <button type="submit" name="action" value="deploy"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full">
                            🚀 Deploy Selected
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (count($errors)): ?>
            <pre class="mt-6 bg-red-100 p-4 rounded text-sm text-red-800 max-h-96 overflow-auto">
      <?= implode("\n", array_map('htmlspecialchars', $errors)) ?>
    </pre>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('.folder-toggle');
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            // Folder expand/collapse
            toggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const content = toggle.closest('li').querySelector('.folder-content');
                    const icon = toggle.querySelector('.folder-icon');
                    const isVisible = !content.classList.contains('hidden');
                    content.classList.toggle('hidden');
                    icon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(90deg)';
                });
            });

            // Cascading checkbox logic
            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    cascadeCheck(cb);
                    updateParentCheckboxes(cb);
                });
            });

            function cascadeCheck(checkbox) {
                const parentLi = checkbox.closest('li');
                const children = parentLi.querySelectorAll('ul input[type="checkbox"]');

                children.forEach(child => {
                    if (!child.disabled) {
                        child.checked = checkbox.checked;
                        child.indeterminate = false;
                    }
                });
            }

            function updateParentCheckboxes(childCheckbox) {
                let current = childCheckbox;

                while (true) {
                    const parentLi = current.closest('ul')?.closest('li');
                    if (!parentLi) break;

                    const parentCheckbox = parentLi.querySelector('input[type="checkbox"]');
                    const allChildren = parentLi.querySelectorAll('ul input[type="checkbox"]');
                    const allChecked = [...allChildren].every(cb => cb.checked);
                    const someChecked = [...allChildren].some(cb => cb.checked);

                    if (parentCheckbox) {
                        parentCheckbox.checked = allChecked;
                        parentCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    current = parentCheckbox;
                }
            }
        });
    </script>

</body>

</html>