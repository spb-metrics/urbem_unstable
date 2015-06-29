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
    * Página de JavaScript do Relatório de Protocolo de Entrega
    * Data de Criação : 04/04/2007 


    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30566 $
    $Name$
    $Autor: $
    $Date: 2007-04-05 10:56:09 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-04.04.47 
*/
/*
*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    stAction = document.frm.action; 
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOculF;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = stAction;
}

</script>
