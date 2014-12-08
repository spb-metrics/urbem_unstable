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
    * Abertura Orcamento Anual
    * Data de Criação   : 13/08/2013
    * @author Analista: Valtair
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AberturaOrcamentoAnual";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro  = new Erro;
$obErroReceitaBruta = new Erro;
$obErroDespesa = new Erro;
$obErroReceitaBruta = new Erro;
$obErroReceitaDedutora = new Erro;
$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obTContabilidadeLancamento      = new TContabilidadeLancamento;

//Verifica cod_lote
$inCodLoteReceitaBruta = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'I'
                                                                                        and cod_entidade = ".$_POST['inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Receita Bruta') ");
$inCodLoteReceitaDedutora = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'I'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Receita Dedutora') ");
$inCodLoteDespesa = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'I'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Despesa') ");
$inCodLoteRecurso = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'I'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Recursos/Fontes Orçamento') ");
/*
 * Rotina de Inclusao
 */

//Deleta todos os Lancamentos Anteriores de Abertura de Orçamento
$arCodLoteLancamentoAnterior = array($inCodLoteReceitaBruta
                                    ,$inCodLoteReceitaDedutora
                                    ,$inCodLoteDespesa
                                    ,$inCodLoteRecurso);
foreach ($arCodLoteLancamentoAnterior as $cod_lote) {
    if ($cod_lote != "") {
        $obTContabilidadeLancamento->setDado("cod_entidade" , $_POST['inCodEntidade']);
        $obTContabilidadeLancamento->setDado("cod_lote"     , $cod_lote );
        $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);            
    }    
}

//--------------------------------------------------/////////////////////////////////////---------------------------------------------------------------
//--------------------------------------------------Receita Bruta Orçada para o Exercício---------------------------------------------------------------
//--------------------------------------------------/////////////////////////////////////---------------------------------------------------------------
$nuValor = 'nuValor_1';
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.1.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaBruta );
if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsReceitaBruta->getCampo( "cod_plano" )."-".$rsReceitaBruta->getCampo( "sequencia" )]=$nuValorImplantacao;
}

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.1.1.0.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaBruta );
if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsReceitaBruta->getCampo( "cod_plano" )."-".$rsReceitaBruta->getCampo( "sequencia" )]=$nuValorImplantacao;
}

//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Receita Bruta');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(820);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteReceitaBruta) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteReceitaBruta);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroReceitaBruta = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroReceitaBruta = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}

//reset do array de dados
$arAberturaOrcamento = array();

//----------------------------------------------------------//////////////////////////////////////////////----------------------------------------
//----------------------------------------------------------Receita Dedutora Bruta Orçada para o Exercício----------------------------------------
//----------------------------------------------------------//////////////////////////////////////////////----------------------------------------
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );
//FUNDEB
$nuValor2 = 'nuValor_3';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.01.01.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora );
if ( trim($_POST[$nuValor2]) != '' ) {
    $nuSomaValor3       = $_POST[$nuValor2];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor2]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor3 = $nuValorImplantacao;
    $arAberturaOrcamento[$rsReceitaDedutora->getCampo( "cod_plano" )."-".$rsReceitaDedutora->getCampo( "sequencia" )] = $nuValorImplantacao;
}
//RENUNCIA
$nuValor3 = 'nuValor_4';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.02.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora );
if ( trim($_POST[$nuValor3]) != '' ) {
    $nuSomaValor4       = $_POST[$nuValor3];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor3]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor4 = $nuValorImplantacao;
    $arAberturaOrcamento[$rsReceitaDedutora->getCampo( "cod_plano" )."-".$rsReceitaDedutora->getCampo( "sequencia" )] = $nuValorImplantacao;
}
//OUTRAS DEDUCOES
$nuValor4 = 'nuValor_5';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.99.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora );
if ( trim($_POST[$nuValor4]) != '' ) {
    $nuSomaValor5       = $_POST[$nuValor4];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor4]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor5 = $nuValorImplantacao;
    $arAberturaOrcamento[$rsReceitaDedutora->getCampo( "cod_plano" )."-".$rsReceitaDedutora->getCampo( "sequencia" )] = $nuValorImplantacao;
}
//RECEITA A DEDUTORA
//somar os valores de cada um dos campos
$nuValorReceitaDedutora = $nuSomaValor3 + $nuSomaValor4 + $nuSomaValor5;
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.1.1.0.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora );
if ( trim($nuValorReceitaDedutora) != '' ) {
    $nuValorImplantacao = str_replace('.','',$nuValorReceitaDedutora);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsReceitaDedutora->getCampo( "cod_plano" )."-".$rsReceitaDedutora->getCampo( "sequencia" )] = $nuValorImplantacao;
}
//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Receita Dedutora');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(822);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteReceitaDedutora) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteReceitaDedutora);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//Verifica se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
foreach ($arAberturaOrcamento as $inCodPlano_inCodSequencia => $nuValorLancamento) {
    $arTmp = explode( '-', $inCodPlano_inCodSequencia );
    $inCodPlano     = $arTmp[0] ;
    $inCodSequencia = $arTmp[1] ;

    if ($nuValorLancamento == 0.00) {
        //ZERAR QUALQUER LANCAMENTO QUANDO O USUARIO colocar valor = 0.00 deleta da base qualquer lancamento de abertura anterior
        $obTContabilidadeLancamento->setDado("cod_lote"     , $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
        $obTContabilidadeLancamento->setDado("cod_entidade" , $_POST['inCodEntidade'] );
        $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);
        unset($arAberturaOrcamento[$inCodPlano_inCodSequencia]);
    }
}
//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroReceitaDedutora = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroReceitaDedutora = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}
//$obErroReceitaDedutora = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);

//reset do array de dados
$arAberturaOrcamento = array();

//--------------------------------------------------------------/////////////////////////////////-----------------------------------------------------
//--------------------------------------------------------------Despesa Prevista para o Exercício-----------------------------------------------------
//--------------------------------------------------------------/////////////////////////////////-----------------------------------------------------
$nuValor = 'nuValor_6';
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.2.1.1.01.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsDespesaPrevista );
if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsDespesaPrevista->getCampo( "cod_plano" )."-".$rsDespesaPrevista->getCampo( "sequencia" )]=$nuValorImplantacao;
}

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.2.1.1.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsDespesaPrevista );
if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsDespesaPrevista->getCampo( "cod_plano" )."-".$rsDespesaPrevista->getCampo( "sequencia" )]=$nuValorImplantacao;
}
//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Despesa');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(821);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ( $inCodLoteDespesa ) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteDespesa);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroDespesa = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroDespesa = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}

//reset do array de dados
$arAberturaOrcamento = array();

//--------------------------------------------------------------///////////////////////////////////////////-------------------------------------------
//--------------------------------------------------------------LANÇAMENTOS DE ABERTURA DOS RECURSOS/FONTES-------------------------------------------
//--------------------------------------------------------------///////////////////////////////////////////-------------------------------------------
//LANÇAMENTOS DE ABERTURA DOS RECURSOS/FONTES
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
$obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
$obTContabilidadePlanoBanco->setDado( 'exercicio',Sessao::getExercicio() );
$obTContabilidadePlanoBanco->recuperaSaldoInicialRecurso($rsRecursos);
//busca os saldos iniciais de recursos e soma todos os valores
$rsRecursos->setPrimeiroElemento();
while ( !$rsRecursos->eof() ) {
    $nuValorSaldoRecurso += $rsRecursos->getCampo('saldo');
    $rsRecursos->proximo();
}
$nuValorSaldoRecurso = str_replace(',','.',$nuValorSaldoRecurso);

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "7.2.1.1.1.00.01.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsRecursoFonte );
if ( trim($nuValorSaldoRecurso) != '' ) {
    $arAberturaOrcamento[$rsRecursoFonte->getCampo( "cod_plano" )."-".$rsRecursoFonte->getCampo( "sequencia" )]=$nuValorSaldoRecurso;
}
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "8.2.1.1.1.00.01.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsRecursoFonte );
if ( trim($nuValorSaldoRecurso) != '' ) {
    $arAberturaOrcamento[$rsRecursoFonte->getCampo( "cod_plano" )."-".$rsRecursoFonte->getCampo( "sequencia" )]=$nuValorSaldoRecurso;
}

$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Recursos/Fontes Orçamento');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(823);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteRecurso) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteRecurso);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}
//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroRecurso = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroRecurso = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}
    //verifica se ocorreu erro em todos os lancamentos
    if( !$obErroRecurso->ocorreu()
        && !$obErroDespesa->ocorreu()
        && !$obErroReceitaBruta->ocorreu()
        && !$obErroReceitaDedutora->ocorreu()
        && !$obErro->ocorreu()
        ){
            SistemaLegado::alertaAviso($pgForm, "1 - ".($obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() ? $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() : "0")."", "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

?>
