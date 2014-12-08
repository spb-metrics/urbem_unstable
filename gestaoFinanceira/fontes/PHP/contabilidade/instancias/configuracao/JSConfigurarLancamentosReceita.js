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
    * Arquivo JavaScript
    * Data de Criação   : 21/10/2011


    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @ignore

    
    
    * Casos de uso: uc-02.04.01

*/

?>
<script language="JavaScript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function selecionaReceita( inputCheck, codContaDespesa, descricaoDespesa ) {
    jQuery("input[type=checkbox]").each(function(i) {
        jQuery(this).removeAttr("checked");
    });

    inputCheck.checked = true;

    executaFuncaoAjax("montaLancamentoReceita", "&cod_conta_receita=" + codContaDespesa + "&descricao=" + descricaoDespesa );
}

function carregaContasLancamento( tipoLancamento, valorRadio ) {
    executaFuncaoAjax( tipoLancamento, "&cod_receita=" + jQuery('#codReceitaLista').val() + "&valor_radio=" + valorRadio );
}
</script>