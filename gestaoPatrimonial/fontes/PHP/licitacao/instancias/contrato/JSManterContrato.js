<script type="text/javascript">
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
</script>
<?php
/**
    * Arquivo JavaScript utilizado no Form de manutenção de contratos
    * Data de Criação   : 11/10/2006


    * @author Analista      : Anelise Schwengber
    * @author Desenvolvedor : Leandro André Zis

    * @ignore
    
    * Casos de uso: uc-03.05.22
  
*/
/*
$Log$
Revision 1.4  2006/11/22 21:31:28  leandro.zis
atualizado

Revision 1.3  2006/11/01 19:57:24  leandro.zis
atualizado


*/
?>
<script language="JavaScript">

   function excluirDocumentos(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirDocumentos');
   }

   function excluirAditivos(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirAditivos');
   }

   function alteraDocumentos(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'alteraDocumentos');
   }

    function formataDiasValidosDocumento()
    {
        montaParametrosGET('sincronizaDiasValidos');
    }

    function formataDataValidaDocumento()
    {
       montaParametrosGET('sincronizaDataValida');
    }

    function limpaFormularioDocumentosExtra()
    {
        jq('#stDataValidade').attr('disabled','disabled');
        jq('#inNumDiasValido').attr('disabled','disabled');
    }

    function validaData(obj){
       var dtAtual = new Number(<?=date('Ymd')?>);
       dtVal    = obj.value.split("/");
       dtValida = parseInt(dtVal[2]+dtVal[1]+dtVal[0]);
         if(dtValida < dtAtual){
             alertaAviso("@A Data de Validade deve ser maior ou igual a data atual.",'form','erro','<?=Sessao::getId()?>');
             obj.value = "";
             obj.focus();
              document.getElementById('inNumDiasValido').value = "";
             return false;
         }
         return true;
    }
    
    function bloqueiaDesbloqueiaCampos(obj)
    {
        if(obj.value =='') {          
            jq('#stDataValidade').val('');
            jq('#inNumDiasValido').val('');
            jq('#stDataValidade').attr('disabled','disabled');
            jq('#inNumDiasValido').attr('disabled','disabled');
        } else {
            jq('#stDataValidade').removeAttr('disabled');
            jq('#inNumDiasValido').removeAttr('disabled');
            jq('#inNumDiasValido').focus();
        }
    }

   function consultarListaArquivo(stArquivo){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&arquivo='+stArquivo,'consultarListaArquivo');
   }        
    
   function excluirListaArquivo(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirListaArquivo');
   }
</script>
