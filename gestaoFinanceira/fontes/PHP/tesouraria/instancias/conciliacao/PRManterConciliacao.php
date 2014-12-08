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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 13/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: PRManterConciliacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//SistemaLegado::BloqueiaFrames();
$arPendenciasMarcadas = Sessao::read('arPendenciasMarcadas');

if (count($arPendenciasMarcadas) > 0) {
    foreach ($arPendenciasMarcadas as $stChave => $inValor) {
        $_REQUEST[$stChave] = 'on';
    }
}

if ($stAcao == "incluir") {

    $obRTesourariaConciliacao = new RTesourariaConciliacao();
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio'] );
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaConciliacao->setMes(intval($_REQUEST['inMes']));
    $obRTesourariaConciliacao->setDataExtrato( $_REQUEST['stDtExtrato'] );
    $obRTesourariaConciliacao->setValorExtrato( $_REQUEST['nuSaldoExtrato'] );
    $obRTesourariaConciliacao->setTimestampConciliacao( $_REQUEST['stTimestampConciliacao'] );

    $arMovimentacaoAux = Sessao::read('arMovimentacaoAux');
    if ( count( $arMovimentacaoAux ) > 0 ) {
        foreach ($arMovimentacaoAux as $key => $arMovimentacaoAux) {
            $arConciliar['boConciliar_'.($arMovimentacaoAux['indices']+1)] = ($arMovimentacaoAux['conciliar'] ) ? 'on' : '';
        }
        $arMovimentacao = Sessao::read('arMovimentacao');
        foreach ($arMovimentacao as $key => $arMovimentacao) {
            $arIndice = explode( ',', $arMovimentacao['indices'] );
            foreach ($arIndice as $inIndice) {
                $arConciliar["boConciliar_".($inIndice+1)] = $_REQUEST["boConciliar_".$arMovimentacao['id']."_".($key+1)];
            }
        }

    }

    $arMovimentacaoPendenciaAuxSessao = Sessao::read('arMovimentacaoPendenciaAux');
    if ( is_array( $arMovimentacaoPendenciaAuxSessao ) ) {
        foreach ($arMovimentacaoPendenciaAuxSessao as $key => $arMovimentacaoAux) {
            $arDataConciliacao = explode('/',$arMovimentacaoAux['dt_conciliacao']);
            $stMesConciliacao = $arDataConciliacao[1];
            $arConciliar['boPendencia_'.($arMovimentacaoAux['indices']+1)] = ($arMovimentacaoAux['conciliar'] AND ((integer) $stMesConciliacao == (integer) $_REQUEST['inMes'] OR  $arMovimentacaoAux['dt_conciliacao'] == '')) ? 'on' : '';
        }
        $arMovimentacaoPendenciaSessao = Sessao::read('arMovimentacaoPendencia');
        $arMovimentacaoPendenciaListagem = Sessao::read('arMovimentacaoPendenciaListagem');
        foreach ($arMovimentacaoPendenciaListagem as $stChave => $arListagem) {
            foreach ($arListagem as $arDados) {
                $arIndice = explode( ',', $arDados['indices'] );
                $arDataConciliacao = explode('/',$arDados['dt_conciliacao']);
                $stMesConciliacao = $arDataConciliacao[1];
                if ((integer) $stMesConciliacao == (integer) $_REQUEST['inMes'] OR  $arDados['dt_conciliacao'] == '') {
                    foreach ($arIndice as $inIndice) {
                        $arConciliar["boPendencia_".($inIndice+1)] = $_REQUEST["boPendencia_".$arDados['tipo']."-".$arDados['sequencia']."_".$arDados['linha']];
                    }
                }
            }
        }
        /*foreach ($arMovimentacaoPendenciaSessao as $key => $arMovimentacao) {
            $arIndice = explode( ',', $arMovimentacao['indices'] );
            foreach ($arIndice as $inIndice) {
                echo "<br>boPendencia_".$arMovimentacao['tipo']."-".$arMovimentacao['sequencia']."_".$inIndice;
                $arConciliar["boPendencia_".($inIndice+1)] = $_REQUEST["boPendencia_".$arMovimentacao['tipo']."-".$arMovimentacao['sequencia']."_".$inIndice];
            }
        }*/
    }
    $arMovimentacaoManualSessao = Sessao::read('arMovimentacaoManual');
    if ( is_array( $arMovimentacaoManualSessao ) ) {
         foreach ($arMovimentacaoManualSessao as $key => $arMovimentacaoManualAux) {
            $arConciliar['boManual_'.($arMovimentacaoManualAux['indices']+1)] = ($arMovimentacaoManualAux['conciliar'] ) ? 'on' : '';
        }
        foreach ($arMovimentacaoManualSessao as $key => $arMovimentacaoManual) {
            $arIndice = explode( ',', $arMovimentacaoManual['indices'] );
            foreach ($arIndice as $inIndice) {
                $arConciliar["boManual_".($inIndice+1)] = $_REQUEST["boManual_".$arMovimentacaoManual['id']."_".($key+1)];
            }
        }
    }

    $obRTesourariaConciliacao->setMovimentacao        ( Sessao::read('arMovimentacaoAux') );
    $obRTesourariaConciliacao->setMovimentacaoPendente( $arMovimentacaoPendenciaAuxSessao );
    $obRTesourariaConciliacao->setMovimentacaoManual  ( $arMovimentacaoManualSessao       );

    for ($x=1; $x<=3; $x++) {
        if ($_REQUEST["inNumCgm".$x]) {
            $obRTesourariaConciliacao->addAssinatura();
            $obRTesourariaConciliacao->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
            $obRTesourariaConciliacao->roUltimaAssinatura->obRCGM->setNumCGM($_REQUEST["inNumCgm".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
            $obRTesourariaConciliacao->roUltimaAssinatura->setTipo('CO');
            $obRTesourariaConciliacao->roUltimaAssinatura->setCargo($_REQUEST["stCargo".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setNumMatricula($_REQUEST["inMatricula".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setSituacao(true);
        }
    }

    $obErro = $obRTesourariaConciliacao->salvarMovimentacoes( $arConciliar );
    if (!$obErro->ocorreu()) {
        Sessao::write('filtroGeraRel', Sessao::read('filtro'));

    $arvoltaBusca = Sessao::read('voltaBusca');

//die;

    if($arvoltaBusca!="")
     SistemaLegado::alertaAviso($arvoltaBusca,'Conta '.$_REQUEST['inCodPlano'],'incluir','aviso',Sessao::getId(),'../');
  else
     SistemaLegado::alertaAviso($pgFilt,'Conta '.$_REQUEST['inCodPlano'],'incluir','aviso',Sessao::getId(),'../');

        $stCaminho = CAM_GF_TES_INSTANCIAS."conciliacao/OCRelatorioConciliacao.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodPlano=".$_REQUEST['inCodPlano']."&stExercicio=".$_REQUEST['stExercicio'];
        $stCampos .= "&inMes=".$_REQUEST['inMes']."&inCodEntidade=".$_REQUEST['inCodEntidade']."&nuSaldoTesouraria=".$_REQUEST['nuSaldoTesouraria'];
        $stCampos .= "&boAgrupar=".Sessao::read('boAgrupar');
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );

    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    SistemaLegado::LiberaFrames();
}

?>
