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
<script>
//     function teste(){
//         var campos = new String();
//         for(cmp=0;cmp<document.frm.elements.length;cmp++){
//           campos+="&"+document.frm.elements[cmp].name+"="+document.frm.elements[cmp].value;
//         }
//         ajaxJavaScript('<?=$pgOcul?>?<?=$sessao->id?>'+campos,'BuscaPreEmpenho');
//     }

    function buscaDado( BuscaDado ){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = BuscaDado;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        //document.frm.action = '<?=$pgProc;?>?<?=$sessao->id;?>';
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }
    
    function limparFiltro(){
        document.frm.reset();
        passaItem('document.frm.inCodEntidade','document.frm.inCodEntidadeDisponivel','tudo');
    }
</script>