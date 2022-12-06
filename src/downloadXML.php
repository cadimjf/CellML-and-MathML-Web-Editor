<?
	$ponteiro = fopen("teste.xml","wb");
	$conteudo = $_GET['value'];
	var_Dump($_GET);
	fwrite($ponteiro,$conteudo);
	fclose($conteudo);
// 	system("gnuplot /home/project/$caminho/grafico", $retval);
// 	/*                        fim do plot                      */
// 	
// 	/* converte o arquivo PS em PDF */
// 	
// 	$comando = "ps2pdf /home/project/$caminho/$nome_grafico.ps";
// 	system($comando, $retval);
// 	if($retval==1){
// 		$stop = true;
// 	}
// 			
// 	$comando = "mv $nome_grafico.pdf /home/project/$caminho/$basecfg"."_"."$nome_grafico.pdf";
// 	system($comando, $retval);
	
?>