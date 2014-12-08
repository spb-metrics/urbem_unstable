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
    * Página de Processamento para Pagamento do módulo Tesouraria
    * Data de Criação   : 27/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PRManterPagamento.php 35026 2008-10-28 22:25:44Z hboaventura $

    * Casos de uso: uc-02.04.05
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
require_once CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php";
require_once CAM_GF_TES_MAPEAMENTO."TTesourariaPagamento.class.php";
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->consultarTesouraria();

$arCheques = Sessao::read('arCheque');
$arNotas = Sessao::read('arNota');
$arPagamento = array();

list( $inCodBoletim , $stDtBoletim ) = explode ( ':' , $_REQUEST[ 'inCodBoletim' ] );

switch ($stAcao) {
    case 'incluir':
    $obErro = new Erro();
    foreach ($arCheques as $arCheque) {
        $inCount = 0;
        if (SistemaLegado::comparaDatas($arCheque['data_emissao'],$stDtBoletim)) {
            $obErro->setDescricao("Data do(s) cheque(s) igual ou superior a data do pagamento");
        }
        foreach ($arNotas as $arNota) {
            if ($arCheque['valor'] > 0) {
                $arPagamentoAux   = array();
                $arNotaLiquidacao = array();
                $arNotaPaga       = array();
                list( $inCodEmpenho, $stExercicioEmpenho    ) = explode( '/', $arNota['empenho'] );
                list( $inCodNota   , $stExercicioLiquidacao ) = explode( '/', $arNota['liquidacao'] );

                if ($arNota['vl_total'] > $arCheque['valor']) {
                    $arPagamentoAux['valor'] = $arCheque['valor'];
                    $arPagamentoAux['cod_plano'] = $arCheque['cod_plano'];
                    $arNotas[$inCount]['vl_total'] -= $arCheque['valor'];
                    $arCheque['valor'] = 0;

                    $arNotaLiquidacao[0]['cod_nota'       ] = $inCodNota;
                    $arNotaLiquidacao[0]['ex_nota'        ] = $stExercicioLiquidacao;
                    $arNotaLiquidacao[0]['cod_empeho'     ] = $inCodEmpenho;
                    $arNotaLiquidacao[0]['ex_empenho'     ] = $stExercicioEmpenho;
                    $arNotaLiquidacao[0]['dt_nota'        ] = $stDtBoletim;
                    $arNotaLiquidacao[0]['valor_pagar'    ] = number_format($arPagamentoAux['valor'],2,',','.');
                    $arNotaLiquidacao[0]['max_valor_pagar'] = $_POST['nuValorPagamento'];

                    $arNotaPaga[0]['cod_nota'    ] = $inCodNota;
                    $arNotaPaga[0]['exercicio'   ] = $stExercicioLiquidacao;
                    $arNotaPaga[0]['cod_entidade'] = $arNota['cod_entidade'];
                    $arNotaPaga[0]['cod_empeho'  ] = $inCodEmpenho;
                    $arNotaPaga[0]['ex_empenho'  ] = $stExercicioEmpenho;
                    $arNotaPaga[0]['dt_nota'     ] = $stDtBoletim;
                    $arNotaPaga[0]['vl_pago'     ] = $arPagamentoAux['valor'];
                    $arNotaPaga[0]['vl_a_pagar'  ] = $_POST['nuValorPagamento'];
                    $arNotaPaga[0]['numero_documento'] = $arCheque['num_cheque'];

                    $arPagamentoAux['nota_liquidacao'] = $arNotaLiquidacao;
                    $arPagamentoAux['nota_paga'      ] = $arNotaPaga;

                    $arPagamentos[] = $arPagamentoAux;
                } elseif ($arNota['vl_total'] > 0) {
                    $arPagamentoAux['valor'] = $arNota['vl_total'];
                    $arPagamentoAux['cod_plano'] = $arCheque['cod_plano'];
                    $arNotas[$inCount]['vl_total'] = 0;
                    $arCheque['valor'] -= $arNota['vl_total'];

                    $arNotaLiquidacao[0]['cod_nota'       ] = $inCodNota;
                    $arNotaLiquidacao[0]['ex_nota'        ] = $stExercicioLiquidacao;
                    $arNotaLiquidacao[0]['cod_empeho'     ] = $inCodEmpenho;
                    $arNotaLiquidacao[0]['ex_empenho'     ] = $stExercicioEmpenho;
                    $arNotaLiquidacao[0]['dt_nota'        ] = $stDtBoletim;
                    $arNotaLiquidacao[0]['valor_pagar'    ] = number_format($arPagamentoAux['valor'],2,',','.');
                    $arNotaLiquidacao[0]['max_valor_pagar'] = $_POST['nuValorPagamento'];

                    $arNotaPaga[0]['cod_nota'    ] = $inCodNota;
                    $arNotaPaga[0]['exercicio'   ] = $stExercicioLiquidacao;
                    $arNotaPaga[0]['cod_entidade'] = $arNota['cod_entidade'];
                    $arNotaPaga[0]['cod_empeho'  ] = $inCodEmpenho;
                    $arNotaPaga[0]['ex_empenho'  ] = $stExercicioEmpenho;
                    $arNotaPaga[0]['dt_nota'     ] = $stDtBoletim;
                    $arNotaPaga[0]['vl_pago'     ] = $arPagamentoAux['valor'];
                    $arNotaPaga[0]['vl_a_pagar'  ] = $_POST['nuValorPagamento'];
                    $arNotaPaga[0]['numero_documento'] = $arCheque['num_cheque'];

                    $arPagamentoAux['nota_liquidacao'] = $arNotaLiquidacao;
                    $arPagamentoAux['nota_paga'      ] = $arNotaPaga;

                    $arPagamentos[] = $arPagamentoAux;
                }
                $nuTotalPagamento += $arPagamentoAux['valor'];
            }
            
            $inCount++;
        }
    }

    if (!$obErro->ocorreu()) {
        $boPagamento = false;
        foreach ($arPagamentos as $arPagamento) {
            $obRTesourariaBoletim = new RTesourariaBoletim();
            $obRTesourariaBoletim->setExercicio  ( Sessao::getExercicio() );
            $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
            $obRTesourariaBoletim->setDataBoletim( $stDtBoletim );
            $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
            $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario( $_POST['stTimestampUsuario'] );
            $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( $_POST['inCodTerminal'] );
            $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $_POST['stTimestampTerminal'] );
            $obRTesourariaBoletim->addPagamento();

            //se e o segundo cheque que esta sendo pago, nao faz a retencao
            if ($boPagamento) {
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->boRetencaoExecutada = true;
            }
            $boPagamento = true;

            if ( $stDtBoletim == date( 'd/m/Y' ) ) {
                $stTimestamp = date( 'Y-m-d H:i:s.ms' );
            } else {
                list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
                $stTimestamp = substr($stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms'),0,-1);
            }
            $obRTesourariaBoletim->roUltimoPagamento->setTimestamp( $stTimestamp );

            $obErro = new Erro;
            if ($_POST['inCodOrdem']) {
                if (SistemaLegado::comparaDatas($_POST['stDtEmissaoOrdem'],$stDtBoletim)) {
                    $obErro->setDescricao("A data do pagamento é anterior à data de emissão da OP");
                }
            }
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem   ( $_POST['inCodOrdem']        );
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio     ( $_POST['stExercicioOrdem']          );
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataVencimento( '31/12/'.Sessao::getExercicio() );
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_POST['inCodEntidade']);
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano ( $arPagamento['cod_plano'] );
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio()   );
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setObservacao( $_POST['stObservacoes'] );

            if (!$obErro->ocorreu()) {
                if ($nuTotalPagamento == '0.00') {
                    $obErro->setDescricao("O valor a pagar deve ser maior que zero.");
                }

                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setNotaLiquidacao( $arPagamento['nota_liquidacao'] );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setValoresPagos( $arPagamento['nota_paga'] );

            }
            $obRTesourariaBoletim->roUltimoPagamento->boCheque = true;
            if (!$obErro->ocorreu()) {
                $obErro = $obRTesourariaBoletim->roUltimoPagamento->pagar();
            }

            $boRetencao = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();

            if ($obRTesourariaBoletim->roUltimoPagamento->obRTesourariaAutenticacao->getDescricao()) {
                Sessao::write('pagamento',true);
            }
            $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));

            if ( !$obErro->ocorreu() ) {
                $inCodOrdem       = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem();
                $stExercicioOrdem = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio();

                //adicionado para que pegue as informações de pagamento que não foram anuladas ainda para gerar após os registros da tabela
                //tcepb.pagamento_origem_recursos_interna
                $obTTesourariaPagamento = new TTesourariaPagamento;
                $stFiltroPagamentos = " where cod_nota=".$_REQUEST['inCodNota'];
                $stFiltroPagamentos .= " and exercicio='".$_REQUEST['stExercicioLiquidacao']."' ";
                $stFiltroPagamentos .="
                          AND NOT EXISTS ( SELECT 1 FROM tesouraria.pagamento_estornado
                                            WHERE pagamento.cod_nota = pagamento_estornado.cod_nota
                                              AND pagamento.timestamp = pagamento_estornado.timestamp
                                              AND pagamento.exercicio = pagamento_estornado.exercicio
                                              AND pagamento.cod_entidade = pagamento_estornado.cod_entidade )
                          AND NOT EXISTS ( SELECT 1 FROM tcepb.pagamento_origem_recursos_interna
                                                   WHERE pagamento_origem_recursos_interna.exercicio = pagamento.exercicio
                                                     AND pagamento_origem_recursos_interna.cod_nota = pagamento.cod_nota
                                                     AND pagamento_origem_recursos_interna.timestamp = pagamento.timestamp
                                                     AND pagamento_origem_recursos_interna.cod_entidade = pagamento.cod_entidade )

                          ";
                $obTTesourariaPagamento->recuperaTodos($rsPagamentos,$stFiltroPagamentos);

                if (isset($_POST['inCodOrigemRecurso'])) {
                    $arOrigemRecurso = explode('-', $_POST['inCodOrigemRecurso']);
                    require_once CAM_GPC_TPB_MAPEAMENTO."TTPBPagamentoOrigemRecursosInterna.class.php";
                    while (!$rsPagamentos->eof()) {
                        $obTTPBPagamentoOrigemRecursosInterna = new TTPBPagamentoOrigemRecursosInterna;
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_entidade', $_POST['inCodEntidade']);
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('exercicio', $_POST['stExercicioLiquidacao']);
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_nota', $_POST['inCodNota']);
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_origem_recursos', $arOrigemRecurso[0]);
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('exercicio_origem_recurso', $arOrigemRecurso[1]);
                        $obTTPBPagamentoOrigemRecursosInterna->setDado('timestamp', $rsPagamentos->getCampo('timestamp') );
                        $obErro = $obTTPBPagamentoOrigemRecursosInterna->inclusao($boTransacao);

                        if ($obErro->ocorreu()) {
                          SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                          SistemaLegado::LiberaFrames();
                          break;
                        }
                        $rsPagamentos->proximo();
                    }
                }
                            
                ###TCEAL
                $obAdministracaoConfiguracao = new TAdministracaoConfiguracao();
                $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE exercicio = '".Sessao::getExercicio()."' and cod_modulo = 2 and parametro = 'cod_uf'");
                $inCodUf = $rsAdministracaoConfiguracao->getCampo('valor');
                $stSiglaUf = SistemaLegado::pegaDado("sigla_uf","sw_uf","where cod_uf = ".$inCodUf."");
                $rsPagamentos->setPrimeiroElemento();
                if ($stSiglaUf == "AL") {
                    require_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPagamentoTipoDocumento.class.php";
                    $rsDocumentoCheque = new RecordSet();
                    $obTTCEALPagamentoTipoDocumento = new TTCEALPagamentoTipoDocumento();
                    $obTTCEALPagamentoTipoDocumento->setDado('cod_entidade', $arPagamento['nota_paga'][0]['cod_entidade']);
                    $obTTCEALPagamentoTipoDocumento->setDado('exercicio', $arPagamento['nota_paga'][0]['exercicio']);
                    $obTTCEALPagamentoTipoDocumento->setDado('cod_nota', $arPagamento['nota_paga'][0]['cod_nota']);
                    $obTTCEALPagamentoTipoDocumento->setDado('cod_tipo_documento',2);
                    $obTTCEALPagamentoTipoDocumento->setDado('timestamp', $rsPagamentos->getCampo('timestamp') );
                    $obTTCEALPagamentoTipoDocumento->setDado('num_documento', $arPagamento['nota_paga'][0]['numero_documento'] );
                    $obErro = $obTTCEALPagamentoTipoDocumento->recuperaPorChave($rsDocumentoCheque, "","",$boTransacao);
                    if ( $rsDocumentoCheque->getNumLinhas() < 0 ) {
                        $obErro = $obTTCEALPagamentoTipoDocumento->inclusao($boTransacao);
                    }
                    if ( $obErro->ocorreu() ) {
                        SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                        SistemaLegado::LiberaFrames();
                    }
                    
                }//fim TCEAL
            }
        }
        if (is_array($arCheques) && !$obErro->ocorreu()) {
            $obRTesourariaCheque = new RTesourariaCheque();
            foreach ($arCheques as $arCheque) {
                $obRTesourariaCheque->stNumCheque                                                 = $arCheque['num_cheque'        ];
                $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arCheque['cod_banco'         ];
                $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arCheque['cod_agencia'       ];
                $obRTesourariaCheque->obRMONContaCorrente->inCodigoConta                          = $arCheque['cod_conta_corrente'];

                $obRTesourariaCheque->baixarChequeEmissao($boTransacao);
            }
        }
    }

    if (!$obErro->ocorreu()) {
        if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
            SistemaLegado::alertaAviso($pgAutenticacao."?".( $_REQUEST['boPagarOutra'] ? $stPagarOutraAuth : "pg_volta=../pagamentos/".$pgList ),"Pagamento Concluído com Sucesso! (OP ".$inCodOrdem."/".$stExercicioOrdem.")","","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso(($_REQUEST['boPagarOutra'] ? $stPagarOutra : $pgList),"Pagamento Concluído com Sucesso! (OP ".$inCodOrdem."/".$stExercicioOrdem.")","","aviso", Sessao::getId(), "../");
        }
    } else {
        SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
        SistemaLegado::LiberaFrames();
    }

    break;

    case 'alterar':
    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio  ( Sessao::getExercicio() );
    $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
    $obRTesourariaBoletim->setDataBoletim( $stDtBoletim );
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario( $_POST['stTimestampUsuario'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( $_POST['inCodTerminal'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $_POST['stTimestampTerminal'] );
    $obRTesourariaBoletim->addPagamento();

    $obRTesourariaConfiguracao = new RTesourariaConfiguracao();
    $obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRTesourariaConfiguracao->consultarTesouraria();

    if ( $stDtBoletim == date( 'd/m/Y' ) ) {
        $obRTesourariaBoletim->roUltimoPagamento->setTimestampEstornado( date( 'Y-m-d H:i:s.ms' ) );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->stTimestampAnulada = date( 'Y-m-d H:i:s.ms' );
        $stDtEstorno = date( 'Ymd' );
    } else {
        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
        $obRTesourariaBoletim->roUltimoPagamento->setTimestampEstornado( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->stTimestampAnulada = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');
        $stDtEstorno = $stAno.$stMes.$stDia;
    }
    $obErro = new Erro;

    $arrNota = Sessao::read('arNota');

    if ( count($arrNota) > 0 ) {
        $inCount = 0;
        $inPos = 0;
        $nuTotalEstorno;
        $stMaiorData = 0;

        foreach ($arrNota as $arNota) {
            list( $inCodEmpenho, $stExercicioEmpenho    ) = explode( '/', $arNota['empenho'] );
            list( $inCodNota   , $stExercicioLiquidacao ) = explode( '/', $arNota['liquidacao'] );

            $nuValorPagar       = str_replace(',','.',str_replace('.','',$_POST["nuValorPagar_".($inCount+1)]    ));  /// Valor do Estorno
            $nuValorEstornar    = $arNota['vl_estornar'];  // Valor Prestado Contas
            $nuValorOriginal    = str_replace(',','.',str_replace('.','',$_POST["nuValorOriginal_".($inCount+1)] ));

            if ($nuValorEstornar != $nuValorOriginal) {
                $nuValorOriginal = $nuValorEstornar;
            }
            if ($nuValorPagar > $nuValorOriginal) {
                $obErro->setDescricao("O valor a estornar da nota $inCodNota não pode ser superior a R$ ".number_format($nuValorOriginal,2,',','.').".");
                break;
            }
            //$nuVlPagar = str_replace(".", "", $_POST["nuValorPagar_".($inCount+1)]);
            //$nuVlPagar = str_replace(",", ".", $nuVlPagar);

            if ($nuValorPagar > 0.00) {
                $arNotaPaga[$inPos]['cod_nota']           = $inCodNota;
                $arNotaPaga[$inPos]['exercicio']          = $stExercicioLiquidacao;
                $arNotaPaga[$inPos]['cod_entidade']       = $arNota['cod_entidade'];
                $arNotaPaga[$inPos]['cod_empeho']         = $inCodEmpenho;
                $arNotaPaga[$inPos]['ex_empenho']         = $stExercicioEmpenho;
                $arNotaPaga[$inPos]['dt_nota']            = $stDtBoletim;
                $arNotaPaga[$inPos]['timestamp']          = $arNota['timestamp'];
                $arNotaPaga[$inPos]['vl_estornado']       = $nuValorPagar;
                $arNotaPaga[$inPos]['vl_pago']            = $_POST['nuValorPagamento'];
                $arNotaPaga[$inPos]['cod_plano']          = $arNota['cod_plano'];
                $arNotaPaga[$inPos]['cod_plano_retencao'] = $arNota['cod_plano_retencao'];
                $arNotaPaga[$inPos]['exercicio_plano']    = $arNota['exercicio_plano'];
                $nuTotalEstorno = $nuTotalEstorno + $nuValorPagar;
                $inPos++;
                $arData = explode('/',$arNota['dt_pagamento']);
                $stData = $arData[2].$arData[1].$arData[0];
                if ($stData >= $stMaiorData) {
                    $stMaiorData = $stData;
                    $dtMaiorData = $arData[0].'/'.$arData[1].'/'.$arData[2];
                }
            }
            $inCount++;
        }

        if (!$nuTotalEstorno) {
            $nuTotalEstorno = $nuValorPagar;
        }
        if ($nuTotalEstorno > 0.00) {
            if ($stDtEstorno >= $stMaiorData) {
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem   ( $_POST['inCodOrdem']       );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio     ( $_POST['stExercicioOrdem']         );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setValorAnulado  ( number_format( $_POST['nuValorPagamento'], 2, ',', '.' ) );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_POST['inCodEntidade']);
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setDataPagamento( $dtMaiorData ); // data de pgto mais recente das notas com valor a estornar
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setObservacao( $_POST['stMotivo'] );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setValoresPagos( $arNotaPaga );
            } else {
                $obErro->setDescricao('A data do boletim deve ser igual ou superior à data do pagamento mais recente com valor a estornar informado ('.$dtMaiorData.').');
            }
        } else {
            $obErro->setDescricao("O valor a estornar deve ser maior que 0,00.");
        }
    }
    if (!$obErro->ocorreu()) {
        $obErro = $obRTesourariaBoletim->roUltimoPagamento->estornar( $boTransacao );
    }

    $arCheques = Sessao::read('arCheque');
    if (is_array($arCheques) && !$obErro->ocorreu()) {
        $obRTesourariaCheque = new RTesourariaCheque();
        foreach ($arCheques as $arCheque) {
            $obRTesourariaCheque->stNumCheque                                                 = $arCheque['num_cheque'        ];
            $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arCheque['cod_banco'         ];
            $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arCheque['cod_agencia'       ];
            $obRTesourariaCheque->obRMONContaCorrente->inCodigoConta                          = $arCheque['cod_conta_corrente'];

            $obRTesourariaCheque->anularBaixaChequeEmissao();
        }
    }

    $boRetencao = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();

    if ($obRTesourariaBoletim->roUltimoPagamento->obRTesourariaAutenticacao->getDescricao()) {
        Sessao::write('pagamento',true);
    }

    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
    if ( !$obErro->ocorreu() ) {
        if( $obRTesourariaConfiguracao->getFormaComprovacao() )
            SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../pagamentos/".$pgList."&".Sessao::getId(),"Estorno de Pagamento Concluído com Sucesso! (OP: ".$_POST['inCodOrdem'] . "/" . Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"Estorno de Pagamento Concluído com Sucesso! (OP: ".$_POST['inCodOrdem'] . "/" . Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"n_estornar","erro");
        SistemaLegado::LiberaFrames();
    }

    break;

}

?>
