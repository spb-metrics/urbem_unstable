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
    * Página de Processamento
    * Data de Criação: 23/09/2014

    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM

    $Id: PRTCEPBConfiguracaoTransferenciasConcedidasRecebidas.php 59957 2014-09-23 19:17:01Z michel $

    * Casos de uso: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBPlanoAnaliticaRelacionamento.class.php");
include_once(TTPB."TTPBPlanoAnaliticaTipoTransferencia.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "TCEPBConfiguracaoTransferenciasConcedidasRecebidas";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TTPBPlanoAnaliticaRelacionamento();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTTPBPlanoAnaliticaTipoTransferencia = new TTPBPlanoAnaliticaTipoTransferencia();

switch ($_REQUEST['stAcao']) {
  default:
    $obMapeamento->setDado('exercicio',Sessao::getExercicio());
    $obTTPBPlanoAnaliticaTipoTransferencia->setDado('exercicio',Sessao::getExercicio());
    
    foreach ($_REQUEST as $stKey=>$stValue) {        
        if (strstr($stKey,'inTransferencia')){
            $arCodigo = explode('_',$stKey);
            $obTTPBPlanoAnaliticaTipoTransferencia->setDado('cod_conta',$arCodigo[1]);
            $obTTPBPlanoAnaliticaTipoTransferencia->setDado('cod_tipo',$stValue);
            $obTTPBPlanoAnaliticaTipoTransferencia->recuperaPorChave($rsRecordSet);
            
            if ($stValue != '') {
                if ($rsRecordSet->eof()) {
                    $obTTPBPlanoAnaliticaTipoTransferencia->inclusao();
                } else {
                    $obTTPBPlanoAnaliticaTipoTransferencia->alteracao();
                }
            } else {
              $obTTPBPlanoAnaliticaTipoTransferencia->exclusao();
            }
        }
    }

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
  break;
}

Sessao::encerraExcecao();
?>
