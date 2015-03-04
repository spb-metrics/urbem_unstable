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
    * Página de Relatório RGF Anexo4
    * Data de Criação   : 08/10/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.23

    $Id: OCGeraRGFAnexo4.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

if ($_REQUEST['stTipoRelatorio'] == 'Semestre'  && $_REQUEST['stAcao'] == 'anexo4novo') {
    $preview = new PreviewBirt(6,36,45);
} elseif ($_REQUEST['stTipoRelatorio'] == 'Quadrimestre'  && $_REQUEST['stAcao'] == 'anexo4novo') {
    $preview = new PreviewBirt(6,36,46);
} elseif ($_REQUEST['stTipoRelatorio'] == 'Mes'  && $_REQUEST['stAcao'] == 'anexo4novo') {
    $preview = new PreviewBirt(6,36,59);
} else {
    $preview = new PreviewBirt(6,36,5);
}

$preview->setTitulo('Demonstrativo das Operações de Crédito');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel (true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) && ( count($_REQUEST['inCodEntidade']) == 1 ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}

if ( Sessao::getExercicio() > '2012' ) {
    if ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
        $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
    } else if ($_REQUEST['stTipoRelatorio'] == 'Mes') {
        switch ($_REQUEST['cmbMensal']) {
            case '01':
                $descricaoPeriodo = "Janeiro de ".Sessao::getExercicio();
            break;
            case '02':
                $descricaoPeriodo = "Fevereiro de ".Sessao::getExercicio();
            break;
            case '03':
                $descricaoPeriodo = "Março de ".Sessao::getExercicio();
            break;
            case '04':
                $descricaoPeriodo = "Abril de ".Sessao::getExercicio();
            break;
            case '05':
                $descricaoPeriodo = "Maio de ".Sessao::getExercicio();
            break;
            case '06':
                $descricaoPeriodo = "Junho de ".Sessao::getExercicio();
            break;
            case '07':
                $descricaoPeriodo = "Julho de ".Sessao::getExercicio();
            break;
            case '08':
                $descricaoPeriodo = "Agosto de ".Sessao::getExercicio();
            break;
            case '09':
                $descricaoPeriodo = "Setembro de ".Sessao::getExercicio();
            break;
            case '10':
                $descricaoPeriodo = "Outubro de ".Sessao::getExercicio();
            break;
            case '11':
                $descricaoPeriodo = "Novembro de ".Sessao::getExercicio();
            break;
            case '12':
                $descricaoPeriodo = "Dezembro de ".Sessao::getExercicio();
            break;
        }
        
        $preview->addParametro( 'dt_inicial' , "01/".$_REQUEST['cmbMensal']."/".Sessao::getExercicio());
        $preview->addParametro( 'dt_final'   , SistemaLegado::retornaUltimoDiaMes($_REQUEST['cmbMensal'], Sessao::getExercicio()));
        $preview->addParametro( 'periodo', $descricaoPeriodo );
   } else {
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
    }
} else {
     if ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
        switch ($_REQUEST['cmbSemestre']) {
            case 1:
                $preview->addParametro( 'periodo', 4 );  // conforme IF existente na PL desse Anexo
            break;
            case 2:
                $preview->addParametro( 'periodo', 5 ); // conforme IF existente na PL desse Anexo
            break;
        }
    } else {
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
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

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo3novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
