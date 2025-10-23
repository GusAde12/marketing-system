<?php
require('assets/lib/fpdf.php');
include "koneksi.php";

if (!isset($_GET['faktur'])) {
    die("Faktur tidak ditemukan.");
}
$no_faktur = $_GET['faktur'];

// Ambil data header pembelian
$stmt = $conn->prepare("SELECT * FROM pembelian WHERE no_faktur_pembelian = ?");
$stmt->bind_param("s", $no_faktur);
$stmt->execute();
$result = $stmt->get_result();
$pembelian = $result->fetch_assoc();

if (!$pembelian) {
    die("Data pembelian tidak ditemukan.");
}

// Ambil data detail barang
$detail = [];
$stmt2 = $conn->prepare("
    SELECT d.id_barang, d.barang_lain, d.jumlah, d.harga, d.total, b.nama_barang 
    FROM detail_pembelian d
    LEFT JOIN barang b ON d.id_barang = b.id_barang
    WHERE d.no_faktur_pembelian = ?
");
$stmt2->bind_param("s", $no_faktur);
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    $detail[] = $row;
}

// FPDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(190,10,'UD. FALDI PROFIL',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(190,6,'Denpasar | Telp: 0812-345-432-123',0,1,'C');
        $this->Ln(5);
    }

    function detail_pembelian($pembelian, $detail)
    {
        // Header Informasi
        $this->SetFont('Arial','B',12);
        $this->Cell(0,7,'Faktur Pembelian: '.$pembelian['no_faktur_pembelian'],0,1);
        $this->SetFont('Arial','',10);
        $this->Cell(50,6,'Distributor',0,0);
        $this->Cell(3,6,':',0,0);
        $this->Cell(0,6,$pembelian['distributor'],0,1);

        if (!empty($pembelian['nama'])) {
            $this->Cell(50,6,'Nama Penjual',0,0);
            $this->Cell(3,6,':',0,0);
            $this->Cell(0,6,$pembelian['nama'],0,1);
        }

        $this->Cell(50,6,'Tanggal',0,0);
        $this->Cell(3,6,':',0,0);
        $this->Cell(0,6,date("d/m/Y", strtotime($pembelian['tanggal'])),0,1);

        $this->Ln(5);

        // Tabel Barang
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(10,8,'No',1,0,'C',true);
        $this->Cell(70,8,'Nama Barang',1,0,'C',true);
        $this->Cell(25,8,'Jumlah',1,0,'C',true);
        $this->Cell(35,8,'Harga Satuan',1,0,'C',true);
        $this->Cell(40,8,'Subtotal',1,1,'C',true);

        $this->SetFont('Arial','',10);
        $no = 1;
        $total = 0;

        foreach ($detail as $row) {
            $nama_barang = !empty($row['nama_barang']) ? $row['nama_barang'] : $row['barang_lain'];
            $this->Cell(10,8,$no++,1);
            $this->Cell(70,8,$nama_barang,1);
            $this->Cell(25,8,$row['jumlah'].' pcs',1,0,'C');
            $this->Cell(35,8,'Rp '.number_format($row['harga'],0,',','.'),1,0,'R');
            $this->Cell(40,8,'Rp '.number_format($row['total'],0,',','.'),1,1,'R');
            $total += $row['total'];
        }

        // Total
        $this->SetFont('Arial','B',10);
        $this->Cell(140,8,'Total Pembelian',1,0,'R');
        $this->Cell(40,8,'Rp '.number_format($total,0,',','.'),1,1,'R');
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().' dari {nb}',0,0,'C');
    }
}

// Cetak PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->detail_pembelian($pembelian, $detail);
$pdf->Output('', 'Faktur_Pembelian_'.$no_faktur.'.pdf');
