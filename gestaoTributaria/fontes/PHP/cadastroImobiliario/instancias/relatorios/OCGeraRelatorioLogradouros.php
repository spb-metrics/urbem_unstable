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
 * Página de processamento oculto para o relatório de logradouros
 * Data de Criação   : 28/03/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: OCGeraRelatorioLogradouros.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_FW_PDF."ListaPDF.class.php";

$obRRelatorio = new RRelatorio;
$obPDF = new ListaPDF();

$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read( "NomFiltro" );

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Cadastro Imobiliário:"   );
$obPDF->setTitulo            ( "Relatório de Logradouros:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arTransf5Sessao = Sessao::read('sessao_transf5');
$obPDF->addRecordSet( $arTransf5Sessao );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "CÓD."               ,7  , 9);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "TIPO"               ,9  , 9);
$obPDF->addCabecalho   ( "NOME DO LOGRADOURO" ,40 , 9);
$obPDF->addCabecalho   ( "BAIRROS"            ,17 , 9);
$obPDF->addCabecalho   ( "CEPs"               ,7 , 9);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "UF"                 ,7  , 9);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "MUNICÍPIO"          ,15 , 9);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "cod_logradouro"  , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "nom_tipo"        , 7 );
$obPDF->addCampo       ( "nom_logradouro"  , 7 );
$obPDF->addCampo       ( "bairros"         , 7 );
$obPDF->addCampo       ( "cep"             , 7 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "sigla_uf"        , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "nom_municipio"   , 7 );

$obPDF->addFiltro( 'Tipo de Logradouro'        , $arNomFiltro['tipo_logradouro'][$arFiltro[ 'inCodTipoLogradouro' ]] );
$obPDF->addFiltro( 'Nome do Logradouro'        , $arFiltro['stNomLogradouro']    );
$obPDF->addFiltro( 'Código Logradouro Inicial' , $arFiltro['inCodInicio']        );
$obPDF->addFiltro( 'Código Logradouro Final'   , $arFiltro['inCodTermino']       );
$obPDF->addFiltro( 'Nome do Bairro'            , $arFiltro['stNomBairro']        );
$obPDF->addFiltro( 'Código Bairro Inicial'     , $arFiltro['inCodInicioBairro']  );
$obPDF->addFiltro( 'Código Bairro Final'       , $arFiltro['inCodTerminoBairro'] );
$obPDF->addFiltro( 'CEP Inicial'               , $arFiltro['inCEPInicio']        );
$obPDF->addFiltro( 'CEP Final'                 , $arFiltro['inCEPTermino']       );
$obPDF->addFiltro( 'Estado'                    , $arNomFiltro['uf'][$arFiltro[ 'inCodigoUF' ]]              );
$obPDF->addFiltro( 'Município'                 , $arNomFiltro['municipio'][$arFiltro[ 'inCodigoMunicipio']] );
$obPDF->addFiltro( 'Ordenação'                 , $arNomFiltro['ordenacao'][$arFiltro[ 'stOrder']]           );

$rsDados = Sessao::read('sessao_transf5');
$rsTotais = new RecordSet;
$arTeste  = array();
$inTotalRegs = $rsDados->getNumLinhas();

if ( $inTotalRegs < 0)
    $inTotalRegs = 0;

$arTeste[0]["total"] = $inTotalRegs;
$rsTotais->preenche( $arTeste );

$obPDF->addRecordSet         ($rsTotais);

$obPDF->setQuebraPaginaLista ( false         );
$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCabecalho         ( "Total de registros", 100, 10  );

$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCampo             ( "total",  8  );

$obPDF->show();
?>
