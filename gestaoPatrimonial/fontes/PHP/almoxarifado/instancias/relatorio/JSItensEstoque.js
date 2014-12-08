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
    * Página de JavaScript de Centro de Custo
    * Data de Criação   : 25/01/2006


    * @author Analista      : Diego
    * @author Desenvolvedor : Tonismar R. Bernardo

    * @ignore

    * Casos de uso: uc-03.03.20
*/

/*
$Log$
Revision 1.1  2007/03/30 19:20:08  hboaventura
Alteração do nome do arquivo

Revision 1.5  2006/07/06 14:03:02  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:53  diego


*/
?>

<script language="JavaScript">

function buscaDado( BuscaDado , codCGM){
    var stTarget              = document.frm.target;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.codCGM.value = codCGM;
    document.frm.target       = "oculto";
    document.frm.action       = '<?=$pgOcul;?>?<?=Sessao::getId();?>' ;
    document.frm.submit();
    /*document.frm.action       = '<?=CAM_FW_POPUPS;?>relatorio/OCRelatorio.php?<?=Sessao::getId();?>';*/
    document.frm.action       = '<?=$pgGera;?>?<?=Sessao::getId();?>';
}

function goOculto(stControle)
{
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=CAM_FW_POPUPS;?>relatorio/OCRelatorio.php?<?=Sessao::getId();?>';
}

function goOcultoFiltro(stControle)
{
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = 'telaPrincipal';
}


</script>

