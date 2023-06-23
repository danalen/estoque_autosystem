<?php 
  setlocale(LC_ALL, 'pt_BR.utf8');
  $mes = isset($_GET['mes'])?$_GET['mes']:'01';
  $produto = isset($_GET['produto'])?$_GET['produto']:'11017';
  $year = isset($_GET['ano'])?$_GET['ano']:'2021';
?>
	
<?php 
	$endereco	= 'endereco_ip';
	$banco		= 'nome_do_banco';
	$usuario	= 'usuario_do_banco';
	$senha		= 'senha_do_banco';
	$port		= 'porta_do_banco';
	
	try {
		$pdo = new PDO("pgsql:host=$endereco;dbname=$banco;port=$port;", $usuario, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		//echo "Conectado no banco de dados!!!";
	?>

	<form method="get" action="">
		
		<label>Grid do Produto</label>
		<!--<input type="text" name="produto">-->
		<input type="year" name="ano" value="<?=$year;?>">
		<select name="produto" id="produto">
			<?php
			$sql2 = "select * from produto where tipo='C'";
			foreach($pdo->query($sql2) as $row2){
				echo "<option value='".$row2['grid']."'>".$row2['nome']."</option>";
			}
			?>
		</select>
		<label>Mês</label>
		<select name="mes" id="mes">
			<option value="01">Janeiro</option>
			<option value="02">Fevereiro</option>
			<option value="03">Março</option>
			<option value="04">Abril</option>
			<option value="05">Maio</option>
			<option value="06">Junho</option>
			<option value="07">Julho</option>
			<option value="08">Agosto</option>
			<option value="09">Setembro</option>
			<option value="10">Outubro</option>
			<option value="11">Novembro</option>
			<option value="12">Dezembro</option>
		</select>
		<input type="submit" value="enviar">
	</form>
	<table id="datatable-buttons" class="table table-striped table-bordered">
		<tr>
			<th style="width: 350px; text-align:left;">Produto</th>
			<th style="width: 350px; text-align:left;">Data/Hora</th>
			<th>Estoque</th>
		</tr>
	<?php 
	$sql3 = "select D.grid as griddep, P.nome AS nomeprod, * from deposito D LEFT JOIN produto P ON D.produto=P.grid where D.produto='$produto'";
	foreach($pdo->query($sql3) as $row3){
		//echo $row3['grid'].$row3['nome'];
		$griddep = $row3['griddep'];
		$nomeprod = $row3['nomeprod'];
		
	}
	
	$ultimo = date('d', mktime(0, 0, 0, $mes+1, 0, $year ));
	if(isset($_GET['produto'])){
		for ($i = 1; $i <= $ultimo; $i++) {
			$ano = $year.'-'.$mes.'-'.$i;
		
			$sql = "select produto_estoque_f(1::int8, $griddep::int8, $produto::int8, '$ano', (select max(turno) from lancto where data='$ano' and turno!='99' and operacao!='M'), NULL)";
			foreach($pdo->query($sql) as $row){
				if($row['produto_estoque_f']<0){
					$style = 'style="background: #ef0000; color: #fff;"';
				} else {
					$style = "";
				}
				print "<tr $style><td>$nomeprod</td><td>$ano</td><td>".number_format($row['produto_estoque_f'], 3, ',', '.').'</td><tr>';
			}
		}
	} else {
		echo "<tr><td style='text-align: center;' colspan='3'>Faça uma seleção</td></tr>";
	}
		if(isset($_GET['produto'])){
		?>
		</table>
		<script>
		var mes = '<?=$mes;?>';
		document.getElementById('mes').value = mes;
		var produto = <?=$produto?>;
		document.getElementById('produto').value = produto;
		document.getElementById("mes").focus();
		</script>
		
		<?php
		}
	} catch (PDOexception $e) {
		echo "Falha ao conectar ao banco de dados.</br>";
		//die($e->getMessage());
		echo $e->getMessage();
	}				
 ?>
