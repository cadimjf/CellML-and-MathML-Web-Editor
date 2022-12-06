<?

include('config.php');
    

/* Armazena informa�es sobre o arquivo de entrada na tabela */
function insereInfArquivo($codprojeto,$nome_entrada,$caminho){
      $busca = mysql_query("SELECT CODIGOARQUIVO FROM Arquivos WHERE NOME = '$nome_entrada' and  CODIGOPROJETO = $codprojeto");
      $nregistros = mysql_num_rows($busca); 
      if ($nregistros == 0){
         $hoje=getdate();  
         $dia = sprintf("%02d",$hoje[mday]);
         $mes = sprintf("%02d",$hoje[mon]);
         $ano = sprintf("%02d",$hoje[year]);
         $vet_extensao = explode('.', $nome_entrada);
         $tamanho = sizeof($vet_extensao);
         $extensao = $vet_extensao[$tamanho-1];
         $result = mysql_query("insert into Arquivos (CODIGOPROJETO,NOME,CAMINHO,DIA,MES,ANO,TIPOARQUIVO) values ($codprojeto,'$nome_entrada','$caminho',$dia,$mes,$ano,'$extensao')");

         return $result;
      }
      else{
         return 0;
      }
}

/* Regsitra a informa�o de uso na tabela de LOG */
function insereLog($codpesquisador,$codprojeto,$ip){
   $hoje=getdate();
   $minuto = sprintf("%02d",$hoje[minutes]);
   $hora = sprintf("%02d",$hoje[hours]);
   $dia = sprintf("%02d",$hoje[mday]);
   $mes = sprintf("%02d",$hoje[mon]);
   $ano = sprintf("%02d",$hoje[year]);
   /* Identifica o IP para montar o relat�io de estat�ticas de acesso */
   $comando = "insert into Log (CODIGOPESQUISADOR,CODIGOPROJETO,ACAO,HORA,DIA,MES,ANO,IP) values ($codpesquisador,$codprojeto,'Submeteu arquivo XML','$hora:$minuto',$dia,$mes,$ano,'$ip')";
   $result = mysql_query($comando);
   if ($result==0) {
      echo("<script>alert('ERROR saving Log');</script>");
   }
}
    
    
    //creating a new project
	$nomeProjeto;
    //verifica se o nome escolhido pelo usuario esta em branco
    if (is_null($_POST['nomeprojusuario']))
    	$nomeProjeto = $_POST['nomeproj']; //recebe o nome padrão
    else 
       	$nomeProjeto = $_POST['nomeprojusuario'];//recebe o nome digitado
    
    
	//verifica se já existe projeto com este nome
	$str = "SELECT codigoprojeto FROM Projetos WHERE (NOME='".$nomeProjeto."') AND (CODIGOPESQUISADOR='".$_POST['coduser']."')";
    $busca = mysql_query($str);
//     var_dump($busca);
//     echo ("<hr>");
    $result = mysql_fetch_array($busca);
    if($result[0]){//testa se há projetos
      //imprime um erro pq ja existe o projeto
      echo "<font color='#AA0000' size='4'>The name $nomeProjeto is already in use. Please choose another name.</font><br>";
      echo ("<input type='button' onclick='fecha()' value='Close this page'>");
      	//este script redireciona a pagina para o repositorio com os projetos do usuario
         echo ("<script language='JavaScript'>\n
              		function fecha(){\n
                		window.close();\n
    				}
        		</script>\n
         ");
    }else{//nao existe projeto com este nome, continua o processo
       
       
       //compila o arquivo
             
      //obtem o nome de arquivos utilizados como parametro pelo agos
      $nome_saida = $_POST['nomeproj']."_SolveODE.hpp";
      $diff = $_POST['nomeproj']."_Ldiff";
      $parameters = $_POST['nomeproj']."_Lparam";
      $bound = $_POST['nomeproj']."_Lbound";
      $nome_entrada = $_POST['nomeproj'].".xml";
      
      system("chmod -R 777 ../models/$nome_entrada");//altera a permissao
      // 	echo("chmod -R 777 ../models/$nome_entrada<br>");
      
      /* Executa o comando do sistema:
      ./compilador [arquivo-Fonte] [arquivo-Destino(sem extensao)] [arquivo-Diff] [arquivo-Parameters] [arquivo-Bound] 
      onde o arquivo-fonte e o xml, o arquivo destino e o nome da classe, os proximos tem os nomes das listas de variaveis */
      $comando= "./compilador ../models/$nome_entrada Solveode $diff $parameters $bound -cvode";
      echo ("<b>AGOS output message:</b><br>");
      $resultado2 = system($comando, $retval);
      echo ("<br>");
       
       
       
       
      $str = "INSERT INTO Projetos VALUES(NULL, '".$_POST['coduser']."',
      '1','". $nomeProjeto ."', DAYOFMONTH(NOW()), MONTH(NOW()), YEAR(NOW()),'editor', 'n')";
      
      mysql_query($str);
      $lastid;
      $busca = mysql_query("SELECT LAST_INSERT_ID()");
      while($array = mysql_fetch_array($busca)) $lastid = $array['LAST_INSERT_ID()'];//gets the last id
      $str = "SELECT nome2 FROM Pesquisadores
         WHERE CODIGOPESQUISADOR=". $_POST['coduser'];
//       echo $str;
      $busca = mysql_query($str);
      
      $sobrenome;
      while($array = mysql_fetch_array($busca)) $sobrenome = $array["nome2"];
      $IDNOME = $_POST['coduser'].$sobrenome;
      //criam os diretorios
      system("mkdir /home/project/$IDNOME");
      system("mkdir /home/project/$IDNOME/AGOS");
      system("mkdir /home/project/$IDNOME/AGOS/".$nomeProjeto);
      system("chmod -R 777 /home/project/$IDNOME/AGOS/".$nomeProjeto);//altera a permissao
      
      $caminho = "/home/project/$IDNOME/AGOS/".$nomeProjeto;
      
      
      // echo $caminho;
      
      
      
      
      /* Renomeia o arquivo de saida para o nome gerado dinamicamente */
      $comando = "mv Solveode.hpp $nome_saida";
      $resultado2 = system($comando , $retval);
      /* Copia os arquivos gerados para a pasta do usuario */
      
      
      system("cp $diff $caminho");
      system("cp $parameters $caminho");
      
      system("cp $bound $caminho");
      $diffu = $diff."u";  // unidades
      system("cp $diffu $caminho");
      $diffv = $diff."v";  // variaveis
      system("cp $diffv $caminho");
      $parametersu = $parameters."u";  // unidades
      system("cp $parametersu $caminho");
      $parametersv = $parameters."v";  // variaveis
      system("cp $parametersv $caminho");
      system("cp calcVars $caminho");
      system("cp ../models/$nome_entrada $caminho");
      system("cp $nome_saida $caminho");
      system("cp ../../novoagos/MCutil.hpp $caminho"); //fazendo uma coia de MCutil para o diretorio do projeto, para que o include no hpp funcione
      insereInfArquivo($lastid,$nome_entrada,"$IDNOME/AGOS/".$nomeProjeto);
                                          
      insereInfArquivo($lastid,$nome_saida,"$IDNOME/AGOS/".$nomeProjeto);
      insereLog($_POST['coduser'],$lastid,$dominio);
      echo ("<input type='button' onclick='redireciona()' value='Redirect to your repository'>");
      	//este script redireciona a pagina para o repositorio com os projetos do usuario
         echo ("<script language='JavaScript'>\n
              		function redireciona(){\n
                		window.location = 'http://www.fisiocomp.ufjf.br:8080/pr01/openProjects.do'\n
    				}
        		</script>\n
         ");
	}//fim - if que testa se há projetos com este nome
?>
