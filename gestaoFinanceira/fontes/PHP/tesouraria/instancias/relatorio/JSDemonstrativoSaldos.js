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
    * Arquivo JavaScript utilizado no relatorio de Empenhos a Pagar
    * Data de Criação   : 14/04/2015


    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Rafael Almeida

    * @ignore
    
    $Revision: $
    $Name$
    $Author: $
    $Date: $
    $Id: $
*/
?>
<script>


function verificaAgruparContaCorrente(boAgrupar){
    
    if ( boAgrupar ) {
        jQuery('#stOrdenacao').val('conta_corrente');
        jQuery('#stOrdenacao').attr('disabled',true);        
        jQuery("#boAgruparContaCorrente").attr('checked',true);
    }else{
        jQuery('#stOrdenacao').attr('disabled',false);      
        jQuery('#stOrdenacao').val('estrutural');
        jQuery("#boAgruparContaCorrente").attr('checked',false);  
    }
}

</script>