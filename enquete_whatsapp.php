<?php
// Script de funções operacional
	 
date_default_timezone_set('America/Fortaleza');
 
function f_bancoopen($whost,$wuser,$wpassword,$wdata)
{
	return mysqli_connect($whost,$wuser,$wpassword,$wdata);
}

function f_bancocharset($wcon,$wcharset)
{
	return mysqli_set_charset($wcon,$wcharset);
}

function f_bancoquery($wcon,$wsql)
{
   	$myfile = fopen('sqlcomand.txt', 'w') or die('Unable to open file!');
	fwrite($myfile, $wsql);
	fclose($myfile);	
	return mysqli_query($wcon,$wsql); 
}

function f_bancoqueryrows($wcon)
{
	return mysqli_affected_rows($wcon);
}
$wbancoopen = f_bancoopen("localhost","sisle873_root","durango2014","sisle873_WhatsAppEnquete");
f_bancocharset($wbancoopen, "utf8");
$wsql = explode("©",$_GET['sql']);
$queryrows = 0 ;
$size = Count($wsql);

for($x = 0; $x < $size - 1 ; $x++) 
{		   
	$wfield = explode("®",$wsql[$x]); 
	// Pesquisa Ativa	

	$wrs = f_bancoquery($wbancoopen,'select ' .
									'pes.pes_codigo, ' .
									'(select 1 from txt txt force INDEX (status) where txt.txt_status = "P" and txt.txt_receivecontato= "' . $wfield[0] . '") as txt_pendencia ' .
									'from pes pes ' . 
									'where pes.pes_ativa = "S"');	 
	$wresult_pesquisa = array();								
	while($row = mysqli_fetch_assoc($wrs))  		
		{			
			$wresult_pesquisa[] = $row;		
		}	
	
	$pendencia = $wresult_pesquisa[0]["txt_pendencia"];
	
	$wrs = f_bancoquery($wbancoopen,'select 
									hed.hed_descricao,
									hed.hed_enter,		
									pes.pes_descricao,
									res.res_descricao,
									foo.foo_descricao
									from pes pes, hed hed, res res, foo foo
									where pes.pes_ativa = "S" and
									hed.pes_codigo =  pes.pes_codigo and
									res.pes_codigo = pes.pes_codigo and
									foo.pes_codigo = pes.pes_codigo 
									order by res.res_ordem ');
	
	$wresult_tela = array();
	$tela = "";
	while($row = mysqli_fetch_assoc($wrs))  		
		{			
			$wresult_tela[] = $row;		
			$tela = $tela . $row["res_descricao"];
		}

	
		
	$tela = $wresult_tela[0]['hed_descricao'] .
			$wresult_tela[0]['hed_enter'] .
			$wresult_tela[0]['hed_enter'] .
			$wresult_tela[0]['pes_descricao'] .
			$wresult_tela[0]['hed_enter'] .
			$wresult_tela[0]['hed_enter'] .
			'Envie :' .
			$wresult_tela[0]['hed_enter'] .
			$tela .
			$wresult_tela[0]['foo_descricao'] ;
	
	
	
	
	
	// Contato Votou	
	$wrs = f_bancoquery($wbancoopen,'select '.
									'mov.res_codigo ' .
									'from mov mov ' .
									'where mov.pes_codigo = "' .$wresult_pesquisa[0]['pes_codigo'] . '" and ' .									
									'mov.cto_codigo = ' . '"' . $wfield[0] . '"' );
	$wresult_votante = array();								
	while($row = mysqli_fetch_assoc($wrs))  		
		{			
			$wresult_votante[] = $row;		
		}

	$wrs = f_bancoquery($wbancoopen,'select '.
									'res.res_codigo ' .
									'from res res ' .
									'where res.res_codigo = "' . 
									preg_replace('/[^\w]/', '', $wfield[1]) . '"' );
	
	$wresult_opcao = array();	
	while($row = mysqli_fetch_assoc($wrs))  		
	{			
		$wresult_opcao[] = $row;		
	}									     
	
	if ( (Count($wresult_votante)<=0) && (Count($wresult_opcao)<=0) && $pendencia != 1 )		
		{ 	
			$wrs = f_bancoquery($wbancoopen,'insert into txt '.
											'(txt_receivecontato,txt_receivehashtag, txt_receivedatahora, txt_typesendmessage, txt_sendmessage, txt_status)'.
											' values '.
											'(' . '"' . $wfield[0] . '"' . ',' .
											'"' . preg_replace('/[^\w]/', '', $wfield[1]) . '"' . ',' .
											'"' . date('Y-m-d') . '"' . ',' .
											'"T"' . ',' .
											'"' . $tela . '"' . ',' .
											'"P"  )' );
									
		}											
		
	if ( (Count($wresult_votante)<=0) && (Count($wresult_opcao)>0) && $pendencia != 1 )		
		{ 	            
			$wrs = f_bancoquery($wbancoopen,'insert into mov ' .
											'(pes_codigo,cto_codigo,res_codigo)	' .
											'values ' .	
											'(' .
											$wresult_pesquisa[0]['pes_codigo'] . ',' .
											'"' . $wfield[0] . '"' . ',' .
											'"' . ucwords( preg_replace('/[^\w]/', '', $wfield[1]) ) . '"' . ")" );
											
			$wrs = f_bancoquery($wbancoopen,'select
											res.res_codigo as res_codigo,
											count(mov.res_codigo) as res_qtde,
											Round( count(mov.res_codigo) / (select COUNT(*) from mov mov where mov.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] . ' ) * 100 )   as res_percentual
											from mov mov,res res
											where mov.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] . ' and
											res.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] .' and res.res_codigo = mov.res_codigo
											group by mov.res_codigo
											order by res.res_ordem '); 
			$tela = "";
			while($row = mysqli_fetch_assoc($wrs))  		
				{			
					$tela = $tela . $row['res_codigo'] . ' (' . $row['res_qtde'] . ') ' . ' (' . $row['res_percentual'] . '%) ' . $wresult_tela[0]['hed_enter'];			
				}							
			$tela = $wresult_tela[0]['hed_descricao'] .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .					
					$wresult_tela[0]['pes_descricao'] .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .
					'Resultado :' .
					$wresult_tela[0]['hed_enter'] .
					$tela .
					$wresult_tela[0]['hed_enter'] .
					'Você votou em : *' . ucwords( preg_replace('/[^\w]/', '', $wfield[1]) ) . '*' . 
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .
					'```Envie Oi para receber o resultado atualizado...```';
					

			
			$wrs = f_bancoquery($wbancoopen,'insert into txt '.
											'(txt_receivecontato,txt_receivehashtag, txt_receivedatahora, txt_typesendmessage, txt_sendmessage, txt_status)'.
											' values '.
											'(' . '"' . $wfield[0] . '"' . ',' .
											'"' . preg_replace('/[^\w]/', '', $wfield[1]) . '"' . ',' .
											'"' . date('Y-m-d') . '"' . ',' .
											'"T"' . ',' .
											'"' . $tela . '"' . ',' .
											'"P"  )' ); 
											 
		}	
		
	if( (Count($wresult_votante) > 0 ) && $pendencia != 1 ) 
		{
			
			$wrs = f_bancoquery($wbancoopen,'select
											res.res_codigo as res_codigo,
											count(mov.res_codigo) as res_qtde,
											Round( count(mov.res_codigo) / (select COUNT(*) from mov mov where mov.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] . ' ) * 100 )   as res_percentual
											from mov mov,res res
											where mov.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] . ' and
											res.pes_codigo = '. $wresult_pesquisa[0]["pes_codigo"] .' and res.res_codigo = mov.res_codigo
											group by mov.res_codigo
											order by res.res_ordem '); 
			$tela = "";
			while($row = mysqli_fetch_assoc($wrs))  		
				{			
					$tela = $tela . $row['res_codigo'] . ' (' . $row['res_qtde'] . ') ' . ' (' . $row['res_percentual'] . '%) ' . $wresult_tela[0]['hed_enter'];		
				}	
			 	
			$tela = $wresult_tela[0]['hed_descricao'] .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['pes_descricao'] .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .
					'Resultado :' .
					$wresult_tela[0]['hed_enter'] .
					$tela . 
					$wresult_tela[0]['hed_enter'] .
					'Você já votou em : *' . $wresult_votante[0]['res_codigo'] . '*' .
					$wresult_tela[0]['hed_enter'] .
					$wresult_tela[0]['hed_enter'] .
					'```Envie Oi para receber o resultado atualizado...```';
			
			$wrs = f_bancoquery($wbancoopen,'insert into txt '.
											'(txt_receivecontato,txt_receivehashtag, txt_receivedatahora, txt_typesendmessage, txt_sendmessage, txt_status)'.
											' values '.
											'(' . '"' . $wfield[0] . '"' . ',' .
											'"' . preg_replace('/[^\w]/', '', $wfield[1]) . '"' . ',' .
											'"' . date('Y-m-d') . '"' . ',' .
											'"T"' . ',' .
											'"' . $tela . '"' . ',' .
											'"P"  )' ); 
		}
	
}


mysqli_close($wbancoopen); 
