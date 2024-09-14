<?php

require_once("../../../conexao.php"); 

$nome = $_POST['nome-categoria'];
$status = $_POST['status-categoria'];

$nome_novo = strtolower( preg_replace("[^a-zA-Z0-9-]", "-", 
        strtr(utf8_decode(trim($nome)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"),
        "aaaaeeiooouuncAAAAEEIOOOUUNC-")) );
$nome_url = preg_replace('/[ -]+/' , '-' , $nome_novo);

$antigo = $_POST['antigo'];
$id = $_POST['txtid2'];

if($nome == ""){
	echo 'Preencha o Campo Nome!';
	exit();
}


if($nome != $antigo){
	$res = $pdo->query("SELECT * FROM categorias where nome = '$nome'"); 
	$dados = $res->fetchAll(PDO::FETCH_ASSOC);
	if(@count($dados) > 0){
			echo 'Categoria já Cadastrada no Banco!';
			exit();
		}
}


//SCRIPT PARA SUBIR FOTO NO BANCO
$caminho = '../../../img/categorias/' .@$_FILES['imagem']['name'];
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
  $imagem = @$_FILES['imagem']['name']; 
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 

$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}


if($id == ""){
	$res = $pdo->prepare("INSERT INTO categorias (nome, imagem, status) VALUES (:nome, :imagem,  :status)");
	$res->bindValue(":imagem", $imagem);
}else{

	if($imagem == "sem-foto.jpg"){
		$res = $pdo->prepare("UPDATE categorias SET nome = :nome, status = :status WHERE id = :id");
	}else{
		$res = $pdo->prepare("UPDATE categorias SET nome = :nome, imagem = :imagem, status = :status WHERE id = :id");
		$res->bindValue(":imagem", $imagem);
	}

	$res->bindValue(":id", $id);

}

$res->bindValue(":nome", $nome);
$res->bindValue(":status", $status);
$res->execute();


echo 'Salvo com Sucesso!!';

?>