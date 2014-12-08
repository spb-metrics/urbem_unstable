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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id: $

    * Casos de uso: uc-03.05.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function geraArquivosRegContabeis(&$obExportador , $stDataInicial, $stDataFinal)
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"  );
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"  );
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"  );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    $obTOrcamentoReceita = new TOrcamentoReceita;
    $obTOrcamentoReceita->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTOrcamentoReceita->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTOrcamentoReceita->setDado( 'dt_inicial'  , $stDataInicial );
    $obTOrcamentoReceita->setDado( 'dt_final'  , $stDataFinal );
    $obTOrcamentoReceita->recuperaReceitaArrecadada( $rsReceitaArrecadada);

    $obExportador->addArquivo("receitaarrecadada.txt");
    $obExportador->roUltimoArquivo->addBloco($rsReceitaArrecadada);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_criacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_nat_receita");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_original");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

//    $obTEmpenhoNotaLiquidacaoItemAnulado = new TEmpenhoNotaLiquidacaoItemAnulado;
//    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
//    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
//    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'dt_inicial'  , $stDataInicial );
//    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'dt_final'  , $stDataFinal );
//    $obTEmpenhoNotaLiquidacaoItemAnulado->recuperaEstornoLiquidacaoEsfinge( $rsEstornoLiquidacao );
////    sistemaLegado::mostravar( $rsEstornoLiquidacao );
//
//    $obExportador->addArquivo("estornoliquidacao.txt");
//    $obExportador->roUltimoArquivo->addBloco($rsEstornoLiquidacao );
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obTEmpenhoNotaLiquidacaoPaga = new TEmpenhoNotaLiquidacaoPaga;
//    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
//    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
//    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'dt_inicial'  , $stDataInicial );
//    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'dt_final'  , $stDataFinal );
//    $obTEmpenhoNotaLiquidacaoPaga->recuperaPagamentoEmpenhoEsfinge( $rsPagamentoEmpenho );
////    sistemaLegado::mostravar( $rsPagamentoEmpenho );
//
//    $obExportador->addArquivo("pagamentoempenho.txt");
//    $obExportador->roUltimoArquivo->addBloco($rsPagamentoEmpenho);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_vencimento");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obTEmpenhoNotaLiquidacaoContaPagadora = new TEmpenhoNotaLiquidacaoContaPagadora;
//    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
//    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
//    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'dt_inicial'  , $stDataInicial );
//    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'dt_final'  , $stDataFinal );
//    $obTEmpenhoNotaLiquidacaoContaPagadora->recuperaDesembolsoEsfinge( $rsDesembolso );
////    sistemaLegado::mostravar( $rsDesembolso );
//
//    $obExportador->addArquivo("desembolso.txt");
//    $obExportador->roUltimoArquivo->addBloco($rsDesembolso);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obTEmpenhoNotaLiquidacaoPagaAnulada = new TEmpenhoNotaLiquidacaoPagaAnulada;
//    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
//    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
//    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'dt_inicial'  , $stDataInicial );
//    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'dt_final'  , $stDataFinal );
//    $obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaEstornoPagamentoEsfinge( $rsEstornoPagamento );
////    sistemaLegado::mostravar( $rsEstornoPagamento );
//
//    $obExportador->addArquivo("estornopagamento.txt");
//    $obExportador->roUltimoArquivo->addBloco($rsEstornoPagamento );
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp_anulada");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
//
//    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
