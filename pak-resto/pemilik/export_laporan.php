<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}


/*
 * Kasir dan Pemilik boleh export laporan.
 */
if (
    $_SESSION['role'] !== 'pemilik' &&
    $_SESSION['role'] !== 'kasir'
) {
    die("Akses ditolak.");
}


require_once "../config/database.php";


/*
 * Ambil periode dari URL.
 *
 * Contoh:
 *
 * export_laporan.php?periode=bulan
 */
$periode = $_GET['periode'] ?? 'bulan';


/*
 * Tentukan tanggal berdasarkan periode.
 */

switch ($periode) {

    case 'hari':

        $tanggal_mulai = date('Y-m-d');
        $tanggal_selesai = date('Y-m-d');

        $label_periode = 'Hari Ini';

        $nama_periode = 'hari';

        break;


    case 'minggu':

        $tanggal_mulai =
            date(
                'Y-m-d',
                strtotime('monday this week')
            );

        $tanggal_selesai =
            date(
                'Y-m-d',
                strtotime('sunday this week')
            );

        $label_periode = 'Minggu Ini';

        $nama_periode = 'minggu';

        break;


    case 'tahun':

        $tanggal_mulai =
            date('Y-01-01');

        $tanggal_selesai =
            date('Y-12-31');

        $label_periode = 'Tahun Ini';

        $nama_periode = 'tahun';

        break;


    case 'bulan':
    default:

        $tanggal_mulai =
            date('Y-m-01');

        $tanggal_selesai =
            date('Y-m-t');

        $label_periode = 'Bulan Ini';

        $nama_periode = 'bulan';

        break;
}


/*
 * Ambil pembayaran yang berhasil.
 */
$sql = "

    SELECT

        pb.id_pembayaran,
        pb.id_pesanan,
        pb.total_bayar,
        pb.metode_bayar,
        pb.status_validasi,
        pb.waktu_bayar,
        pg.nama_pegawai

    FROM pembayaran pb

    INNER JOIN pegawai pg
        ON pb.id_pegawai = pg.id_pegawai

    WHERE pb.status_validasi = 'berhasil'

    AND DATE(pb.waktu_bayar)
        BETWEEN ? AND ?

    ORDER BY
        pb.waktu_bayar ASC

";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);

$stmt->execute();

$result =
    $stmt->get_result();


$transaksi = [];

$total_pendapatan = 0;

$jumlah_transaksi = 0;


while ($row = $result->fetch_assoc()) {

    $transaksi[] = $row;

    $total_pendapatan +=
        (float) $row['total_bayar'];

    $jumlah_transaksi++;
}


/*
 * Nama file XLSX.
 */
$filename =
    "laporan_pendapatan_"
    . $nama_periode
    . "_"
    . date('Y-m-d')
    . ".xlsx";


/*
 * XLSX sebenarnya adalah file ZIP
 * yang berisi beberapa file XML.
 *
 * Jadi kita dapat membuat XLSX
 * tanpa library tambahan.
 */


/*
 * Pastikan ZipArchive tersedia.
 */
if (!class_exists('ZipArchive')) {

    die(
        "Extension ZipArchive PHP belum aktif. "
        . "Aktifkan extension=zip pada php.ini."
    );

}


/*
 * Buat temporary file.
 */
$temp_file =
    tempnam(
        sys_get_temp_dir(),
        'laporan_'
    );


$zip =
    new ZipArchive();


if (
    $zip->open(
        $temp_file,
        ZipArchive::CREATE |
        ZipArchive::OVERWRITE
    ) !== true
) {

    die(
        "Gagal membuat file XLSX."
    );

}


/*
 * ================================
 * CONTENT TYPES
 * ================================
 */

$zip->addFromString(
    '[Content_Types].xml',
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <Types
        xmlns="http://schemas.openxmlformats.org/package/2006/content-types">

        <Default
            Extension="rels"
            ContentType="application/vnd.openxmlformats-package.relationships+xml"/>

        <Default
            Extension="xml"
            ContentType="application/xml"/>

        <Override
            PartName="/xl/workbook.xml"
            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>

        <Override
            PartName="/xl/worksheets/sheet1.xml"
            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>

        <Override
            PartName="/xl/styles.xml"
            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>

    </Types>'
);


/*
 * ================================
 * ROOT RELATIONSHIPS
 * ================================
 */

$zip->addFromString(
    '_rels/.rels',
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

    <Relationships
        xmlns="http://schemas.openxmlformats.org/package/2006/relationships">

        <Relationship
            Id="rId1"
            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
            Target="xl/workbook.xml"/>

    </Relationships>'
);


/*
 * ================================
 * WORKBOOK
 * ================================
 */

$zip->addFromString(
    'xl/workbook.xml',
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

    <workbook
        xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
        xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">

        <sheets>

            <sheet
                name="Laporan Pendapatan"
                sheetId="1"
                r:id="rId1"/>

        </sheets>

    </workbook>'
);


/*
 * ================================
 * WORKBOOK RELATIONSHIPS
 * ================================
 */

$zip->addFromString(
    'xl/_rels/workbook.xml.rels',
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

    <Relationships
        xmlns="http://schemas.openxmlformats.org/package/2006/relationships">

        <Relationship
            Id="rId1"
            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
            Target="worksheets/sheet1.xml"/>

        <Relationship
            Id="rId2"
            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"
            Target="styles.xml"/>

    </Relationships>'
);


/*
 * ================================
 * STYLES
 * ================================
 */

$zip->addFromString(
    'xl/styles.xml',
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

    <styleSheet
        xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">

        <fonts count="2">

            <font>
                <sz val="11"/>
                <name val="Arial"/>
            </font>

            <font>
                <b/>
                <sz val="11"/>
                <name val="Arial"/>
            </font>

        </fonts>


        <fills count="2">

            <fill>
                <patternFill patternType="none"/>
            </fill>

            <fill>
                <patternFill patternType="solid">
                    <fgColor rgb="D9EAF7"/>
                </patternFill>
            </fill>

        </fills>


        <borders count="1">

            <border>
                <left/>
                <right/>
                <top/>
                <bottom/>
                <diagonal/>
            </border>

        </borders>


        <cellXfs count="2">

            <xf
                numFmtId="0"
                fontId="0"
                fillId="0"
                borderId="0"/>

            <xf
                numFmtId="0"
                fontId="1"
                fillId="1"
                borderId="0"/>

        </cellXfs>

    </styleSheet>'
);


/*
 * ================================
 * HELPER XML FUNCTIONS
 * ================================
 */

function xmlEscape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_XML1 | ENT_QUOTES,
        'UTF-8'
    );
}


function excelColumnName($number)
{
    $name = '';

    while ($number > 0) {

        $number--;

        $name =
            chr(
                65 + ($number % 26)
            )
            . $name;

        $number =
            intdiv(
                $number,
                26
            );
    }

    return $name;
}


function excelCell(
    $column,
    $row,
    $value,
    $style = 0
) {

    return
        '<c r="'
        . $column
        . $row
        . '" s="'
        . $style
        . '" t="inlineStr">'
        . '<is><t>'
        . xmlEscape($value)
        . '</t></is>'
        . '</c>';

}


/*
 * ================================
 * BUILD WORKSHEET
 * ================================
 */

$sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

<worksheet
    xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">

<sheetData>';


/*
 * Title.
 */

$sheet .= '<row r="1">';

$sheet .= excelCell(
    'A',
    1,
    'LAPORAN PENDAPATAN PAK RESTO',
    1
);

$sheet .= '</row>';


/*
 * Periode.
 */

$sheet .= '<row r="2">';

$sheet .= excelCell(
    'A',
    2,
    'Periode'
);

$sheet .= excelCell(
    'B',
    2,
    $label_periode
);

$sheet .= '</row>';


/*
 * Tanggal.
 */

$sheet .= '<row r="3">';

$sheet .= excelCell(
    'A',
    3,
    'Tanggal Mulai'
);

$sheet .= excelCell(
    'B',
    3,
    $tanggal_mulai
);

$sheet .= '</row>';


$sheet .= '<row r="4">';

$sheet .= excelCell(
    'A',
    4,
    'Tanggal Selesai'
);

$sheet .= excelCell(
    'B',
    4,
    $tanggal_selesai
);

$sheet .= '</row>';


/*
 * Summary.
 */

$sheet .= '<row r="6">';

$sheet .= excelCell(
    'A',
    6,
    'Jumlah Transaksi',
    1
);

$sheet .= excelCell(
    'B',
    6,
    $jumlah_transaksi,
    1
);

$sheet .= '</row>';


$sheet .= '<row r="7">';

$sheet .= excelCell(
    'A',
    7,
    'Total Pendapatan',
    1
);

$sheet .= excelCell(
    'B',
    7,
    'Rp '
    . number_format(
        $total_pendapatan,
        0,
        ',',
        '.'
    ),
    1
);

$sheet .= '</row>';


/*
 * Header detail transaksi.
 */

$headerRow = 9;

$headers = [

    'ID Pembayaran',
    'ID Pesanan',
    'Total Bayar',
    'Metode Bayar',
    'Status Validasi',
    'Waktu Bayar',
    'Kasir'

];


$sheet .=
    '<row r="'
    . $headerRow
    . '">';


foreach (
    $headers as $index => $header
) {

    $column =
        excelColumnName(
            $index + 1
        );

    $sheet .= excelCell(
        $column,
        $headerRow,
        $header,
        1
    );

}


$sheet .= '</row>';


/*
 * Detail transaksi.
 */

$currentRow = 10;


foreach (
    $transaksi as $row
) {

    $values = [

        $row['id_pembayaran'],

        $row['id_pesanan'],

        'Rp '
        . number_format(
            $row['total_bayar'],
            0,
            ',',
            '.'
        ),

        $row['metode_bayar'],

        $row['status_validasi'],

        $row['waktu_bayar'],

        $row['nama_pegawai']

    ];


    $sheet .=
        '<row r="'
        . $currentRow
        . '">';


    foreach (
        $values as $index => $value
    ) {

        $column =
            excelColumnName(
                $index + 1
            );

        $sheet .= excelCell(
            $column,
            $currentRow,
            $value
        );

    }


    $sheet .= '</row>';


    $currentRow++;

}


$sheet .= '

</sheetData>

</worksheet>
';


/*
 * Tambahkan worksheet.
 */

$zip->addFromString(
    'xl/worksheets/sheet1.xml',
    $sheet
);


/*
 * Selesai membuat XLSX.
 */

$zip->close();


/*
 * Kirim file ke browser.
 */

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header(
    'Content-Length: ' .
    filesize($temp_file)
);

header(
    'Cache-Control: max-age=0'
);


readfile($temp_file);


/*
 * Hapus temporary file.
 */

unlink($temp_file);

exit;