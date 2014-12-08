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
* Página de JavaScript para relatório de Servidor
* Data de Criação   : 15/07/2005


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.12
*/

/*
$Log$
Revision 1.4  2006/08/08 17:48:19  vandre
Adicionada tag log.

*/
?>
<script language="JavaScript">
function atualizaFormularioFiltro(){
    document.frm.stCtrl.value = 'atualizaFormularioFiltro';    
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = 'OCRelatorioServidorFiltro.php?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaValor(controle){
    document.frm.stCtrl.value = controle;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();    
    document.frm.action = stAction;

}

function mudaCheck(pai,campos){
    arrCampos = new Array();
    arrCampos = campos.split(",")
    objPai = eval("document.frm."+pai);
    for (i=0; i<arrCampos.length; i++) {
        obj = eval("document.frm."+arrCampos[i]);           
        if( objPai.checked ){
            obj.checked = true;
        }else{
            obj.checked = false;
        }
    }
}

function limpaForm() {
    document.frm.reset();
    buscaValor("montaOpcoes");
}

function selecionarTodos(){
    if (jq('#boTodos').prop('checked')) {
        jq(':checkbox').each(function() {
                                            this.checked = true;           
                                        });
    } else {
        jq(':checkbox').each(function() {
                                            this.checked = false;                   
                                        });
    }
}

</script>