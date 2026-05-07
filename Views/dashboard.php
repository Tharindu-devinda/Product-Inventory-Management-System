<?php
/** @var array $stats */
$stats = $stats ?? ['products' => 0, 'employees' => 0, 'suppliers' => 0];
?>
<div class="py-5 mx-2">
    <h1 class="text-3xl font-bold mb-4">Dashboard</h1>

    <div class="grid grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-semibold">Total Products</h2>
            <p class="text-2xl font-bold"><?php echo (int) $stats['products']; ?></p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-semibold">Total Employees</h2>
            <p class="text-2xl font-bold"><?php echo (int) $stats['employees']; ?></p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-semibold">Total Suppliers</h2>
            <p class="text-2xl font-bold">
                <?php echo (int) $stats['suppliers']; ?>
            </p>
        </div>
    </div>
</div>