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
 * Página de Processamento de Manter Homologação
 * Data de Criação: 23/10/2006

 * @author Analista: Anelise Schwengber
 * @author Desenvolvedor: Andre Almeida

 * @ignore

 * Casos de uso: uc-03.05.21

 $Id: PRManterAutorizacao.php 59718 2014-09-08 14:50:16Z jean $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacaoAnulada.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldosAnulada.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php";

include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgGera     = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

Sessao::setTrataExcecao ( true );

$obTLicHomologacao = new TLicitacaoHomologacao();
$obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos();

$stFiltroHomologacao  = " AND licitacao.cod_licitacao  = " . $_REQUEST['inCodLicitacao'];
$stFiltroHomologacao .= " AND licitacao.exercicio      = '" . Sessao::getExercicio() ."'";
$stFiltroHomologacao .= " AND licitacao.cod_entidade   = " . $_REQUEST['inCodEntidade'] ;
$stFiltroHomologacao .= " AND licitacao.cod_modalidade = " . $_REQUEST['inCodModalidade'];

$obTLicHomologacao->recuperaGrupoAutEmpenho( $rsAutEmpenho, $stFiltroHomologacao );

$arAutorizacao = array();
$inCont = 0;

$obErro  = new erro ;
$arErros = array();

$stErro = false;

//// data máxima para a entidade
$data = $_REQUEST['stDtAutorizacao'];
$ano = substr($data, 6, 4);
$mes = substr($data, 3, 2);
$dia = substr($data, 0, 2);
$dataFormatadaEntidade = $ano.$mes.$dia;

//// data licitação
$data1 = $_REQUEST['inDataLicitacao'];
$ano1 = substr($data1, 6, 4);
$mes1 = substr($data1, 3, 2);
$dia1 = substr($data1, 0, 2);
$dataFormatadaLicitacao = $ano1.$mes1.$dia1;

if (($dataFormatadaEntidade-$dataFormatadaLicitacao) < 0) {
    $stErro = "Data do Processo Licitatório superior à última autorização da entidade ".$_REQUEST['inCodEntidade'].".";
} elseif ($dataFormatadaEntidade - (date("Y").date("m").date("d")) > 0) {
    $stErro = "Data da Autorização deve ser menor ou igual a data atual. ";
}

if ($stErro) {
    SistemaLegado::exibeAviso( $stErro." ","n_incluir","erro");
    echo "<script>LiberaFrames(true,true);</script>";
} else {
    while (!$rsAutEmpenho->eof()) {
        // itens
        $stFiltroHomologacao_item  = $stFiltroHomologacao;
        $stFiltroHomologacao_item .= " AND cotacao_fornecedor_item.cgm_fornecedor = ".$rsAutEmpenho->getCampo("fornecedor") ;
        $stFiltroHomologacao_item .= " AND solicitacao_item_dotacao.cod_despesa   = ".$rsAutEmpenho->getCampo("cod_despesa") ;
        $stFiltroHomologacao_item .= " AND solicitacao_item_dotacao.cod_conta     = ".$rsAutEmpenho->getCampo("cod_conta");
        $stFiltroHomologacao_item .= " AND NOT EXISTS
                                           (
                                                SELECT  1
                                                  FROM  empenho.item_pre_empenho_julgamento
                                                 WHERE  item_pre_empenho_julgamento.exercicio_julgamento = cotacao_fornecedor_item.exercicio
                                                   AND  item_pre_empenho_julgamento.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                                                   AND  item_pre_empenho_julgamento.cod_item             = cotacao_fornecedor_item.cod_item
                                                   AND  item_pre_empenho_julgamento.lote                 = cotacao_fornecedor_item.lote
                                                   AND  item_pre_empenho_julgamento.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                                           ) ";

        $stFiltroHomologacao_item .= " AND NOT EXISTS
                                           (
                                                SELECT  1
                                                  FROM  compras.cotacao_anulada
                                                 WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                   AND  cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                                           )  ";

        $stOrdem = " ORDER BY catalogo_item.descricao ";
        $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacao( $rsItensAutEmpenho, $stFiltroHomologacao_item, $stOrdem );
        
        $arItensAutorizacao[] = $rsItensAutEmpenho->arElementos;

        $obTLicHomologacao = new TLicitacaoHomologacao();
        $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacaoImp( $rsItensAutEmpenhoImp, $stFiltroHomologacao_item, $stOrdem );

        $arItensAutorizacaoImp[] = $rsItensAutEmpenhoImp->arElementos;

        unset($stFiltroHomologacao_item );

        while ( !$rsItensAutEmpenho->eof() ) {

            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $rsItensAutEmpenho->getCampo('cod_reserva'));
            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $rsItensAutEmpenho->getCampo('exercicio_solicitacao'));
            $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao' , date('d/m/Y'));
            $obTOrcamentoReservaSaldosAnulada->consultar();

            $obExcecao = Sessao::getExcecao();
            if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                Sessao::getExcecao()->setDescricao("");
            }

            if ( !$obTOrcamentoReservaSaldosAnulada->getDado ( 'motivo_anulacao' ) ) {
                $obTOrcamentoReservaSaldosAnulada->setDado( 'motivo_anulacao' , 'Anulação Automática. Entidade: '.$rsAutEmpenho->getCampo( 'cod_entidade' ).' - '.$rsAutEmpenho->getCampo( 'nom_entidade' ).', Mapa de compras: '. $rsItensAutEmpenho->getCampo( 'cod_mapa' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) . '' );
                $obTOrcamentoReservaSaldosAnulada->inclusao( Sessao::getTransacao());
            }
            $rsItensAutEmpenho->proximo();
        }

        $rsAutEmpenho->proximo();
    }

    $stFiltroSolicitacaoLicitacao = $stFiltroHomologacao;
    $stFiltroSolicitacaoLicitacao.= "
                AND NOT EXISTS
                    (
                        SELECT  1
                          FROM  compras.cotacao_anulada
                         WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                           AND  cotacao_anulada.exercicio   = cotacao.exercicio
                    )

                AND NOT EXISTS
                    (
                        SELECT  1
                          FROM  compras.solicitacao_anulacao
                         WHERE  solicitacao_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                           AND  solicitacao_anulacao.exercicio   = solicitacao.exercicio
                           AND  solicitacao_anulacao.cod_entidade   = solicitacao.cod_entidade
                    )

                      GROUP BY  solicitacao.cod_solicitacao
                             ,  solicitacao.observacao
                             ,  solicitacao.exercicio
                             ,  solicitacao.cod_almoxarifado
                             ,  solicitacao.cod_entidade
                             ,  solicitacao.cgm_solicitante
                             ,  solicitacao.cgm_requisitante
                             ,  solicitacao.cod_objeto
                             ,  solicitacao.prazo_entrega
                             ,  solicitacao.timestamp";

    $obTLicHomologacao->recuperaSolicitacaoLicitacaoNaoAnulada( $rsSolicitacaoLicitacaoAtiva, $stFiltroSolicitacaoLicitacao );

    while (!$rsSolicitacaoLicitacaoAtiva->EOF()) {
        $observacaoSolicitacaoLicitacao .= $rsSolicitacaoLicitacaoAtiva->getCampo('observacao').'§§';
        $rsSolicitacaoLicitacaoAtiva->proximo();
    }

    Sessao::write('observacaoSolicitacao',$observacaoSolicitacaoLicitacao);

    $inCountAutorizacao = 0;
    $rsAutEmpenho->setPrimeiroElemento();

    while (!$rsAutEmpenho->eof()) {

        $obAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
        $obAutorizacaoEmpenho->boAutViaHomologacao = TRUE;
        $obAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutEmpenho->getCampo('cod_entidade') );
        $obAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
        $obAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsAutEmpenho->getCampo("cod_despesa") );
        $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsAutEmpenho->getCampo("mascara_classificacao") );
        $obAutorizacaoEmpenho->obRCGM->setNumCGM( $rsAutEmpenho->getCampo("fornecedor") );
        $obAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( 0 );
        $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial($_REQUEST['stDtAutorizacao']);
        $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( '31/12/'.date('Y') );
        $obAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao($_REQUEST['stDtAutorizacao']);
        $obAutorizacaoEmpenho->setDescricao( $rsAutEmpenho->getCampo("cod_objeto") . " - " . $rsAutEmpenho->getCampo("desc_objeto") );
        $obAutorizacaoEmpenho->setDtAutorizacao( $_REQUEST['stDtAutorizacao'] );
        $obAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $rsAutEmpenho->getCampo("reserva") );
        $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsAutEmpenho->getCampo("num_orgao") );
        $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $rsAutEmpenho->getCampo("num_unidade") );
        $obAutorizacaoEmpenho->setCodCategoria ( 1 );

        // atributo modalidade
        // array para relação entre modalidade licitacao e atributo modalidade do empenho
        $arModalidade = array(1 => 2, 2 => 3, 3 => 4, 4 => 0, 5 => 1, 6 => 11, 7 => 12,8 => 5,9 => 6, 10 => 13, 11 => 14);
        $inAtribModalidade = $arModalidade[$rsAutEmpenho->getCampo("cod_modalidade")];
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '101' , $inAtribModalidade );

        // atributo tipo credor
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '103' , 1 );

        // atributo complementar
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '100' , 2 );

        $inNumItemCont = 1;
        foreach ($arItensAutorizacaoImp[$inCountAutorizacao] as $chave =>$dadosItens) {

            // gerar autorização
            $obAutorizacaoEmpenho->addItemPreEmpenho();
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCompra( true );
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem( $inNumItemCont++ );
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioMapa($dadosItens['exercicio_mapa']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade($dadosItens['qtd_cotacao']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade($dadosItens['nom_unidade']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal($dadosItens['vl_cotacao']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem($dadosItens['descricao_completa']);
            //descricao_completa do item do catalogo concatenada com complemento do item na solicitacao
            $complemento = "";
            if (trim($dadosItens['descricao_completa'])) {
                $complemento .= trim($dadosItens['descricao_completa'])." ";
            }
            if (trim($dadosItens['complemento'])) {
                $complemento .= trim($dadosItens['complemento']);
            }
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento($complemento);
            //
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCgmFornecedor($dadosItens['fornecedor']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioJulgamento($dadosItens['exercicio']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setLoteCompras($dadosItens['lote']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCotacao($dadosItens['cod_cotacao']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItem($dadosItens['cod_item']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $dadosItens['cod_unidade']);
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $dadosItens['cod_grandeza'] );
            $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade($dadosItens['simbolo']);
        }

        $obErro = $obAutorizacaoEmpenho->incluir(Sessao::getTransacao());

        if ($obErro->ocorreu()) {
            $arErros[] = $dadosItens['cod_item'].': '.$obErro->getDescricao();
            break;
        } else {
            # Salvar Assinaturas configuráveis se houverem
            $arAssinaturas = Sessao::read('assinaturas');

            if (is_array($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
                $arAssinatura = $arAssinaturas['selecionadas'];

                $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('exercicio'       , $obAutorizacaoEmpenho->getExercicio());
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_entidade'    , $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_autorizacao' , $obAutorizacaoEmpenho->getCodAutorizacao());
                $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();

                foreach ($arAssinatura as $arAssina) {
                    if (isset($arAssina['papel'])) {
                        if (is_numeric($arAssina['papel'])) {
                            $inNumAssina = $arAssina['papel'];
                        } else {
                            $inNumAssina = $arPapel[$arAssina['papel']];
                        }
                    }

                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('num_assinatura', $inNumAssina);
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('numcgm'        , $arAssina['inCGM']);
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cargo'         , $arAssina['stCargo']);
                    $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao($boTransacao);
                }

                unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
                # Limpa Sessao->assinaturas
                # $arAssinaturas = array('disponiveis' => array(), 'papeis' => array(), 'selecionadas' => array());
                # Sessao::write('assinaturas', $arAssinaturas);
            }

            # Armazena os dados da autorização em array para depois ser usado na impressão.
            $arAutorizacao[$inCont++] = array(
                                            "inCodAutorizacao"	=> $obAutorizacaoEmpenho->getCodAutorizacao(),
                                            "inCodPreEmpenho" 	=> $obAutorizacaoEmpenho->getCodPreEmpenho(),
                                            "inCodEntidade" 	=> $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                            "stDtAutorizacao" 	=> $obAutorizacaoEmpenho->getDtAutorizacao(),
                                            "inCodDespesa" 		=> $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa(),
                                            "stExercicio"       => $obAutorizacaoEmpenho->getExercicio());
        }
        $inCountAutorizacao++;
        $rsAutEmpenho->proximo();
    }

    if (count($arAutorizacao) > 0) {
        if (count($arAutorizacao) == 1) {
            $stMsg = $arAutorizacao[0]['inCodAutorizacao']. "/".Sessao::getExercicio() ;
        } else {
            $inCont = count($arAutorizacao)-1;
            $stMsg = "Autorizações de ".$arAutorizacao[0]['inCodAutorizacao']."/".Sessao::getExercicio()." até ".$arAutorizacao[$inCont]['inCodAutorizacao']. "/".Sessao::getExercicio();
        }

        if (count($arErros) > 0) {
            $stErro = "Nem todas as autorizações foram realizadas.";
        }

        # Grava no array as autorizações geradas.
        Sessao::write('arAutorizacao', $arAutorizacao);

        # Exibe a mensagem e redireciona para a tela de download.
        SistemaLegado::alertaAviso($pgGera.'?'.Sessao::getId(), $stMsg , "incluir", "aviso", Sessao::getId(), "../");
    } else {
        $stErro = $arErros[0];
        SistemaLegado::exibeAviso( $stErro ,"n_incluir","erro");
        echo "<script>LiberaFrames(true,true);</script>";
    }
}

Sessao::encerraExcecao();

?>
