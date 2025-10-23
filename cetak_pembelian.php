<?php
require('assets/lib/fpdf.php');

class PDF extends FPDF
{
	function Header()
	{
	    $this->SetFont('Arial','B',18);
	    $this->Cell(190,10,'UD. FALDI PROFIL',0,1,'C');

	    $this->SetFont('Arial','I',10);
	    $this->Cell(190,6,'Denpasar | Telp/Fax : 0812-345-432-123',0,1,'C');

	    $this->SetFont('Arial','',10);
	    $this->Cell(190,6,'Laporan Data Pembelian',0,1,'C');
	    
	    $this->Ln(5);
	    $this->SetFont('Arial','',10);
	    $this->Cell(190,6,'Tanggal Cetak: '.date("d-m-Y"),0,1,'R');
	    $this->Ln(5);

	    // Garis horizontal
	    $this->Line(10, $this->GetY(), 200, $this->GetY());
	    $this->Ln(5);
	}

	function data_pembelian()
	{
		$con = new mysqli("localhost", "root", "", "imk");
		if ($con->connect_error) {
			die("Koneksi gagal: " . $con->connect_error);
		}

		$sql = "
			SELECT 
				p.no_faktur_pembelian,
				p.distributor,
				p.nama,
				p.tanggal,
				(
					SELECT COUNT(*) 
					FROM detail_pembelian d 
					WHERE d.no_faktur_pembelian = p.no_faktur_pembelian
				) AS jumlah,
				(
					SELECT SUM(d.total) 
					FROM detail_pembelian d 
					WHERE d.no_faktur_pembelian = p.no_faktur_pembelian
				) AS total
			FROM pembelian p
			GROUP BY p.no_faktur_pembelian
			ORDER BY p.tanggal DESC
		";

		$result = $con->query($sql);

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		return $data;
	}

	function set_table($header, $data)
	{
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(230, 230, 230);
		$this->Cell(10,8,"No",1,0,'C',true);
		foreach($header as $col)
			$this->Cell(30,8,$col,1,0,'C',true);
		$this->Ln();

		$this->SetFont('Arial','',9);
		$no = 1;
		foreach ($data as $row)
		{
			$this->Cell(10,8,$no++,1);
			$this->Cell(30,8,$row['no_faktur_pembelian'],1);
			$this->Cell(30,8,$row['distributor'],1);
			$this->Cell(30,8,!empty($row['nama']) ? $row['nama'] : '-',1);
			$this->Cell(30,8,(int)$row['jumlah'],1,0,'C');
			$this->Cell(30,8,"Rp ".number_format($row['total'], 0, ',', '.'),1,0,'R');
			$this->Cell(30,8,date("d-m-Y", strtotime($row['tanggal'])),1,0,'C');
			$this->Ln();
		}
	}

	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(0,10,'Halaman '.$this->PageNo().' dari {nb}',0,0,'C');
	}
}

// Buat dan cetak PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetTitle('Laporan Pembelian');
$pdf->AddPage();

$header = array('No Faktur', 'Distributor', 'Nama Penjual', 'Jumlah Barang', 'Total', 'Tanggal');
$data = $pdf->data_pembelian();

$pdf->set_table($header, $data);
$pdf->Output('', 'I'); // 'I' tampil di browser, 'D' untuk download otomatis
?>
