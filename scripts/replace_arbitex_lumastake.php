<?php
/**
 * Script to replace all Lumastake references with Lumastake
 * Run: php scripts/replace_lumastake_lumastake.php
 */

$replacements = [
    // Emails
    'no-reply@lumastake.com' => 'no-reply@lumastake.com',
    'support@lumastake.com' => 'support@lumastake.com',
    'inquiries@lumastake.com' => 'inquiries@lumastake.com',

    // Social handles
    '@lumastake_support' => '@lumastake_support',
    '@lumastake' => '@lumastake',

    // Company name in text (preserve CSS classes like bg-lumastake-)
    'Lumastake.' => 'Lumastake.',
    ' Lumastake ' => ' Lumastake ',
    '>Lumastake<' => '>Lumastake<',
    '"Lumastake"' => '"Lumastake"',
    '\'Lumastake\'' => '\'Lumastake\'',
    'Lumastake,' => 'Lumastake,',
    'Lumastake!' => 'Lumastake!',
    'Lumastake?' => 'Lumastake?',
    'Lumastake Security' => 'Lumastake Security',
    'lumastake.com' => 'lumastake.com',

    // Specific case preserving (for titles, headings)
    'Welcome to Lumastake' => 'Welcome to Lumastake',
    'About Lumastake' => 'About Lumastake',
    'LUMASTAKE' => 'LUMASTAKE',

    // Meta and SEO
    'about lumastake' => 'about lumastake',
   'lumastake@gmail.com' => 'lumastake@gmail.com',
];

// Directories to search
$directories = [
    'app/Models',
    'app/Mail',
    'app/Http/Controllers',
    'resources/views',
    'database/migrations',
];

$filesChanged = 0;
$totalReplacements = 0;

foreach ($directories as $directory) {
    $path = __DIR__ . '/../' . $directory;

    if (!is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $ext = $file->getExtension();
        if (!in_array($ext, ['php', 'blade'])) {
            continue;
        }

        $content = file_get_contents($file->getPathname());
        $originalContent = $content;

        // Skip files that only contain CSS classes (bg-lumastake-, text-lumastake-, etc.)
        // These should NOT be replaced
        foreach ($replacements as $search => $replace) {
            // Skip if this is a CSS class pattern
            if (strpos($search, '@') === false && preg_match('/[bg|text|border|from|to|via]-lumastake-/', $content)) {
                // Be cautious with bare "Lumastake" replacements near CSS
                if ($search === 'Lumastake' || $search === 'lumastake') {
                    continue;
                }
            }

            $content = str_replace($search, $replace, $content);
        }

        if ($content !== $originalContent) {
            file_put_contents($file->getPathname(), $content);
            $filesChanged++;
            echo "✓ Updated: " . $file->getPathname() . "\n";
        }
    }
}

echo "\n========================================\n";
echo "✅ Complete!\n";
echo "Files changed: {$filesChanged}\n";
echo "========================================\n";
