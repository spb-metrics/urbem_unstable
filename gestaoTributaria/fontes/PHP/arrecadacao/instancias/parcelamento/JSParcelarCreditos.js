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
  * Página de JavaScript para Parcelamento de Crditos
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSParcelarCreditos.js 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.2  2006/09/15 11:16:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script language="JavaScript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

}

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;
    if (campoT == true){
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
    else{
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
}

function Cancelar () {
<?php
 //   $link = Sessao::read( "link" );
 //   $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&boVinculoEdificacao=".$_REQUEST['boVinculoEdificacao']."";
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgFilt.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}
</script>
