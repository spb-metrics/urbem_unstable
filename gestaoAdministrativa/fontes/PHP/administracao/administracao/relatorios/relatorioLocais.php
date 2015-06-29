<?php
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
?>
<?php

  /**
    * Manutneção de relatórios
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: relatorioLocais.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Casos de uso: uc-01.03.94

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

 ?>
<script type="text/javascript">
    function SalvarRelatorio()
    {
        document.frm.action = "locais.php?<?=Sessao::getId()?>";
        document.frm.submit();
    }
</script>

<form action="locais.php?<?=Sessao::getId()?>" method="POST" name="frm">

<?php

    # setAjuda("UC-01.03.94");

    print '
            <table width="100">
                 <tr>
                     <td class="labelcenter" title="Salvar Relatório">
                     <a href="javascript:SalvarRelatorio();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                 </tr>
             </table>
             ';

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

</form>
