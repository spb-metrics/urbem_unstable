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
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"    );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , $_REQUEST['stExercicio'] );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$stAno = Sessao::getExercicio();

$obErro = new Erro();

if (Sessao::getExercicio() < '2013') {
    $preview = new PreviewBirt(6,36,6);
    $preview->setTitulo('Dem Disponibilidades de Caixa');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel( true );
} else {
    $preview = new PreviewBirt(6,36,52);
    $preview->setTitulo('Dem Disponibilidades de Caixa');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel( true );
}

$preview->addParametro('cod_entidade', implode(',', $_REQUEST['inCodEntidade']));

$boConfirmaFundo = strpos(strtolower($rsEntidade->getCampo('nom_cgm')), 'fundo');

if (count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', utf8_encode($rsEntidade->getCampo('nom_cgm')) );
    if (preg_match( "/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) || $boConfirmaFundo > 0) {
        $preview->addParametro( 'poder' , 'Executivo' );
    } else {
        $preview->addParametro( 'poder' , 'Legislativo' );
    }
} else {
    while (!$rsEntidade->eof()) {
        if (preg_match( "/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', utf8_encode($rsEntidade->getCampo('nom_cgm')) );
            $preview->addParametro( 'poder' , 'Executivo' );
            break;
        }
        $rsEntidade->proximo();
    }
}

switch ($_REQUEST['stTipoRelatorio']) {
    case 'UltimoQuadrimestre':
        $preview->addParametro('periodo', $_REQUEST['cmbQuadrimestre']);
        $numPeriodo = 'Quadrimestre';
    break;
    case 'UltimoSemestre':
        $preview->addParametro('periodo', $_REQUEST['cmbSemestre']);
        $numPeriodo = 'Semestre';
    break;
}

$preview->addParametro('tipo_periodo', $numPeriodo);

$inPeriodo = $request->get('cmbQuadrimestre') != '' ? $request->get('cmbQuadrimestre') : $request->get('cmbSemestre');

$stDataInicial = '01/01/'.$stAno;
$stDataFinal = '31/12/'.$stAno;

$preview->addParametro( 'data_inicio', $stDataInicial );
$preview->addParametro( 'data_fim'   , $stDataFinal );
$preview->addParametro( 'exercicio'  , $_REQUEST['stExercicio'] );

// verificando se foi selecionado Câmara e outra entidade junto
$rsEntidade->setPrimeiroElemento();
if ( !$obErro->ocorreu() && ( count($_REQUEST['inCodEntidade']) != 1 ) ) {
    while ( !$rsEntidade->eof() ) {
        if (preg_match( "/c[âa]mara/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $obErro->setDescricao( "Entidade ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
            $boPreview = false;
            break;
        }
        $rsEntidade->proximo();
    }
}

#############################Modificações do tce para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;

$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);
$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', utf8_encode($stDataEmissao) );
$preview->addParametro( 'tipoAnexo', 'anexo5novo');
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
