<?php
declare(strict_types=1);

$root = __DIR__;
$attachments = $root . '/attachments';
$campaign = $root . '/campaign';

mkdir($attachments, 0777, true);
mkdir($campaign, 0777, true);

file_put_contents(
    $attachments . '/logo_grafia.png',
    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
);

file_put_contents(
    $attachments . '/sample-catalog.pdf',
    <<<'PDF'
%PDF-1.0
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 3 3]>>endobj
trailer<</Size 4/Root 1 0 R>>
%%EOF
PDF
);

copy($campaign . '/target-template.csv', $campaign . '/target.csv');

echo "fixtures created\n";
