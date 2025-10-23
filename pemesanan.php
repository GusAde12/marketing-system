<?php include "head.php" ?>
<?php
	if (isset($_GET['action']) && $_GET['action']=="tambah_pemesanan") {
		include "tambah_pemesanan.php";
	}
	else if (isset($_GET['action']) && $_GET['action']=="edit_pemesanan") {
		include "edit_pemesanan.php";
	}
	else{
?>
<script type="text/javascript">
	document.title="Pemesanan";
	document.getElementById('pemesanan').classList.add('active');
</script>
<script type="text/javascript" src="assets/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
    $(function(){
    	$.tablesorter.addWidget({
    		id:"indexFirstColumn",
    		format:function(table){
    			$(table).find("tr td:first-child").each(function(index){
    				$(this).text(index+1);
    			})
    		}
    	});
    	$("table").tablesorter({
    		widgets:['indexFirstColumn'],
    		headers:{
        		0:{sorter:false},
        		3:{sorter:false},
        		4:{sorter:false},
        		5:{sorter:false},
        		6:{sorter:false},
        		7:{sorter:false},
        	}
    	});
    });
</script>
<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
			<div class="contenttop">
				<div class="left">
				<a href="?action=tambah_pemesanan" class="btnblue"><i class="fa fa-plus"></i> Tambah Data Pemesanan</a>
				<a href="cetak_datapemesanan.php" class="btnblue" target="_blank"><i class="fa fa-print"></i> Cetak</a>
				</div>
				<div class="right">
					<script type="text/javascript">
						function gotocat(val){
							var value=val.options[val.selectedIndex].value;
							window.location.href="pemesanan.php?id_cat="+value+"";
						}
					</script>
					
					<form class="leftin">
						<input type="search" name="q" placeholder="Cari Data Stok..." value="<?php echo $keyword=isset($_GET['q'])?$_GET['q']:""; ?>">
						<button><i class="fa fa-search"></i></button>
					</form>
				</div>
				<div class="both"></div>
			</div>
			<span class="label">Data Pemesanan </span>
			<table class="datatable" id="datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Jumlah</th>
            <th>Harga Per Unit</th>
            <th>Satuan</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "null";
		$root->tampil_pemesanan("null"); // Pastikan ada parameter

        ?>
    </tbody>
</table>
			</div>
		</div>
	</div>
</div>


<?php 
}
include "foot.php" ?>
