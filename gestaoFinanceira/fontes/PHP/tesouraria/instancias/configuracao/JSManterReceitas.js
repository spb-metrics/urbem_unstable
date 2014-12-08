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
    * Data de Criação   : 08/09/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-13 15:55:06 -0300 (Seg, 13 Ago 2007) $
    
    * Casos de uso: uc-02.04.03

*/

/*
$Log$
Revision 1.7  2007/08/13 18:49:18  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.6  2007/05/29 14:11:35  domluc
Mudanças na forma de classificação de receitas.

Revision 1.5  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script language="JavaScript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limpaContas() {
    document.frm.stContaInicial.value='';
    document.frm.stContaFinal.value='';
    document.getElementById('stDescContaInicial').innerHTML='&nbsp;';
    document.getElementById('stDescContaFinal').innerHTML='&nbsp;';
}

function detalhaConta( stExercicio, stCodEntidade, inCodigo , stCodEstrutural, stDescricao, stTipoReceita) {
//    window.open('../../popups/receitas/popup.php?<?=Sessao::getId()?>&inCodPlano='+inCodPlano+'&stExercicio='+stExercicio,'algo','width=350px,height=230px,resizable=1,scrollbars=0,left=350,top=200');
    parent.frames['telaPrincipal'].location='FMDetalhamentoReceitas.php?<?=Sessao::getId();?>&stExercicio='+stExercicio+'&stCodEntidade='+stCodEntidade+'&inCodigo='+inCodigo+'&stTipoReceita='+stTipoReceita+'&stDescricao='+stDescricao+'&stCodEstrutural='+stCodEstrutural	;
}


</script>
                
