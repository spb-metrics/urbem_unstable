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
  * Página de JavaScript da Configuração de Leis do PPA
  * Data de Criação: 14/01/2014
  
  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  
  * @ignore
  *
  * $Id: JSVincularPlanoTCE.js 62528 2015-05-18 17:53:04Z franver $
  
  * $Revision: 62528 $
  * $Name: $
  * $Author: franver $
  * $Date: 2015-05-18 14:53:04 -0300 (Seg, 18 Mai 2015) $
  
*/
?>
<script language="JavaScript">

function validaCampos() {
    var jq = window.parent.frames["telaPrincipal"].jQuery;
    var obErro = false;

    jq("tr [name^='sw_table_1_row_']").each(function() {
        if (jq("#"+jq(this).attr('name')+"_cell_5").attr('name') != undefined ) {
            if (jq("#"+jq(this).attr('name')+"_cell_5").html() == '*' ) {
                if (jq("#"+jq(this).attr('name')+"_cell_6 select").val() == '') {
                    obErro = true;
                    return false;
                } else {                    
                    return true;
                }
            }
        } 
    });

    if (!obErro) {
        return true;
    } else {
        alertaAviso('Campos com (*) são obrigatórios.', 'n_incluir', 'erro','<?=Sessao::getId();?>');
        return false;
    }
}

</script>