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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obHdnInId = new Hidden;
$obHdnInId->setName  ( 'inId' );
$obHdnInId->setValue ( $inId  );

$obVlrPrecoUnitario = new Moeda();
$obVlrPrecoUnitario->setRotulo('Preço Unitário');
$obVlrPrecoUnitario->setId('nuVlUnitario');
$obVlrPrecoUnitario->setName('nuVlUnitario');
$obVlrPrecoUnitario->setValue('0,0000');
$obVlrPrecoUnitario->setDecimais(4);
$obVlrPrecoUnitario->setNull( false );
$obVlrPrecoUnitario->setSize (23);

$obMontaItemUnidade = new IMontaItemUnidade(new Form);
$obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("*Item");
$obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);

$obMontaQuantidadeValores = new IMontaQuantidadeValores();
$obMontaQuantidadeValores->obValorUnitario->setRotulo('Valor da Cotação Unitária');
$obMontaQuantidadeValores->obValorUnitario->setName('nuVlReferencia');
$obMontaQuantidadeValores->obValorUnitario->setId('nuVlReferencia');
$obMontaQuantidadeValores->obValorUnitario->setValue('0,0000');
$obMontaQuantidadeValores->obValorUnitario->setDecimais ( 4 );
$obMontaQuantidadeValores->obValorUnitario->setNull( false );
$obMontaQuantidadeValores->obValorTotal->setRotulo( 'Valor Total da Cotação do Item' );
$obMontaQuantidadeValores->obValorTotal->setValue('0,0000');
$obMontaQuantidadeValores->obValorTotal->setDecimais ( 4 );
$obMontaQuantidadeValores->obValorTotal->setNull( true );

$obDtContacao = new Data();
$obDtContacao->setName('dtCotacao');
$obDtContacao->setId('dtCotacao');
$obDtContacao->setRotulo('Data da Cotação');
$obDtContacao->setTitle('Data da Cotação.');

$obVlrPrecoUnitario = new Moeda();
$obVlrPrecoUnitario->setRotulo('Preço Unitário');
$obVlrPrecoUnitario->setId('nuVlUnitario');
$obVlrPrecoUnitario->setName('nuVlUnitario');
$obVlrPrecoUnitario->setValue('0,0000');
$obVlrPrecoUnitario->setDecimais(4);
$obVlrPrecoUnitario->setNull( false );
$obVlrPrecoUnitario->setSize (23);
 
$obIntQtdeLicitada = new Quantidade();
$obIntQtdeLicitada->setRotulo('Quantidade Licitada');
$obIntQtdeLicitada->setId('nuQtdeLicitada');
$obIntQtdeLicitada->setName('nuQtdeLicitada');
$obIntQtdeLicitada->setValue('0,0000');
$obIntQtdeLicitada->setSize (23);
$obIntQtdeLicitada->setNull( false );

$obIntQtdeAderida = new Quantidade();
$obIntQtdeAderida->setRotulo('Quantidade Aderida');
$obIntQtdeAderida->setId('nuQtdeAderida');
$obIntQtdeAderida->setName('nuQtdeAderida');
$obIntQtdeAderida->setValue('0,0000');
$obIntQtdeAderida->setSize (23);
$obIntQtdeAderida->setNull( false );

$obVlrPercentualItem = new Porcentagem();
$obVlrPercentualItem->setId( 'nuPercentualItem' );
$obVlrPercentualItem->setName( 'nuPercentualItem' );
$obVlrPercentualItem->setRotulo( 'Percentual por Item' );
$obVlrPercentualItem->setTitle( 'Selecione o percentual de desconto por Item' );
$obVlrPercentualItem->setValue(0);

if ($stAcao == "alterar" && $inDescontoTabela == 2) {
    $obVlrPercentualItem->setDisabled(true);
}

$obBscCGMVencedor = new BuscaInner;
$obBscCGMVencedor->setRotulo( "CGM Vencedor do Registro de Preço" );
$obBscCGMVencedor->setTitle( "Informe o código do CGM Vencedor" );
$obBscCGMVencedor->setId( "inNomCGMVencedor" );
$obBscCGMVencedor->setValue($stNomCGMVencedor);
$obBscCGMVencedor->obCampoCod->setName("inNumCGMVencedor");
$obBscCGMVencedor->obCampoCod->setId("inNumCGMVencedor");
$obBscCGMVencedor->obCampoCod->setValue( $inNumCGMVencedor );
$obBscCGMVencedor->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMVencedor='+this.value,'buscaCGMVencedor');");
$obBscCGMVencedor->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMVencedor','inNomCGMVencedor','','".Sessao::getId()."&stCtrl=buscaCGMVencedor','800','550');" );

$obBtnSalvar = new Button;
$obBtnSalvar->setName  ("btnSalvar");
$obBtnSalvar->setId    ("btnSalvar");
$obBtnSalvar->setValue ("Incluir Item");
$obBtnSalvar->setTipo  ("button");
$obBtnSalvar->obEvento->setOnClick("montaParametrosGET('incluirListaItens');");

// Define Objeto Button para Limpar Item
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparFormItem');");

# Form Lote
$obSpnLote = new Span;
$obSpnLote->setId('spnLote');

# Form Item
$obSpnItem = new Span;
$obSpnItem->setId('spnItem');

# Table com Itens
$obSpanListaItem = new Span();
$obSpanListaItem->setID( 'spnListaItens' );


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
