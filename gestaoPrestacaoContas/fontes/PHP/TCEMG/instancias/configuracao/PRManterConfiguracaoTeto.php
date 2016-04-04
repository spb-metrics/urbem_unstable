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
    * Página de Processamento Configuração de Orgão
    * Data de Criação   : 14/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore

    * $Id: PRManterConfiguracaoTeto.php 64799 2016-04-01 18:32:14Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTetoRemuneratorio.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTeto";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get("stAcao");

$stModulo = $request->get("hdnStModulo");

switch ($stAcao) {
    default:
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTTCEMGTetoRemuneratorio = new TTCEMGTetoRemuneratorio();

        $obTTCEMGTetoRemuneratorio->setDado("cod_entidade"  ,$request->get('hdnCodEntidade'));
        $obErro = $obTTCEMGTetoRemuneratorio->exclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $arListaTetos = Sessao::read('arListaTetos');

            foreach ($arListaTetos as $key => $teto) {
                $obTTCEMGTetoRemuneratorio = new TTCEMGTetoRemuneratorio();

                if (trim($teto['cod_evento']) != "") {
                    $obErro = $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo = '".$teto['cod_evento']."'", "", $boTransacao);
                    $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , $rsEvento->getCampo('cod_evento'));
                } else
                    $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , trim($teto['cod_evento']));

                if (!$obErro->ocorreu()) {
                    $obTTCEMGTetoRemuneratorio->setDado("exercicio"     ,$teto['exercicio']);
                    $obTTCEMGTetoRemuneratorio->setDado("cod_entidade"  ,$request->get('hdnCodEntidade'));
                    $obTTCEMGTetoRemuneratorio->setDado("vigencia"      ,$teto['vigencia']);
                    $obTTCEMGTetoRemuneratorio->setDado("teto"          ,$teto['teto']);
                    $obTTCEMGTetoRemuneratorio->setDado("justificativa" ,$teto['justificativa']);

                    $obErro = $obTTCEMGTetoRemuneratorio->inclusao($boTransacao);
                }

                if ($obErro->ocorreu())
                    break;
            }
        }

        if(!$obErro->ocorreu()){
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGTetoRemuneratorio);
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}
?>
