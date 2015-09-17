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
    * Página de Processamento de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterEmpenho.php 63332 2015-08-18 19:54:17Z franver $

    $Revision: 32828 $
    $Name$
    $Autor:$
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.01.08
                    uc-02.03.03
                    uc-02.03.02
                    uc-02.03.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipanteDocumentos.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenho";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
include( $pgJS );

$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Trecho de código do filtro
$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $_POST['stDtEmpenho']);
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, "", $boTransacao);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês do Empenho encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "incluir":

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        if (!$_REQUEST["inModalidadeLicitacao"] || $_REQUEST["inModalidadeLicitacao"] == '') {
            SistemaLegado::exibeAviso("Modalidade TCMGO não informada!","n_incluir","erro");
            SistemaLegado::LiberaFrames(true,False);
            break;
        }

        if ($_REQUEST['inModalidadeLicitacao'] == '10' || $_REQUEST['inModalidadeLicitacao'] == '11') {
        
            if (!$_REQUEST["inFundamentacaoLegal"] || $_REQUEST["inFundamentacaoLegal"] == '') {
                SistemaLegado::exibeAviso("Fundamentação legal não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        
            if (!$_REQUEST["stJustificativa"] || $_REQUEST["stJustificativa"] == '') {
                SistemaLegado::exibeAviso("Justificativa não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        
            if (!$_REQUEST["stRazao"] || $_REQUEST["stRazao"] == '') {
                SistemaLegado::exibeAviso("Razão da escolha não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        }
    }

    $obAtributos = new MontaAtributos;
    $obAtributos->setName      ( "Atributo_" );
    $obAtributos->recuperaVetor( $arChave    );

    //Atributos Dinâmicos
    foreach ($arChave as $key=>$value) {
        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
        $inCodAtributo = $arChaves[0];
        if ( is_array($value) ) {
            $value = implode(",",$value);
        }
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
    }

    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_POST['inCodAutorizacao'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setCodReserva( $_POST['inCodReserva'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $_POST['inCodTipo'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $_POST['nuVlReserva'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $_POST['nuVlReserva'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_POST['stCodClassificacao'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDescricao( $_POST['stNomEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->setCodHistorico( $_POST['inCodHistorico'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->setNumCGM( $_POST['inCodFornecedor'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $_POST['inCodTipo'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $_POST['inCodPreEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodCategoria( $_REQUEST['inCodCategoria'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenho( $_POST['stDtEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtVencimento( $_POST['stDtVencimento'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodCategoria( $_REQUEST['inCodCategoria']);

    $obErro = $obREmpenhoEmpenhoAutorizacao->autorizarEmpenho($boTransacao);

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);
    if ( !$obErro->ocorreu() && $inCodUF == 9 && Sessao::getExercicio() >= 2012) {

        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOEmpenhoModalidade.class.php';
        $obTEmpenhoModalidade = new TTCMGOEmpenhoModalidade();

        if ($_REQUEST['inModalidadeLicitacao'] == '10' || $_REQUEST['inModalidadeLicitacao'] == '11') {

            $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $_REQUEST['inCodEntidade']);
            $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho());
            $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
            $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $_REQUEST['inModalidadeLicitacao']);
            $obTEmpenhoModalidade->setDado( 'cod_fundamentacao' , $_REQUEST['inFundamentacaoLegal']);
            $obTEmpenhoModalidade->setDado( 'justificativa'     , $_REQUEST['stJustificativa']);
            $obTEmpenhoModalidade->setDado( 'razao_escolha'     , $_REQUEST['stRazao']);
            $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);

        } else {

            $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $_REQUEST['inCodEntidade']);
            $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho());
            $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
            $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $_REQUEST['inModalidadeLicitacao']);
            $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);
        }

        //Informações sobre a licitação
        if ($_REQUEST['stProcessoLicitacao'] || $_REQUEST['stExercicioLicitacao'] || $_REQUEST['stProcessoAdministrativo']) {
            include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOProcessos.class.php';
            $obTTCMGOProcessos = new TTCMGOProcessos();
            $obTTCMGOProcessos->setDado( 'cod_empenho', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCMGOProcessos->setDado( 'cod_entidade', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTCMGOProcessos->setDado( 'exercicio', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getExercicio() );
            $obTTCMGOProcessos->setDado( 'numero_processo', $_REQUEST['stProcessoLicitacao'] );
            $obTTCMGOProcessos->setDado( 'exercicio_processo', $_REQUEST['stExercicioLicitacao'] );
            $obTTCMGOProcessos->setDado( 'processo_administrativo', $_REQUEST['stProcessoAdministrativo'] );
            $obErro = $obTTCMGOProcessos->inclusao($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        // Adiantamentos: Faz inclusao em empenho.contrapartida_empenho
        if ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) {
            include_once( TEMP."TEmpenhoContrapartidaEmpenho.class.php" );
            $obTEmpenhoContrapartidaEmpenho = new TEmpenhoContrapartidaEmpenho();
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'conta_contrapartida' , $_POST['inCodContrapartida']        );
            $obErro = $obTEmpenhoContrapartidaEmpenho->inclusao($boTransacao);
        }
    }

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 20) {
        if ( !$obErro->ocorreu() ) {
            // Relaciona o empenho com o fundeb
            include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNFundebEmpenho.class.php" );
            $obTTCERNFundebEmpenho = new TTCERNFundebEmpenho();
            $obTTCERNFundebEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCERNFundebEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTTCERNFundebEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTTCERNFundebEmpenho->setDado( 'cod_fundeb'          , $_REQUEST['inCodFundeb'] );
            $obErro = $obTTCERNFundebEmpenho->inclusao($boTransacao);

            // Relaciona o empenho com o royalties
            include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNRoyaltiesEmpenho.class.php" );
            $obTTCERNRoyaltiesEmpenho = new TTCERNRoyaltiesEmpenho();
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTTCERNRoyaltiesEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_royalties'       , $_REQUEST['inCodRoyalties'] );
            $obErro = $obTTCERNRoyaltiesEmpenho->inclusao($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        /* Salvar assinaturas configuráveis se houverem */
        $arAssinaturas = Sessao::read('assinaturas');
    if (array_key_exists('selecionadas', $arAssinaturas)) {
        $inCountArrayAssinaturas = count($arAssinaturas['selecionadas']);
    } else {
        $inCountArrayAssinaturas = 0;
    }

    if ( isset($arAssinaturas) && $inCountArrayAssinaturas > 0 ) {
        include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php" );
        $arAssinatura = $arAssinaturas['selecionadas'];
        $obTEmpenhoEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
        $obTEmpenhoEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
        $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_entidade', $_POST['inCodEntidade'] );
        $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_empenho', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
        $arPapel = $obTEmpenhoEmpenhoAssinatura->arrayPapel();

            $boInserir = 'true';
            $inCount = 0;
            $arAssinaInseridos = array();
            $arAssinaturaTemp = array_reverse($arAssinatura);
            foreach ($arAssinaturaTemp as $arAssina) {
                if ( !isset($arAssina['papel']) ) {
                    SistemaLegado::exibeAviso("Selecione um papel para cada nome selecionado!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,False);
                    exit;
                } else {
                    $stPapel = $arAssina['papel'];
                }

                if (array_key_exists($stPapel, $arPapel)) {
                    $inNumAssina = $arPapel[$stPapel];
                } elseif (array_search($stPapel, $arPapel)) {
                    $inNumAssina = $stPapel;
                }

                foreach ($arAssinaInseridos as $inCGMTemp => $stPapelTemp) {
                    if ($arAssina['inCGM'] != $inCGMTemp && $inNumAssina != $stPapelTemp) {
                        $boInserir = 'true';
                    } else {
                        $boInserir = 'false';
                        break;
                    }
                }
                if ($boInserir == 'true') {
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'num_assinatura', $inNumAssina );
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'numcgm',$arAssina['inCGM'] );
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                    $obErro = $obTEmpenhoEmpenhoAssinatura->inclusao($boTransacao);
                    $arAssinaInseridos[$arAssina['inCGM']] = $inNumAssina;
                }
                $inCount++;
            }
            unset($obTEmpenhoEmpenhoAssinatura);
            // Limpa Sessao->assinaturas
            $arAssinaturas = array( 'disponiveis'=>array(), 'papeis'=>array(), 'selecionadas'=>array() );
            Sessao::write('assinaturas', $arAssinaturas);
    }
    }
    $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obREmpenhoEmpenhoAutorizacao->obTEmpenhoEmpenhoAutorizacao);
    if ( !$obErro->ocorreu() ) {
        if ($_REQUEST['boEmitirLiquidacao'] == "S") {
            $pgProx = CAM_GF_EMP_INSTANCIAS."liquidacao/FMManterLiquidacao.php";
            $stFiltroLiquidacao  = "&inCodEmpenho=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho();
            $stFiltroLiquidacao .= "&inCodPreEmpenho=".$_POST['inCodPreEmpenho'];
            $stFiltroLiquidacao .= "&inCodEntidade=".$_POST['inCodEntidade'];
            $stFiltroLiquidacao .= "&inCodReserva=".$_POST['inCodReserva'];
            $stFiltroLiquidacao .= "&inCodAutorizacao=".$_POST['inCodAutorizacao'];
            $stFiltroLiquidacao .= "&dtExercicioEmpenho=".Sessao::getExercicio();
            $stFiltroLiquidacao .= "&stEmitirEmpenho=S";
            $stFiltroLiquidacao .= "&stAcaoEmpenho=".$stAcao;
            $stFiltroLiquidacao .= "&pgProxEmpenho=".$pgFilt;
            $stFiltroLiquidacao .= "&acao=812&modulo=10&funcionalidade=202";
            $stFiltroLiquidacao .= "&acaoEmpenho=256&moduloEmpenho=10&funcionalidadeEmpenho=82";
            print '<script type="text/javascript">
                        mudaMenu         ( "Liquidação","202" );
                   </script>';
            SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=liquidar".$stFiltroLiquidacao, $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro, $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        }
        $stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEmpenho=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho(). "&inCodEntidade=" .$_POST['inCodEntidade']."&acao=" . Sessao::read('acao');
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    break;
}

?>
