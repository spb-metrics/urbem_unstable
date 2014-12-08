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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/
?>

<script language="javascript">
        
    function montaLista( id,classificacao,cod_plano,nom_conta ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>','montaLista');
    }
        
    function excluirListaItens( id,classificacao,cod_plano,nom_conta ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id+'&classificacao='+classificacao+'&cod_plano='+cod_plano+'&nom_conta='+nom_conta,'excluirListaItens');
    }
    
    function montaAlteracaoItens( id,classificacao,cod_plano,nom_conta ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id+'&classificacao='+classificacao+'&cod_plano='+cod_plano+'&nom_conta='+nom_conta,'montaAlteracaoItens');
    }
    
    function limpaCombos(){
        document.getElementById('inClassificacao').selectedIndex = 0;
        document.getElementById('spnContas').innerHTML = '';
        document.getElementById('inCodConta').value = '';
        document.getElementById('stConta').innerHTML = '&nbsp';
    }
</script>
