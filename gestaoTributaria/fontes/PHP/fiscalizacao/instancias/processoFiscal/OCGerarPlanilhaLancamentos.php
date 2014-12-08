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
    * Página oculta do formulário Gerar Planilha de Lancamentos
    * Data de Criação   : 12/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISGerarPlanilhaLancamentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISGerarPlanilhaLancamentos.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISGerarPlanilhaLancamentos;
$obVisao = new VFISGerarPlanilhaLancamentos( $obController );

if ($_POST['boInicio']) {
    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
    $preview = new PreviewBirt( 5, 34, 1 );
    $preview->setTitulo( 'Relatório do Birt' );
    $preview->setVersaoBirt( '2.5.0' );
    //$preview->setExportaExcel( true );
    $preview->preview();
} else {
    $stFuncao = $_REQUEST['stCtrl'];

    $retorno = $obVisao->$stFuncao( $_REQUEST );

    print( $retorno );
}
?>
