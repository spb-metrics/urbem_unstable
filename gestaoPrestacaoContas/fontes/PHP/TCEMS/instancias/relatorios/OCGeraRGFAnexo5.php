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
    * Página de Relatório RGF Anexo5
    * Data de Criação   : 08/03/2008

    * @author Bruce

    * @ignore

     * Casos de uso : uc-06.01.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , $_REQUEST['stExercicio'] );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$obErro = new Erro();

$preview = new PreviewBirt(6,57,2);
$preview->setTitulo('Dem Disponibilidades de Caixa');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$preview->addParametro( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {

    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));

} else {

    $rsEntidade->setPrimeiroElemento();
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));

    while ( !$rsEntidade->eof() ) {
        if (eregi("prefeitura.*", $rsEntidade->getCampo( 'nom_cgm' ))) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm'));
            break;
        }
        $rsEntidade->proximo();
    }
}

$stDataInicial = "01/01/".$_REQUEST['stExercicio'];

if ($_REQUEST['stTipoRelatorio'] == 'UltimoQuadrimestre') {
    switch ($_REQUEST['cmbQuadrimestre']) {
        case 3: $stDataFinal = '31/12'; break;
    }
    $nuPeriodo = $_REQUEST['cmbQuadrimestre'] ;
    $preview->addParametro( 'tipo_periodo'  , 'Quadrimestre' );
} elseif ($_REQUEST['stTipoRelatorio'] == 'UltimoSemestre') {
    switch ($_REQUEST['cmbSemestre']) {
        case 2: $stDataFinal = '31/12'; break;
    }
    $nuPeriodo = $_REQUEST['cmbSemestre'] ;
    $preview->addParametro( 'tipo_periodo'  , 'Semestre' );
}
$stDataFinal = "$stDataFinal/".$_REQUEST['stExercicio'];


$preview->addParametro( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
$preview->addParametro( 'data_inicio'  , $stDataInicial             );
$preview->addParametro( 'data_fim'     , $stDataFinal               );
$preview->addParametro( 'exercicio'    , $_REQUEST['stExercicio']   );
$preview->addParametro( 'periodo'      , $nuPeriodo                 );
$preview->addParametro( 'poder'        , 'Legislativo'              );

$preview->addAssinaturas(Sessao::read('assinaturas'));
if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
