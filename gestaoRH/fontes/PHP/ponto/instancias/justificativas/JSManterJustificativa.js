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
    * Página de Javascript
    * Data de Criação: 29/09/2008

    
    * @author Analista: Dagiane	Vieira	
    * @author Desenvolvedor: <Alex Cardoso>
    
    * @ignore
    
    $Id: $
    
    * Casos de uso: uc-04.10.03
*/
?>
<script language="JavaScript">

function verificaQuantidadeHoras(quantidadeHoras) {
    if ( quantidadeHoras.value.length == 1 ){
        quantidadeHoras.value = quantidadeHoras.value + '00:00';
    }else if ( quantidadeHoras.value.length == 2 ) {
              quantidadeHoras.value = quantidadeHoras.value + '0:00';
    }else if (quantidadeHoras.value.length ==  4){
              quantidadeHoras.value = quantidadeHoras.value + '00';
    }else if (quantidadeHoras.value.length ==  5){
              quantidadeHoras.value = quantidadeHoras.value + '0';
    }
}

</script>