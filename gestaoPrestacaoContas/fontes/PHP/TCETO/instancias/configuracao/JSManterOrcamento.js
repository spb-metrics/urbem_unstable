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
<?
/**
    * Pacote de configuração do TCETO - JavaScript Configurar Orçamento
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: JSManterOrcamento.js 60778 2014-11-14 17:55:19Z evandro $
*/
?>
<script language="JavaScript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}
		
</script>