<?
/**
  * Página de Formulario de Configuração de Consideracoes dos Arquivos
  * Data de Criação: 25/02/2014

  * @author Analista:      Sergio Santos
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: JSManterConsideracao.js 57486 2014-03-12 17:17:06Z evandro $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/
?>

<script language="JavaScript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaSpan(){
    jQuery("#spnCodigos").html("");
    jQuery("#inMes").val("");
}

function validaCampos(){
  if ( (jQuery("#inCodEntidade").val() != "") && (jQuery("#inMes").val() != "") ) {
    return true;
  }else{
    return false;
  }
}


</script>