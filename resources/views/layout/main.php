<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Stride App') ?></title>
    <style>
        body { font-family: system-ui, sans-serif; line-height: 1.5; padding: 2rem; max-width: 800px; margin: 0 auto; }
        header, footer { padding: 1rem 0; border-bottom: 1px solid #eee; }
        footer { border-bottom: none; border-top: 1px solid #eee; margin-top: 2rem; font-size: 0.9em; color: #666; }
        h1 { margin-top: 0; }
    </style>
</head>
<body>

    <header>
        <strong>Stride</strong>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        &copy; <?= date('Y') ?> 2bo Stride
    </footer>

</body>
</html>
