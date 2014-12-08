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
* Página de Processamento Pessoal - Rescindir Contrato
* Data de Criação   : 20/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: tiago $
$Date: 2007-04-23 10:55:21 -0300 (Seg, 23 Abr 2007) $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php" );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php" );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obErro = new Erro;
$obRPessoalRescisaoContrato  = new RPessoalRescisaoContrato;
switch ($stAcao) {
    case "incluir":
        $obTransacao = new Transacao;
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->addPessoalCasoCausa();
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->setCodCasoCausa( $_POST['inCasoCausa'] );
        $obRPessoalRescisaoContrato->setDtRescisao( $_POST['dtRescisao'] );
        $obRPessoalRescisaoContrato->setNroCertidaoObito($_POST['stNroCertidaoObito']);
        $obRPessoalRescisaoContrato->setDescCausaMortis($_POST['stDescCausaMortis']);
        $obRPessoalRescisaoContrato->setAvisoPrevio($_POST['stAvisoPrevio']);
        $obRPessoalRescisaoContrato->setDataAvisoPrevio($_POST['dtAviso']);
        $obRPessoalRescisaoContrato->setIncorporarFolhaSalario(($_POST['boFolhaSalario'] == 1) ? true : false);
        $obRPessoalRescisaoContrato->setIncorporarFolhaDecimo(($_POST['boFolhaDecimo'] == 1) ? true : false);
        $obRPessoalRescisaoContrato->setRNorma($_POST['inCodNorma' ] );
        $obRPessoalRescisaoContrato->setExercicio(Sessao::getExercicio());

        // Verifica se veio da ação de alteracão de pensionista com opção de rescisão de contrato
        if (sessao::read('incluirRescisaoContratoPensionista') != null) {
            $obRPessoalRescisaoContrato->obRPessoalContrato->setCodContrato( $_POST['inCodContrato'] );
            $obErro = $obRPessoalRescisaoContrato->incluirRescisaoContratoPensionista();
            $pgFilt = "../pensionista/FLManterPensionista.php?".Sessao::getId();
        } else {
            $obRPessoalRescisaoContrato->obRPessoalContratoServidor->setCodContrato( $_POST['inCodContrato'] );
            $obErro = $obRPessoalRescisaoContrato->incluirRescisaoContrato();
        }

        if ($_POST['boGeraTermoRecisao'] == 'true' && !$obErro->ocorreu()) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " AND contrato.cod_contrato = ".$_POST['inCodContrato'];
            $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
            $arContratos = array();
            $arTmp = array(
                'inContrato' => $rsCGM->getCampo("registro"),
                'cod_contrato' => $rsCGM->getCampo("cod_contrato"),
                'numcgm' => $rsCGM->getCampo("numcgm"),
                'nom_cgm' => $rsCGM->getCampo("nom_cgm")
            );
            $arContratos[] = $arTmp;

            //Necessário calcular recisão para gerar Termo de recisão
            $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
            $obRFolhaPagamentoCalcularFolhas->setTipoFiltro('cgm_contrato');
            $obRFolhaPagamentoCalcularFolhas->setCodigos($arContratos);
            $obRFolhaPagamentoCalcularFolhas->setCalcularRescisao();
            $obRFolhaPagamentoCalcularFolhas->calcularFolha(true);
        }

        if ( !$obErro->ocorreu() ) {
            if ($_POST['boGeraTermoRecisao'] == 'true') {
                //busca competência atual
                $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
                $obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);

                $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
                $inMes     = (int) ($arData[1]);
                $stAno     = $arData[2];

                $stLink  = "?stCaminho=".CAM_GRH_FOL_INSTANCIAS."relatorio/PREmitirTermoRescisao.php";
                $stLink .= "&stTipoFiltro=contrato_rescisao&stOrdenacao=alfabetica";
                $stLink .= "&inCodMes=".$inMes;
                $stLink .= "&inAno=".$stAno;

                Sessao::write('arContratos', $arContratos);
            }

            SistemaLegado::LiberaFrames(true, false);
            SistemaLegado::alertaAviso($pgFilt,"Matrícula: ".$_POST['inRegistro'],"incluir","aviso", Sessao::getId(), "../");

            if ($_POST['boGeraTermoRecisao'] == 'true') {
                SistemaLegado::mudaFrameOculto(CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stLink);
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "excluir":
        $obRPessoalRescisaoContrato->obRPessoalContratoServidor->setCodContrato( $_GET['inCodContrato'] );
        $obErro = $obRPessoalRescisaoContrato->excluirRescisaoContrato();
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt,"Matrícula: ".$_GET['inRegistro'],"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgFilt,urlencode($_GET['inRegistro']),"n_excluir","erro", Sessao::getId(), "../");
    break;
}

?>
