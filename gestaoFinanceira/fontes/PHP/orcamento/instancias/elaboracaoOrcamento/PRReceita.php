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
    * Página de Processamento de Comissao de Avaliacao
    * Data de Criação   : 02/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Id: PRReceita.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 31912 $
    $Name$
    $Autor: $
    $Date: 2008-02-19 08:17:10 -0300 (Ter, 19 Fev 2008) $

    * Casos de uso: uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"          );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';
include CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceitaCreditoTributario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "Receita";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoReceita = new ROrcamentoReceita;
$obTOrcamentoReceita = new TOrcamentoReceita;
$obTOrcamentoRecurso = new TOrcamentoRecurso;
$obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
$obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

function verificaValorConta($stClassificacao, $boTransacao = "")
{
    global $obROrcamentoReceita;
    $arClassReceita = preg_split( "/[^a-zA-Z0-9]/", $stClassificacao );
    $inCount        = count( $arClassReceita );

    //busca a posicao do ultimo valor na string de classificacao
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassReceita[$inPosicao] != 0) {
            break;
        }
    }

    for ($i = 0; $i <= $inPosicao; $i++) {
        $stClassFilha .= $arClassReceita[$i].".";
    }
    $stClassFilha = substr( $stClassFilha, 0, strlen( $stClassFilha ) - 1 );
    $stFiltro .= " AND classificacao like publico.fn_mascarareduzida('".$stClassFilha."') || '%' ";
    $obROrcamentoReceita->verificaValorConta( $inSumConta, $stFiltro, $boTransacao );

    return $inSumConta;
}

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
$obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
$obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
$obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
$obTOrcamentoConfiguracao->consultar();
if($obTOrcamentoConfiguracao->getDado("valor") == 'true') // Utilização da Destinação de Recursos || 2008 em diante
    $boDestinacao = true;

switch ($stAcao) {
    case "incluir":
        $obErro = new Erro;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $inSumConta = verificaValorConta( $_REQUEST['inCodReceita'], $boTransacao );

        if ($inSumConta > 0.00) {
            $obErro->setDescricao('Já houveram movimentações na classificação informada ('.$_REQUEST['inCodReceita'].')');
        } else {
            //busca o codigo da conta da Classificação de Receita informada
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['inCodReceita'] );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica, $boTransacao );
            $obROrcamentoReceita->setCreditoTributario ( $_POST['boCreditoTributario'] == "S" ? true : false );
            $inCodConta = $rsRubrica->getCampo( 'cod_conta' );
            if($_POST['nuValorOriginal'])
                $obROrcamentoReceita->setValorOriginal                         ( $_POST['nuValorOriginal']);
            else
                $obROrcamentoReceita->setValorOriginal                         ( 0.00);

            $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade      ( $_POST['inCodEntidade']  );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodConta( $inCodConta              );

            if ($boDestinacao) {
                $stFiltro .= " WHERE cod_conta    = ".$inCodConta;
                $stFiltro .= "   AND exercicio    = ".Sessao::getExercicio();
                $stFiltro .= "   AND cod_entidade = ".$_POST['inCodEntidade'];
                $obTOrcamentoReceita->recuperaTodos($rsReceita,$stFiltro, '', $boTransacao);

                if ($rsReceita->getNumLinhas() >= 1 ) {
                    $obErro->setDescricao("A Classificação de Receita informada já foi cadastrada no exercício de (".Sessao::getExercicio().")");
                }

                if (!$obErro->ocorreu()) {
                    $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                    $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                    $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                    $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                    $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                    $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                    $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                    $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                    if ($inCodRecursoExiste == '') {
                        $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                        $obTOrcamentoRecurso->proximoCod( $inCodRecurso, $boTransacao );
                        $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                        $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                        if (!$obErro->ocorreu()) {
                            $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio() );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso          );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);
                            $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                            $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                        }

                        if (Sessao::getExercicio() > '2008') {
                            $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                            $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                            $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                            $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                            // Verifica qual o cod_recurso que possui conta contabil vinculada C
                            $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                            $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                            $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                            $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                            $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                            $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                            if ($inCodRecursoBuscaC == '') {

                                if (!$obErro->ocorreu()) {
                                    $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                                    $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                                    $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                                    $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                                    if ($inProximoCodEstruturalC != 99) {
                                        $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                        $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                        $inProximoCodEstruturalC++;
                                        $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                        $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                        $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                        $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                        $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                        $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                        $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                        $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                        $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao);
                                    } else {
                                        SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                    }
                                }
                            }

                            // Verifica qual o cod_recurso que possui conta contabil vinculada D
                            $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                            $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                            $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                            $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                            if ($inCodRecursoBuscaD == '') {
                                if (!$obErro->ocorreu()) {
                                    $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                                    $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                                    $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                                    $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                                    if ($inProximoCodEstruturalD != 99) {
                                        $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                        $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                        $inProximoCodEstruturalD++;
                                        $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                                        $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                        $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                        $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                        $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                        $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                        $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                        $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                        $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao);
                                    } else {
                                        SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                    }
                                }
                            }
                        }
                    } else {
                        $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                    }
                }
            } else {
                $stFiltro .= " WHERE cod_conta    = ".$inCodConta;
                $stFiltro .= "   AND exercicio    = '".Sessao::getExercicio()."'";
                $stFiltro .= "   AND cod_entidade = ".$_POST['inCodEntidade'];
                $obTOrcamentoReceita->recuperaTodos($rsReceita, $stFiltro, '', $boTransacao);

                if ($rsReceita->getNumLinhas() >= 1 ) {
                    $obErro->setDescricao("A Classificação de Receita informada já foi cadastrada no exercício de (".Sessao::getExercicio().")");
                }

                $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso($_POST['inCodRecurso']);
            }

            if (!$obErro->ocorreu()) {
                $obErro = $obROrcamentoReceita->salvar($boTransacao);
                $inCodReceita = $obROrcamentoReceita->getCodReceita();

                if (!$obErro->ocorreu()) {
                    if ($_POST['boCreditoTributario'] == "S") {
                        if ($_POST['inCodContaCreditoTributario'] != '') {
                            $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                            $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $inCodReceita);
                            $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , Sessao::getExercicio());
                            $obTOrcamentoReceitaCreditoTributario->setDado('cod_conta'   , $_POST['inCodContaCreditoTributario']);

                            $obErro = $obTOrcamentoReceitaCreditoTributario->inclusao($boTransacao);
                        } else {
                            $obErro->setDescricao("A conta de Crédito Tributário deve ser escolhida");
                        }
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $obErro = lancarMetasReceita($boTransacao);
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?inCodEntidade=".$_POST['inCodEntidade'], $inCodReceita."/".$obROrcamentoReceita->getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $boDestinacao = isset($boDestinacao) ? $boDestinacao : false;
        $obErro = new Erro;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php");

        //busca o codigo da conta da Classificação de Receita informada
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['inCodEstrutural'] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica, $boTransacao );

        $inCodConta = $rsRubrica->getCampo( 'cod_conta' );

        $obROrcamentoReceita->setCodReceita                                ( $_POST['inCodFixacaoReceita'] );
        $obROrcamentoReceita->setValorOriginal                             ( $_POST['nuValorOriginal']     );
        $obROrcamentoReceita->setCreditoTributario                         ( $_POST['boCreditoTributario'] == "S" ? true : false );
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade      ( $_POST['inCodEntidade']       );

        if ($boDestinacao) {
            $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

            if (Sessao::getExercicio() < '2009') {
                $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()        );
                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $_REQUEST['inCodRecurso'] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                $obTOrcamentoRecursoDestinacao->alteracao( $boTransacao );
            } else {
                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $_REQUEST['inCodRecurso']);
                $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao", '');
                $obTOrcamentoRecursoDestinacao->recuperaPorChave($rsEspecificacao, $boTransacao);
                $inCodEspecificacao = $rsEspecificacao->getCampo('cod_especificacao');

                if ($inCodEspecificacao != $arDestinacaoRecurso[2]) {
                    $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                    $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                    $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                    $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                    $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                    $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                    $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                    if ($inCodRecursoExiste == '') {
                        $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                        $obTOrcamentoRecurso->proximoCod( $inCodRecurso, $boTransacao );
                        $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                        $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                        if (!$obErro->ocorreu()) {
                            $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio() );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso          );
                            $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]);
                            $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]);
                            $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                            $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                        }

                        $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio', Sessao::getExercicio());
                        $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                        $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);
                        $stNomEspecificacao = $rsEspecificacao->getCampo('descricao');

                        // Verifica qual o cod_recurso que possui conta contabil vinculada C
                        $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                        $obTOrcamentoRecursoDestinacao->setDado("cod_recurso", '');
                        $obTOrcamentoRecursoDestinacao->setDado("cod_uso", '');
                        $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao", '');
                        $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", '');
                        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                        $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoC, '', '', $boTransacao);

                        $inCodRecursoBuscaC = $rsContaRecursoC->getCampo('cod_recurso');

                        if ($inCodRecursoBuscaC == '') {

                            if (!$obErro->ocorreu()) {
                                $obRContabilidadePlanoBancoC = new RContabilidadePlanoBanco;
                                $obRContabilidadePlanoBancoC->setCodEstrutural('2.9.3.2.0.00.00.');
                                $obRContabilidadePlanoBancoC->getProximoEstruturalRecurso($rsProxCod, $boTransacao);
                                $inProximoCodEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
                                if ($inProximoCodEstruturalC != 99) {
                                    $obRContabilidadePlanoBancoC->obRContabilidadeSistemaContabil->setCodSistema(4);
                                    $obRContabilidadePlanoBancoC->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                    $inProximoCodEstruturalC++;
                                    $inProximoCodEstruturalC = str_pad($inProximoCodEstruturalC, 2, "0", STR_PAD_LEFT);
                                    $stCodEstruturalC = '2.9.3.2.0.00.00.'.$inProximoCodEstruturalC.'.00.00';
                                    $obRContabilidadePlanoBancoC->setCodEstrutural($stCodEstruturalC);
                                    $obRContabilidadePlanoBancoC->setNomConta($stNomEspecificacao);
                                    $obRContabilidadePlanoBancoC->setExercicio(Sessao::getExercicio());
                                    $obRContabilidadePlanoBancoC->setNatSaldo('C');
                                    $obRContabilidadePlanoBancoC->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                    $obRContabilidadePlanoBancoC->setContaAnalitica(true);

                                    $obErro = $obRContabilidadePlanoBancoC->salvar($boTransacao);
                                } else {
                                    SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                }
                            }
                        }

                        // Verifica qual o cod_recurso que possui conta contabil vinculada D
                        $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $arDestinacaoRecurso[2]);
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'1.9.3.2.0.00.00.%'");
                        $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecursoD, '', '', $boTransacao);

                        $inCodRecursoBuscaD = $rsContaRecursoD->getCampo('cod_recurso');

                        if ($inCodRecursoBuscaD == '') {
                            if (!$obErro->ocorreu()) {
                                $obRContabilidadePlanoBancoD = new RContabilidadePlanoBanco;
                                $obRContabilidadePlanoBancoD->setCodEstrutural('1.9.3.2.0.00.00.');
                                $obRContabilidadePlanoBancoD->getProximoEstruturalRecurso($rsProxCodD, $boTransacao);
                                $inProximoCodEstruturalD = $rsProxCodD->getCampo('prox_cod_estrutural');
                                if ($inProximoCodEstruturalD != 99) {
                                    $obRContabilidadePlanoBancoD->obRContabilidadeSistemaContabil->setCodSistema(4);
                                    $obRContabilidadePlanoBancoD->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                                    $inProximoCodEstruturalD++;
                                    $inProximoCodEstruturalD = str_pad($inProximoCodEstruturalD, 2, "0", STR_PAD_LEFT);
                                    $stCodEstruturalD = '1.9.3.2.0.00.00.'.$inProximoCodEstruturalD.'.00.00';
                                    $obRContabilidadePlanoBancoD->setCodEstrutural($stCodEstruturalD);
                                    $obRContabilidadePlanoBancoD->setNomConta($stNomEspecificacao);
                                    $obRContabilidadePlanoBancoD->setExercicio(Sessao::getExercicio());
                                    $obRContabilidadePlanoBancoD->setNatSaldo('D');
                                    $obRContabilidadePlanoBancoD->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                    $obRContabilidadePlanoBancoD->setContaAnalitica(true);

                                    $obErro = $obRContabilidadePlanoBancoD->salvar($boTransacao);
                                } else {
                                    SistemaLegado::exibeAviso("Limite de Contas Excedido","n_incluir","erro");
                                }
                            }
                        }
                    } else { // se ja existe o recurso cadastrado, so altera a receita como o novo cod_recurso
                        $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                    }
                } else { // se nao alterou a especificacao, altera somente o recurso
                    $stFiltroBuscaExiste  = ' WHERE exercicio = '.Sessao::getExercicio().' ';
                    $stFiltroBuscaExiste .= '   AND cod_uso = '.$arDestinacaoRecurso[0].' ';
                    $stFiltroBuscaExiste .= '   AND cod_destinacao = '.$arDestinacaoRecurso[1].' ';
                    $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arDestinacaoRecurso[2].' ';
                    $stFiltroBuscaExiste .= '   AND cod_detalhamento = '.$arDestinacaoRecurso[3].' ';
                    $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste, '', $boTransacao);
                    $inCodRecursoExiste = $rsDestinacao->getCampo('cod_recurso');

                    if ($inCodRecursoExiste == '') {
                        $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()        );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $_REQUEST['inCodRecurso'] );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]   );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]   );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]   );
                        $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                        $obTOrcamentoRecursoDestinacao->alteracao( $boTransacao );
                    } else {
                        $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso($inCodRecursoExiste);
                    }
                }
            }
        } else {
            $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso($_POST['inCodRecurso']);
        }

        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodConta($inCodConta);
        $obErro = $obROrcamentoReceita->salvar($boTransacao);

        $obTOrcamentoReceita->setDado('cod_receita'       , $_POST['inCodFixacaoReceita'] );
        $obTOrcamentoReceita->setDado('exercicio'         , Sessao::getExercicio());
        $obTOrcamentoReceita->recuperaPorChave($rsOrcamentoReceita, $boTransacao);
        
        $obTOrcamentoReceita->setDado('cod_entidade'      ,$_POST['inCodEntidade']);
        $obTOrcamentoReceita->setDado('cod_recurso'       ,$_POST['inCodRecurso']);
        $obTOrcamentoReceita->setDado('cod_conta'         ,$rsOrcamentoReceita->getCampo('cod_conta'));
        $obTOrcamentoReceita->setDado('credito_tributario',$_POST['boCreditoTributario'] == "S" ? true : false);
        $obTOrcamentoReceita->setDado('vl_original'       , $_POST['nuValorOriginal']);
        $obErro = $obTOrcamentoReceita->alteracao($boTransacao);
                
        if (!$obErro->ocorreu() ) {
            if ($_POST['boCreditoTributario'] == "S") {
                if ($_POST['inCodContaCreditoTributario'] != '') {
                    $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                    $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $obROrcamentoReceita->getCodReceita());
                    $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , Sessao::getExercicio());
                    $obTOrcamentoReceitaCreditoTributario->recuperaPorChave($rsContaCreditoTributario, $boTransacao);
                    $obTOrcamentoReceitaCreditoTributario->setDado('cod_conta'   , $_POST['inCodContaCreditoTributario']);
                    
                    if ($rsContaCreditoTributario->getNumLinhas() > 0) {
                        $obErro = $obTOrcamentoReceitaCreditoTributario->alteracao($boTransacao);
                    } else {
                        $obErro = $obTOrcamentoReceitaCreditoTributario->inclusao($boTransacao);
                    }
                } else {
                    $obErro->setDescricao("A conta de Crédito Tributário deve ser escolhida");
                }
            } else {
                $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                
                $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $obROrcamentoReceita->getCodReceita());
                $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , Sessao::getExercicio());

                $obErro = $obTOrcamentoReceitaCreditoTributario->exclusao($boTransacao);
            }
        }

        if (!$obErro->ocorreu() ) {
            $obErro = lancarMetasReceita($boTransacao);
        }

        $stFiltro = "";
        if (is_array($sessao->link)) {
            foreach ($sessao->link as $stCampo => $stValor) {
                $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
            }
        }
        $stFiltro .= "pg=".$sessao->link['pg']."&";
        $stFiltro .= "pos=".$sessao->link['pos']."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList, $_POST['inCodFixacaoReceita']."/".$obROrcamentoReceita->getExercicio(), "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obErro = new Erro;

        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php");
        $obRContablidadeLancamentoReceita   = new RContabilidadeLancamentoReceita;
        $obRContablidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio ( Sessao::getExercicio() );
        $obRContablidadeLancamentoReceita->consultarExistenciaReceita();

        if ( $obRContablidadeLancamentoReceita->getCountReceitaExercicio() == 0) {
            include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
            $obTContabilidadeDesdobramentoReceita   = new TContabilidadeDesdobramentoReceita;
            $obTContabilidadeDesdobramentoReceita->setDado( "exercicio", Sessao::getExercicio() );
            $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita", $_GET['inCodReceita'] );

            $obErro = $obTContabilidadeDesdobramentoReceita->verificaReceitaSecundaria( $boSecundaria );

            if (!$obErro->ocorreu() && !$boSecundaria ) {
                $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita', $_GET['inCodReceita']);
                $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'  , Sessao::getExercicio());

                $obTOrcamentoReceitaCreditoTributario->recuperaPorChave($rsContaCreditoTributario, $boTransacao);

                if ($rsContaCreditoTributario->getNumLinhas() > 0) {
                    $obTOrcamentoReceitaCreditoTributario->exclusao($boTransacao);
                }

                $obROrcamentoReceita->setCodReceita( $_GET['inCodReceita'] );
                $obROrcamentoReceita->setExercicio ( Sessao::getExercicio() );
                $obErro = $obROrcamentoReceita->excluir();
                if ($boDestinacao && $_REQUEST['inCodRecurso'] && !$obErro->ocorreu() ) {
                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio",     Sessao::getExercicio()        );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",   $_REQUEST['inCodRecurso'] );
                    $obTOrcamentoRecursoDestinacao->exclusao();

                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                    $obTOrcamentoRecurso = new TOrcamentoRecurso;
                    $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                    $obTOrcamentoRecurso->setDado("cod_recurso", $_REQUEST['inCodRecurso'] );
                    $obTOrcamentoRecurso->exclusao();
                }
            } else {
                $obErro->setDescricao("Receita Secundária - não pode ser excluída!");
            }
        } else {
            $obErro->setDescricao("Já existe movimentação nas contas.");
        }
        $stFiltro = "";
        if ($sessao->transf4['filtro']) {
            foreach ($sessao->transf4['filtro'] as $stCampo => $stValor) {
                $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
            }
        }
        $stFiltro .= "pg=".$sessao->transf4['pg']."&";
        $stFiltro .= "pos=".$sessao->transf4['pos']."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir", $_GET['inCodReceita']."/".$obROrcamentoReceita->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir", urlencode($obErro->getDescricao()) ,"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

/*
 *
 * LANÇAR METAS !!
 *
 */

function lancarMetasReceita($boTransacao = "")
{
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php"      );
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php" );
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"              );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"              );

    $obRPrevisaoReceita                 = new ROrcamentoPrevisaoReceita;
    $obROrcamentoPrevisaoOrcamentaria   = new ROrcamentoPrevisaoOrcamentaria;
    $obRConfiguracaoOrcamento           = new ROrcamentoConfiguracao;
    global $obROrcamentoReceita;
    global $request;
    $obTOrcamentoReceita                = new TOrcamentoReceita;

    $obErro = new Erro;

    $inNumColunas    = $request->get('inNumCampos');
    $vlValorOriginal = $request->get('nuValorOriginal');
    $inCodEstrutural = $request->get('inCodReceita');
    $inCodReceita    = $request->get('inCodFixacaoReceita');
    $inCodEntidade   = $request->get('inCodEntidade');

    $obRPrevisaoReceita->setQtdColunas ( $inNumColunas );
    $obRPrevisaoReceita->setQtdLinhas  ( 1 );
    $obRPrevisaoReceita->setExercicio  ( Sessao::getExercicio() );

    if (!$inCodReceita) {
        $obTOrcamentoReceita->setDado( 'cod_estrutural' , $inCodEstrutural );
        $obTOrcamentoReceita->setDado( 'exercicio'      , $obRPrevisaoReceita->getExercicio() );
        $obErro = $obTOrcamentoReceita->recuperaCodReceita( $rsCodReceita, $boTransacao );
        if ( !$rsCodReceita->eof() ) {
            $inCodReceita = $rsCodReceita->getCampo( 'cod_receita' );
            $obROrcamentoReceita->setCodReceita( $inCodReceita );
        }
    }

    $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
    $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->consultar( $rsPrevisaoOrcamentaria, $boTransacao );

    if ( $obRPrevisaoReceita->getExercicio() != $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->getExercicio() ) {
        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->salvar($boTransacao);
    }
    $vlTotal = isset($vlTotal) ? $vlTotal : 0;
    for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
        $inValor = "vlValor_".$inContColunas;

        $inValor = str_replace( ".", "" , $_POST[$inValor] );
        $inValor = str_replace( ",", ".", $inValor );
        $arValor[$inContColunas] = $inValor;
        $vlTotal += $inValor;
    }

    $vlTotal = $_POST['TotalValor'] ;
    $vlTotal = str_replace( ".", "" , $vlTotal );
    $vlTotal = str_replace( ",", ".", $vlTotal );

    $vlValorOriginal = str_replace( ".", "" , $vlValorOriginal );
    $vlValorOriginal = str_replace( ",", ".", $vlValorOriginal );

    $boSalvar = 0;

    if ($vlTotal <> 0.00) {
        if ( number_format($vlTotal,2,'.','') > number_format($vlValorOriginal,2,'.','')) {
            $obErro->setDescricao( "Valor Total das Metas de Arrecadação ultrapassou o Valor de Previsão da Receita." );
            $boSalvar++;
        } elseif ( number_format($vlTotal,2,'.','') < number_format($vlValorOriginal,2,'.','') ) {
            $obErro->setDescricao( "Valor Total das Metas de Arrecadação é inferior ao Valor de Previsão da Receita." );
            $boSalvar++;
        }
    } else {
        $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
        $obErro = $obRPrevisaoReceita->limparDados($boTransacao);
        $boSalvar++;
    }

    if ($boSalvar == 0) {
        $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
        $obErro = $obRPrevisaoReceita->limparDados($boTransacao);

        for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
            $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
            $obRPrevisaoReceita->setPeriodo         ( $inContColunas );
            $inValor = "vlValor_".$inContColunas;

            if ($arValor[$inContColunas] == "") {
                $obRPrevisaoReceita->setValorPeriodo ( 0 );
            } else {
                $valor = str_replace('.','',$$inValor);
                $valor = str_replace(',','.',$valor);
                $obRPrevisaoReceita->setValorPeriodo ( $arValor[$inContColunas] );
            }
            $obErro = $obRPrevisaoReceita->salvar($boTransacao);
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    return $obErro;
}

?>
