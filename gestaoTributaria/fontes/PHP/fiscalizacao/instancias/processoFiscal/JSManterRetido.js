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
    * Página de Levantamento fiscal por retenção
    * Data de Criacao: 18/08/2008


    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

    * @package URBEM
    * @subpackage JavaScript

    *Casos de uso: 

    $Id:$
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

function Cancelar(){
<?php
    $stLink = "&pg=".$sessao->link["pg"]."&pos=".$sessao->link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?='FLManterLevantamento.php?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function incluirNotaLista() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'incluirNotaLista';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
     document.frm.stCtrl.value = 'incluirNota';
}

function excluirNota( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirNota';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirServicoLista() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'incluirServicoLista';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function alterarServico( inIndice1 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'alterarServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirServico( inIndice1 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function preencheProxComboServico( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxComboServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosServico(){
    document.frm.stCtrl.value = 'preencheCombosServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

</script>
