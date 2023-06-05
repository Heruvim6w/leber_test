<?php
function getImageType($extension): string
{
    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            return 'jpeg';

        case 'png':
            return 'png';

        default:
            throw new InvalidArgumentException('File extension "' . $extension . '" is not valid for images.');
            break;
    }
}

function watermarkImages($imagesDirectory): void
{
    $watermark = imagecreatefrompng($imagesDirectory . 'watermark.png');
    $margeRight = 15;
    $margeBottom = 15;
    $sizeX = imagesx($watermark);
    $sizeY = imagesy($watermark);

    $files = scandir($imagesDirectory);

    foreach ($files as $file) {
        if (basename($file) !== 'watermark.png' && is_file($imagesDirectory . '/' . $file)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            try {
                $image = [
                    'image' => null,
                    'extension' => null,
                ];

                $image['extension'] = $extension;
                $imageType = getImageType($extension);

                $image['image'] = imagecreatefromstring(file_get_contents($imagesDirectory . $file));
                imagesavealpha($image['image'], true);
                imagecopy(
                    $image['image'],
                    $watermark,
                    imagesx($image['image']) - $sizeX - $margeRight,
                    imagesy($image['image']) - $sizeY - $margeBottom,
                    0,
                    0,
                    imagesx($watermark),
                    imagesy($watermark)
                );

                header('Content-type: image/' . $imageType);
                $markedFileName = $imagesDirectory . 'marked/' . date('Ymd_h:i:s') . '.' . $extension;

                if ($imageType === 'jpeg') {
                    imagejpeg($image['image'], $markedFileName, 100);
                } elseif ($imageType === 'png') {
                    imagepng($image['image'], $markedFileName, 9);
                }

                imagedestroy($image['image']);

            } catch (Exception $exception) {
                $errorMessage = $exception->getMessage();
                print_r("Image: $file \n Error: $errorMessage \n");
            }
        }
    }
    print_r("Success \n");
}

$imagesDirectory = __DIR__ . '/images/';
watermarkImages($imagesDirectory);
