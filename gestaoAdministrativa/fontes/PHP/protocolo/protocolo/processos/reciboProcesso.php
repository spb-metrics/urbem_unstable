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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 14/03/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: reciboProcesso.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );

$preview = new PreviewBirt(1,5,4);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeArquivo('reciboProcesso');

$numMatricula = pegaDado("num_matricula","sw_processo_matricula","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");
$numInscricao = pegaDado("num_inscricao","sw_processo_inscricao","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");

$preview->addParametro ( 'pNumMatricula' , $numMatricula );
$preview->addParametro ( 'pNumInscricao' , $numInscricao );

$preview->addParametro ( 'pExercicioSessao' , Sessao::getExercicio() );

$preview->addParametro ( 'pCodProcesso'  , $_REQUEST['codProcesso' ] );
$preview->addParametro ( 'pAnoExercicio' , $_REQUEST['anoExercicio'] );

$cod_municipio = pegaConfiguracao("cod_municipio");
$codUf = pegaConfiguracao("cod_uf");
$preview->addParametro ( 'pCodMunicipio' , $cod_municipio );
$preview->addParametro ( 'pCodUf' , $codUf );

$stDataHoje = dataExtenso(date("Y-m-d"));
$preview->addParametro ('pDataHoje', $stDataHoje);

$preview->preview();
