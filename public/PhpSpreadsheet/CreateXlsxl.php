<?php

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

try {
    $path = realpath(__DIR__);

    $data = [
        'Сальков',
        'Андрей',
        'salkov_andrei@mail.ru'
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Profile');
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

    $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Roboto')
        ->setSize(15);

    $headerStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => [
                    'rgb' => '808080'
                ]
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => Color::COLOR_YELLOW
            ]
        ]
    ];

    $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
    $headerItems = [
        'Last Name',
        'Name',
        'Email'
    ];
    $sheet->fromArray([$headerItems]);

    $row = 2;

    if (is_array($data[0])) {
        foreach ($data as $rowArray) {
            $sheet->fromArray([$rowArray], null, "A$row");
            $row++;
        }
    } else {
        $sheet->fromArray([$data], null, "A$row");
    }
    $writer->save($path . '/Files/profile_' . date('Ymd_h:i:s') . '.xlsx');
} catch (Exception $e) {
    $errorMessage = date('Y/m/d H:i:s') . ': ' . $e->getMessage() . PHP_EOL;
    error_log($errorMessage, 3, $path . '/Logs/app.log');

    die($e->getMessage() . PHP_EOL);
}