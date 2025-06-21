<?php if (!empty($mainMenu)): ?>
    <?php foreach ($mainMenu as $item): ?>
        <li class="relative group">
            <a href="<?= htmlspecialchars($item['url']) ?>" class="hover:text-primary transition-colors duration-300">
                <?= htmlspecialchars($item['title']) ?>
            </a>

            <?php if (!empty($item['children'])): ?>
                <ul class="submenu absolute left-0 top-full mt-1 w-48 bg-white shadow-lg rounded-md py-2 opacity-0 scale-95 invisible group-hover:visible group-hover:opacity-100 group-hover:scale-100 transition-all duration-200 ease-in-out z-50">
                    <?php foreach ($item['children'] as $child): ?>
                        <li>
                            <a href="<?= htmlspecialchars($child['url']) ?>" class="block px-4 py-2 hover:bg-gray-100 hover:text-blue-600 transition-colors duration-150">
                                <?= htmlspecialchars($child['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li class="text-sm text-gray-500">No menu items found.</li>
<?php endif; ?>