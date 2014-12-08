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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * Casos de uso : uc-03.05.31

*/

/*
$Log$
Revision 1.1  2007/09/19 14:56:49  bruce
Ticket#10105#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3, 37, 1 );
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt( '2.5.0' );

$preview->setNomeArquivo('contrato');

$stEntidade = '';
if ( is_array( $_POST['inNumCGM'] ) ) {
    $stEntidade = implode ( ' , ' ,  $_POST['inNumCGM'] ) ;
}

$preview->addParametro ( 'inNumContrato'  , $_POST['inNumContrato'  ] );
$preview->addParametro ( 'stObjeto'       , $_POST['stObjeto'       ] );
$preview->addParametro ( 'stDtInicial'    , $_POST['stDtInicial'    ] );
$preview->addParametro ( 'stDtFinal'      , $_POST['stDtFinal'      ] );
$preview->addParametro ( 'inCodFornecedor', $_POST['inCodFornecedor'] );
$preview->addParametro ( 'stEntidades'    , $stEntidade               );
$preview->addParametro ( 'stAnulados'     , $_POST['snAnulados']      );
$preview->addParametro ( 'tipoContrato'     , $_POST['tipoContrato']      );

if ($_POST['dtVlPagos']) {
    $preview->addParametro ( 'dtPagosAte', $_POST['dtVlPagos'] );
} else {
    $preview->addParametro ( 'dtPagosAte', date );
}

if ( count( $_POST['inCodOrgaoSelecionados'] ) > 0 ) {
    $stOrgaosSel = implode ( ' , ' , $_POST['inCodOrgaoSelecionados'] );
}

if (!( ($_REQUEST['stDtInicialAssinatura']=="") && ($_REQUEST['stDtFinalAssinatura']=="") ) ) {
    $dtIni = $_REQUEST['stDtInicialAssinatura'];
    $dtFim = $_REQUEST['stDtFinalAssinatura'];
    $periodo = "AND to_char(contrato.dt_assinatura,'DD/MM/YYYY') BETWEEN '".$dtIni."' AND '".$dtFim."'";
    $preview->addParametro ( 'periodoAssinatura', $periodo);
}

if (!( ($_REQUEST['stDtInicialInicioExec']=="") && ($_REQUEST['stDtFinalInicioExec']=="") ) ) {
    $dtIni = $_REQUEST['stDtInicialInicioExec'];
    $dtFim = $_REQUEST['stDtFinalInicioExec'];
    $periodo = "AND to_char(contrato.inicio_execucao,'DD/MM/YYYY') BETWEEN '".$dtIni."' AND '".$dtFim."'";
    $preview->addParametro ( 'periodoInicioExec', $periodo);
}

if (!( ($_REQUEST['stDtInicialFimExec']=="") && ($_REQUEST['stDtFinalFimExec']=="") ) ) {
    $dtIni = $_REQUEST['stDtInicialFimExec'];
    $dtFim = $_REQUEST['stDtFinalFimExec'];
    $periodo = "AND to_char(contrato.fim_execucao,'DD/MM/YYYY') BETWEEN '".$dtIni."' AND '".$dtFim."'";
    $preview->addParametro ( 'periodoFimExec', $periodo);
}

$preview->addParametro ( 'inCodOrgaoSelecionados', $stOrgaosSel );

$preview->preview();
