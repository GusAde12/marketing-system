<script type="text/javascript">
	document.title="Transaksi Baru";
	document.getElementById('transaksi').classList.add('active');
</script>
<script type="text/javascript">
		$(document).ready(function(){
			if ($.trim($('#contenth').text())=="") {
				$('#prosestran').attr("disabled","disabled");
				$('#prosestran').attr("title","tambahkan barang terlebih dahulu");
				$('#prosestran').css("background","#ccc");
				$('#prosestran').css("cursor","not-allowed");
			}
		})

</script>
<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Entry  Transaksi Baru</h3>
				<form class="form-input" method="post" action="handler.php?action=tambah_tempo" style="padding-top: 30px;">
					<label>Pilih Barang : </label>
					<select style="width: 372px;cursor: pointer;" required="required" name="id_barang">
						<?php
						$data=$root->con->query("select * from barang");
						while ($f=$data->fetch_assoc()) {
							echo "<option value='$f[id_barang]'>$f[nama_barang] (stock : $f[stok] | Harga : ".number_format($f['harga_jual']).")</option>";
						}
						?>
					</select>
					<label>Jumlah Beli :</label>
					<input required="required" type="number" name="jumlah">
					<input type="hidden" name="trx" value="<?php echo date("d")."/AF/".$_SESSION['id']."/".date("y") ?>">
					<button class="btnblue" type="submit"><i class="fa fa-save"></i> Simpan</button>
				</form>
				
			</div>
		</div>
		<br>
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Data transaksi</h3>
				<table class="datatable" style="width: 100%;">
				<thead>
				<tr>
					<th width="35px">NO</th>
					<th>ID Barang</th>
					<th>Nama Barang</th>
					<th>Jumlah Beli</th>
					<th>Total Harga</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody id="contenth">
				<?php
				$trx=date("d")."/AF/".$_SESSION['id']."/".date("y");
				$data=$root->con->query("select barang.nama_barang,tempo.id_subtransaksi,tempo.id_barang,tempo.jumlah_beli,tempo.total_harga from tempo inner join barang on barang.id_barang=tempo.id_barang where trx='$trx'");
				$getsum=$root->con->query("select sum(total_harga) as grand_total from tempo where trx='$trx'");
				$getsum1=$getsum->fetch_assoc();
				$no=1;
				while ($f=$data->fetch_assoc()) {
					?><tr>
						<td><?= $no++ ?></td>
						<td><?= $f['id_barang'] ?></td>
						<td><?= $f['nama_barang'] ?></td>
						<td><?= $f['jumlah_beli'] ?></td>
						<td>Rp. <?= number_format($f['total_harga']) ?></td>
						<td><a href="handler.php?action=hapus_tempo&id_tempo=<?= $f['id_subtransaksi'] ?>&id_barang=<?= $f['id_barang'] ?>&jumbel=<?= $f['jumlah_beli'] ?>" class="btn redtbl"><span class="btn-hapus-tooltip">Cancel</span><i class="fa fa-close"></i></a></td>
						</tr>
					<?php
				}
				?>
			</tbody>
				
				<tr>
					<?php if ($getsum1['grand_total']>0) { ?>
					<td colspan="3"></td><td>Grand Total :</td>
					<td> Rp. <?= number_format($getsum1['grand_total']) ?></td>
					<td></td>
					<?php }else{ ?>
					<td colspan="6">Data masih kosong</td>
					<?php } ?>
				</tr>
				
			</table>
			<br>
			<form class="form-input" action="handler.php?action=selesai_transaksi" method="post">
				<label>Nama Pembeli :</label>
				<input required="required" type="text" name="nama_pembeli">

				<label>Status Bayar:</label>
				<select name="status_bayar" id="status_bayar" required>
					<option value="">-- Pilih Status Bayar --</option>
					<option value="Lunas">Lunas</option>
					<option value="DP">DP</option>
				</select>

				<div id="dp_fields" style="display:none;">
					<label>Jumlah DP:</label>
					<input type="number" name="jumlah_dp" id="jumlah_dp" min="0" value="0">

					<label>Sisa Hutang:</label>
					<input type="number" name="sisa_hutang" id="sisa_hutang" readonly>
				</div>

				<input type="hidden" name="total_bayar" id="total_bayar" value="<?= $getsum1['grand_total'] ?>">

				<button class="btnblue" id="prosestran" type="submit"><i class="fa fa-save"></i> Proses Transaksi</button>
			</form>

			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


			<script type="text/javascript">
				$(document).ready(function () {
					const totalBayar = <?= $getsum1['grand_total'] ?>;

					$('#status_bayar').on('change', function () {
						let status = $(this).val();
						if (status === 'DP') {
							$('#dp_fields').show();
							$('#jumlah_dp').prop('disabled', false);
						} else {
							$('#dp_fields').hide();
							$('#jumlah_dp').prop('disabled', true).val(0);
							$('#sisa_hutang').val(0);
						}
					});

					$('#jumlah_dp').on('input', function () {
						let dp = parseInt($(this).val()) || 0;
						let sisa = totalBayar - dp;
						$('#sisa_hutang').val(sisa);
					});
				});
			</script>


			</div>
		</div>


	</div>
</div>




<?php
include "foot.php";
?>
