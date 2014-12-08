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
    * Data de Criação   : 25/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO."TTGOConfiguracaoEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stDataAtual = date('d-m-Y');
Sessao::setTrataExcecao ( true );
$obMapeamento = new TTGOConfiguracaoEntidade();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stModulo = $_POST["stModulo"] ? $_POST["stModulo"] : $_GET["stModulo"];
$stModulo = str_replace("?","",$stModulo);

switch ($_REQUEST['stAcao']) {
    default:
        foreach ($_REQUEST as $stKey=>$stValue) {
            if ($stAcao == 'undgestora') {
                if (strstr($stKey,'inCodigo') && trim($stValue)!='') {
                    $arCodigo = explode('_',$stKey); //Formato: inCodigo_1
                    $obMapeamento->setDado('cod_entidade',$arCodigo[1]);
                    $obMapeamento->setDado('parametro','tc_codigo_unidade_gestora');
                    $obMapeamento->setDado('valor',$stValue.'_'.$stDataAtual);
                    $obMapeamento->recuperaPorChave($rsRecordSet);
                    if ($rsRecordSet->eof()) {
                        $obMapeamento->inclusao();
                    } else {
                        $obMapeamento->alteracao();
                    }
                }
                if (strstr($stKey,'inNumUnidade') && trim($stValue)!='') {
                    $arCodigo = explode('_',$stKey); //Formato: inCodigo_1
                    $obMapeamento->setDado('cod_entidade',$arCodigo[1]);
                    $obMapeamento->setDado('parametro','tc_ug_orgaounidade');
                    $obMapeamento->setDado('valor',$stValue.'_'.$stDataAtual);
                    $obMapeamento->recuperaPorChave($rsRecordSet);
                    if ($rsRecordSet->eof()) {
                        $obMapeamento->inclusao();
                    } else {
                        $obMapeamento->alteracao();
                    }
                }
            } elseif ($stAcao == 'balancete') {
                if (strstr($stKey,'inTipoBalancete') && trim($stValue)!='') {
                    $arCodigo = explode('_',$stKey); //Formato: inTipoBalancete_1
                    $obMapeamento->setDado('cod_entidade',$arCodigo[1]);
                    $obMapeamento->setDado('parametro','tc_codigo_tipo_balancete');
                    $obMapeamento->setDado('valor',$stValue);
                    $obMapeamento->setDado('cod_modulo',$stModulo);
                    $obMapeamento->recuperaPorChave($rsRecordSet);
                    if ($rsRecordSet->eof()) {
                        $obMapeamento->inclusao();
                    } else {
                        $obMapeamento->alteracao();
                    }
                }
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao"."&modulo=$stModulo","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        break;
}

Sessao::encerraExcecao();
?>
