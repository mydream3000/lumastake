<?php
/**
 * Script to replace all Arbitex references with Lumastake
 * Run: php scripts/replace_arbitex_lumastake.php
 */

$replacements = [
    // Emails
    'no-reply@arbitex.io' => 'no-reply@lumastake.com',
    'support@arbitex.io' => 'support@lumastake.com',
    'inquiries@arbitex.io' => 'inquiries@lumastake.com',

    // Social handles
    '@arbitex_support' => '@lumastake_support',
    '@arbitex' => '@lumastake',

    // Company name in text (preserve CSS classes like bg-arbitex-)
    'Arbitex.' => 'Lumastake.',
    ' Arbitex ' => ' Lumastake ',
    '>Arbitex<' => '>Lumastake<',
    '"Arbitex"' => '"Lumastake"',
    '\'Arbitex\'' => '\'Lumastake\'',
    'Arbitex,' => 'Lumastake,',
    'Arbitex!' => 'Lumastake!',
    'Arbitex?' => 'Lumastake?',
    'Arbitex Security' => 'Lumastake Security',
    'arbitex.io' => 'lumastake.com',

    // Specific case preserving (for titles, headings)
    'Welcome to Arbitex' => 'Welcome to Lumastake',
    'About Arbitex' => 'About Lumastake',
    'ARBITEX' => 'LUMASTAKE',

    // Meta and SEO
    'about arbitex' => 'about lumastake',
   'arbitex@gmail.com' => 'lumastake@gmail.com',
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

        // Skip files that only contain CSS classes (bg-arbitex-, text-arbitex-, etc.)
        // These should NOT be replaced
        foreach ($replacements as $search => $replace) {
            // Skip if this is a CSS class pattern
            if (strpos($search, '@') === false && preg_match('/[bg|text|border|from|to|via]-arbitex-/', $content)) {
                // Be cautious with bare "Arbitex" replacements near CSS
                if ($search === 'Arbitex' || $search === 'arbitex') {
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
