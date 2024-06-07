<?php 
	include_once "../init.php";

	// User login check
	if ($getFromU->loggedIn() === false) {
        header('Location: ../index.php');
	}
	
	include_once 'skeleton.php'; 

	// Function to format number to Rupiah
	function formatRupiah($number) {
		return "Rp" . number_format($number, 2, ',', '.');
	}
?>

<div class="wrapper">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4 style="font-family:'Source Sans Pro'; font-size: 1.3em; text-align: center">Expenses incurred between <?php echo $_SESSION['yrfrom'] ?> and <?php echo $_SESSION['yrto'] ?> </h4>    
				</div>
				<div class="card-content">
					<table>
						<thead>
							<tr>
								<th>#</th>
								<th>Item</th>
								<th>Cost</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$yrexp = $getFromE->yrwise($_SESSION['UserId'],$_SESSION['yrfrom'],$_SESSION['yrto']);
								if($yrexp !== NULL)
								{
									$len = count($yrexp);
									for ($x = 1; $x <= $len; $x++) {
										echo "<tr>
											<td>".$x."</td>
											<td>".$yrexp[$x-1]->Item."</td>
											<td>".formatRupiah($yrexp[$x-1]->Cost)."</td>
											<td>".date("d-m-Y",strtotime($yrexp[$x-1]->Date))."</td>
										</tr>";	
									}
								}
							?>					
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
