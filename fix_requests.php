<?php
// Fix all request property access
$files = [
    'app/Http/Controllers/Admin/OrderController.php',
    'app/Http/Controllers/Admin/UserController.php',
    'app/Http/Controllers/Customer/ActivityController.php',
    'app/Http/Controllers/Customer/CartController.php',
    'app/Http/Controllers/Customer/WalletController.php',
    'app/Http/Controllers/Merchant/OrderController.php',
    'app/Http/Controllers/Merchant/ProductController.php',
];

$replacements = [
    '$request->qty' => '$request->input(\'qty\')',
    '$request->amount' => '$request->input(\'amount\')',
    '$request->status' => '$request->input(\'status\')',
    '$request->role' => '$request->input(\'role\')',
    '$request->name' => '$request->input(\'name\')',
    '$request->description' => '$request->input(\'description\')',
    '$request->price' => '$request->input(\'price\')',
    '$request->stock' => '$request->input(\'stock\')',
    '$request->rating' => '$request->input(\'rating\')',
    '$request->comment' => '$request->input(\'comment\')',
    '$request->start_date' => '$request->input(\'start_date\')',
    '$request->end_date' => '$request->input(\'end_date\')',
    '$request->search' => '$request->input(\'search\')',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    }
}

echo "Done!\n";
