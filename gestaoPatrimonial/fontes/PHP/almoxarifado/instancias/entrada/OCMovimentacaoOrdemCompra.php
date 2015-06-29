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
    * Arquivo Oculto da Entrada por Ordem de Compra
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id: OCMovimentacaoOrdemCompra.php 62703 2015-06-10 13:29:57Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

require_once(CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php" );
require_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdem.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

// função que monta a lista de item da ordem de compra
function montaListaItens($arItens)
{
    $stPrograma = "MovimentacaoOrdemCompra";
    $pgOcul = "OC".$stPrograma.".php";

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arItens );

    $rsListaItens->setPrimeiroElemento();

    $table = new TableTree();
    $table->setArquivo( 'OCMovimentacaoOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem' );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens a serem Atendidos');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Item' , 25 			);
    $table->Head->addCabecalho( 'Unidade de Medida' , 10 		);
    $table->Head->addCabecalho( 'Centro de Custo' , 25 		);
    $table->Head->addCabecalho( 'Solicitado OC' , 10 	);
    $table->Head->addCabecalho( 'Atendido OC' , 10 	);

    $stTitle = "";

    $table->Body->addCampo( 'nom_item', "E" 	);
    $table->Body->addCampo( 'nom_unidade', "C" 	);
    $table->Body->addCampo( 'centro_custo', "E"	 	);
    $table->Body->addCampo( 'solicitado_oc', "D" 	);
    $table->Body->addCampo( 'atendido_oc', "D"  	);

    $stMensagem = "";

    $table->Head->addCabecalho( 'Detalhar' , 10 	);
    $obRadioDetalhar = new Radio();
    $obRadioDetalhar->setName( 'item' );
    $obRadioDetalhar->obEvento->setOnChange( "selecionaItem(this);" );
    $obRadioDetalhar->obEvento->setOnClick( "executaFuncaoAjax( 'montaDadosItem', '&item='+this.id, false );" );
    $table->Body->addComponente( $obRadioDetalhar, "C" 	);

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    $stRetorno = "$('spnItens').innerHTML = '".$stHTML."';";
    $stRetorno .= (empty($stMensagem) ? "" : $stMensagem);

    return $stRetorno;
}

function montaListaItensAtendidos($arItens)
{
    $stPrograma = "MovimentacaoOrdemCompra";
    $pgOcul = "OC".$stPrograma.".php";

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arItens );

    $rsListaItens->setPrimeiroElemento();

    $table = new TableTree();
    $table->setArquivo( 'OCMovimentacaoOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem' );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens Atendidos');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Item' , 25 			);
    $table->Head->addCabecalho( 'Unidade de Medida' , 10 		);
    $table->Head->addCabecalho( 'Centro de Custo' , 25 		);
    $table->Head->addCabecalho( 'Solicitado OC' , 10 	);
    $table->Head->addCabecalho( 'Atendido OC' , 10 	);

    $stTitle = "";

    $table->Body->addCampo( 'nom_item', "E" 	);
    $table->Body->addCampo( 'nom_unidade', "C" 	);
    $table->Body->addCampo( 'centro_custo', "E"	 	);
    $table->Body->addCampo( 'solicitado_oc', "D" 	);
    $table->Body->addCampo( 'atendido_oc', "D"  	);

    $stMensagem = "";

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    $stRetorno = "$('spnItensAtendidos').innerHTML = '".$stHTML."';";
    $stRetorno .= (empty($stMensagem) ? "" : $stMensagem);

    return $stRetorno;
}

// Função que monta a listagem do perecíveis
function montaListaItensPerecivel($arItensPerecivel)
{
    $rsListaItensPerecivel = new RecordSet;
    $rsListaItensPerecivel->preenche( $arItensPerecivel );

    $table = new Table();
    $table->setRecordset( $rsListaItensPerecivel );
    $table->setSummary('Listagem Perecível');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Lote' , 25 );
    $table->Head->addCabecalho( 'Data de Fabricação' , 15 );
    $table->Head->addCabecalho( 'Data de Validade' , 15 );
    $table->Head->addCabecalho( 'Quantidade' , 10 );

    $table->Body->addCampo( 'inNumLotePerecivel', "E" );
    $table->Body->addCampo( 'dtFabricacaoPerecivel', "C" );
    $table->Body->addCampo( 'dtValidadePerecivel', "C" );
    $table->Body->addCampo( 'inQtdePerecivel', "E" );

    $table->Body->addAcao( "ALTERAR", 'alterarPerecivel(%d,%s,%s,%s,%d)', array('inNumLotePerecivel', 'dtFabricacaoPerecivel', 'dtValidadePerecivel', 'inQtdePerecivel', 'inNumLinhaListaPerecivel') );
    $table->Body->addAcao( "EXCLUIR", 'excluirPerecivel(%d,%d)', array('inNumLinhaListaPerecivel', $_REQUEST['inCodItem'] ) );

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    return "$('spnItensPereciveis').innerHTML = '".$stHTML."';";
}

// Função que limpa os dados da parte perecivel do item
function limpaDadosPerecivel()
{
    $stJs = "";
    $stJs .= "\n $('inNumLotePerecivel').value = '';";
    $stJs .= "\n $('dtFabricacaoPerecivel').value = '';";
    $stJs .= "\n $('dtValidadePerecivel').value = '';";
    $stJs .= "\n $('inQtdePerecivel').value = '';";
    $stJs .= "\n if ($('Incluir').value != 'Incluir') $('Incluir').value = 'Incluir';";

    return $stJs;
}

// Função que valida os dados para inserir na listagem dos perecíveis
function validaListaPerecivel()
{
    // valida o numero do lote
    if ($_REQUEST['inNumLotePerecivel'] == '') {
        $stMensagem = "O Número do Lote precisa ser preenchido.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    } else {
        // faz um somatório para guardar o total da quantidade da listagem
        $inTotQtde = 0;

        // faz a verificação se o lote já está na listagem, verificando se a linha da lista é diferente da alterada
        if ($_REQUEST['inNumLinhaListaPerecivel'])
        $arItensPerecivel = Sessao::read('arItensPerecivel');
        if (isset($arItensPerecivel[$_REQUEST['inCodItem']])) {
            foreach ($arItensPerecivel[$_REQUEST['inCodItem']] as $chave => $valor) {
                if( $_REQUEST['inNumLotePerecivel'] != $valor['inNumLotePerecivel'] )
                    $inTotQtde += str_replace(",",  ".", str_replace(".", "", $valor['inQtdePerecivel']));
                if ( ($valor['inNumLotePerecivel'] == $_REQUEST['inNumLotePerecivel']) && ($_REQUEST['inNumLinhaListaPerecivel'] != $valor['inNumLinhaListaPerecivel']) ) {
                    $stMensagem = "O item de Lote ".$_REQUEST['inNumLotePerecivel']." já está na lista.";

                    return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
                }
            }
        }
    }

    // valida a data de fabricação
    if ($_REQUEST['dtFabricacaoPerecivel'] == '') {
        $stMensagem = "A data de fabricação precisa ser preenchida.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } elseif (SistemaLegado::ComparaDatas($_REQUEST['dtFabricacaoPerecivel'], date("d/m/Y"))) {
        $stMensagem = "A data de fabricação não pode ser posterior a data de hoje.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    // valida a data de validade
    if ($_REQUEST['dtValidadePerecivel'] == '') {
        $stMensagem = "A data de validade precisa ser preenchida.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } elseif (SistemaLegado::ComparaDatas($_REQUEST['dtFabricacaoPerecivel'], $_REQUEST['dtValidadePerecivel'])) {
        $stMensagem = "A data de validade não pode ser posterior a data de fabricação.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } elseif (SistemaLegado::ComparaDatas(date('d/m/Y'), $_REQUEST['dtValidadePerecivel'])) {
        $stMensagem = "A data de validade não pode ser anterior a data de hoje.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    //valida a quantidade
    if (empty($_REQUEST['inQtdePerecivel'])) {
        $stMensagem = "A quantidade precisa ser preenchida.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } else {
        $inTotQtde             = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeEntrada']));
        $inQtdePerecivel       = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdePerecivel']));
        $inQtdeDisponivel      = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeDisponivel']));
        $inQtdeUltimoPerecivel = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeUltimoPerecivel']));

        if ($inQtdePerecivel <= 0) {
            $stMensagem = "A quantidade precisa ser um valor maior que zero.";

            return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

        } elseif (($_REQUEST['acaoPerecivel'] == 'incluir') && (($inQtdePerecivel+$inTotQtde) > $inQtdeDisponivel)) {
            $stMensagem = "A quantidade informada ultrapassa a quantidade do item selecionado na Ordem de Compra.";

            return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        } elseif (($_REQUEST['acaoPerecivel'] == 'alterar') && (($inQtdePerecivel+($inTotQtde-$inQtdeUltimoPerecivel)) > $inQtdeDisponivel)) {
            $stMensagem = "A quantidade informada ultrapassa a quantidade do item selecionado na Ordem de Compra.";

            return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }
    }
}

// Monta a listagem dos itens de entrada
function montaListaItensEntrada($arItensEntrada)
{
    $rsListaItensEntrada = new RecordSet;
    $rsListaItensEntrada->preenche( $arItensEntrada );

    $table = new Table();
    $table->setRecordset( $rsListaItensEntrada );
    $table->setSummary('Itens da Ordem de Compra para Entrada');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Item' , 20 );
    $table->Head->addCabecalho( 'Unidade de Medida' , 7 );
    $table->Head->addCabecalho( 'Marca' , 7 );
    $table->Head->addCabecalho( 'Centro de Custo' , 20 );
    $table->Head->addCabecalho( 'Qtde.' , 7 );
    $table->Head->addCabecalho( 'Valor Total' , 10 );

    $table->Body->addCampo( 'stItem', "E" );
    $table->Body->addCampo( 'stUnidadeMedida', "C" );
    $table->Body->addCampo( 'stMarca', "C" );
    $table->Body->addCampo( 'stCentroCusto', "E" );
    $table->Body->addCampo( 'inQtdeEntrada', "D" );
    $table->Body->addCampo( 'vlTotalItem', "D" );

    $table->Body->addAcao( "ALTERAR", 'alterarEntrada(%d)', array( 'inCheckBoxId' ) );
    $table->Body->addAcao( "EXCLUIR", 'excluirEntrada(%d)', array( 'inCodItem' ) );

    $table->Foot->addSoma( 'vlTotalItem', "D" );

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    return "$('spnItensEntrada').innerHTML = '".$stHTML."';";
}

// Função que valida os dados para inserir na listagem de entrada os itens
function validaListaEntrada()
{
    if ($_REQUEST['inCodItem'] == '') {
        $stMensagem = "Selecione um Item na listagem.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    if ( Sessao::read('boPerecivel') ) {
        $arItensPerecivel = Sessao::read('arItensPerecivel');
        if ( count($arItensPerecivel[$_REQUEST['inCodItem']]) < 1 ) {
            $stMensagem = "Deve ser incluído os dados perecíveis do item.";

            return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }
    }

    if ($_REQUEST['inCodAlmoxarifado'] == '') {
        $stMensagem = "O Almoxarifado precisa ser selecionado.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    if ($_REQUEST['inCodCentroCusto'] == '') {
        $stMensagem = "O Centro de Custo precisa ser selecionado.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    if ($_REQUEST['inCodMarca'] == '') {
        $stMensagem = "A Marca precisa ser seleionada.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    $inQtdeDisponivel = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeDisponivel']));
    $inQtdeEntrada = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeEntrada']));

    if ($inQtdeEntrada == '') {
        $stMensagem = "A Quantidade precisa ser preenchida.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } elseif (!($inQtdeEntrada > 0)) {
        $stMensagem = "A Quantidade deve ser maior que zero.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } elseif ($inQtdeEntrada > $inQtdeDisponivel) {
        $stMensagem = "A quantidade informada ultrapassa a quantidade disponivel do item selecionado.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    if ($_REQUEST['flValorTotalMercado'] == '') {
        $stMensagem = "O Valor Total de Mercado precisa ser preenchido.";

        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    } else {
        $flValorTotalMercado = str_replace(",", ".", str_replace(".", "", $_REQUEST['flValorTotalMercado']));
        if ($flValorTotalMercado <= 0) {
            $stMensagem = "O Valor Total de Mercado precisa ser maior que zero.";

            return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }
    }
}

// Função que limpa os dados quando o item vai para a listagem de entrada
function limpaDadosEntrada()
{
    $stJs = "";
    $stJs .= "\n frm.inCodAlmoxarifado.value = '';";
    $stJs .= "\n $('inCodMarca').value = '';";
    $stJs .= "\n $('stMarca').value = '&nbsp;';";
    $stJs .= "\n $('inCodCentroCusto').value = '';";
    $stJs .= "\n $('inQtdeEntrada').value = '';";
    $stJs .= "\n $('flValorTotalMercado').value = '';";
    $stJs .= "\n $('spnDetalheItem').innerHTML = '';";

    // desmarca a radio do item selecionado
    $stJs .= "\n $('item_".($_REQUEST['inNumLinhaEntrada']+1)."').checked = false;";

    return $stJs;
}

// Monta a tela de detalhes do item selecionado
function montaFormDadosItem($inItemId)
{
    // require dos arquivos necessários
    require_once(CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
    require_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php");
    require_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");
    require_once(CAM_FW_HTML."MontaAtributos.class.php");

    // inicializa os valores para saber se o item tem atributo e/ou se é perecível
    Sessao::write('boAtributo', false);
    Sessao::write('boPerecivel', false);
    Sessao::write('boBemPatrimonio', false);

    /*********************************************************
    *   Span de listagem de itens, caso o item seja perecível
    **********************************************************/
    $obSpnItensPereciveis = new Span();
    $obSpnItensPereciveis->setId( 'spnItensPereciveis' );

    /**********************************************************
    *                       Monta os campo
    **********************************************************/

    // $inItemId guarda o id do item selecionado. $arItemId[1] vai guardar o numero da linha.
    $arItemId = explode("_", $inItemId );

    // guarda o numero da linha do array de itens
    $inNumLinha = $arItemId[1]-1;

    $arItens = Sessao::read('arItens');
    $arItensEntrada = Sessao::read('arItensEntrada');
    $arItemLinha = Sessao::read('arItemLinha');

    // Faz um explode to nome do item, separando id da descrição
    $arItem = explode(" - ", $arItens[$inNumLinha]["nom_item"]);
    $inCodItem = $arItem[0];
    $stItem = $arItem[1];

    // recebe a unidade de medida do item
    $stUnidadeMedida = $arItens[$inNumLinha]["nom_unidade"];

    // atribui o valor a variável para poder fazer as verificações para saber se é para alterar o item ou não
    $boAlterarItem = false;

    if ( isset( $arItemLinha[$inCodItem] ) ) {
        $inNumLinhaEntrada = $arItemLinha[$inCodItem];

        foreach ($arItensEntrada as $chave =>$dados) {
            foreach ($arItens as $key => $dadosItens) {
                if ($dados['inNumItem'] == $dadosItens['num_item']) {
                    $boAlterarItem = true;
                }
            }
        }
    }

    //--------------------------------------
    // Monta atributos do item (caso tenha)
    //--------------------------------------

    // se não for, faz uma pesquisa para ver se o item tem algum atributo
    $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
    $obTAtributoCatalogoItem->setDado("cod_item", $inCodItem );
    $obTAtributoCatalogoItem->recuperaAtributoCatalogoItem( $rsAtributos );
    if ( $rsAtributos->getNumLinhas() > 0 ) {
        Sessao::write('boAtributo', true);
    }

    // se for para alterar, preenche os os dados com os dados no array de atributos
    if ($boAlterarItem) {
        $inCont = 0;
        $arItensAtributo = Sessao::read('arItensAtributo');
        while ( !$rsAtributos->eof() ) {
            $rsAtributos->arElementos[$inCont]['valor_padrao'] = $arItensAtributo[$inCodItem][$inCont]['stValor'];
            $rsAtributos->proximo();
            $inCont++;
        }
    }

    // caso o item tenha atributo, gera os campos no formulario
    if (Sessao::read('boAtributo')) {
        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos do Item no Estoque"  );
        $obMontaAtributos->setName       ( "Atributos_" );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );
        $obMontaAtributos->recuperaValores();
    }

    // ---------------------------------------
    // monta dados Item Perecível (caso tenha)
    // ---------------------------------------

    // faz uma busca verificando se o cod_tipo = 2 (perecível)
    $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalagoItem->setDado( "cod_item", $inCodItem );
    $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

    if ($rsCatalogoItem->getCampo('cod_tipo') == 2 ) {
        Sessao::write('boPerecivel', true);
    }

    // verifica se o item é perecível
    // caso seja, monta os campos dos itens do perecível
    if ( Sessao::read('boPerecivel') ) {

        // numero do lote do perecivel
        $obTxtNumLotePerecivel = new Inteiro();
        $obTxtNumLotePerecivel->setRotulo    ( 'Número do Lote' );
        $obTxtNumLotePerecivel->setName      ( "inNumLotePerecivel" );
        $obTxtNumLotePerecivel->setId        ( "inNumLotePerecivel" );
        $obTxtNumLotePerecivel->setTitle     ( "Informe o número do lote.");
        $obTxtNumLotePerecivel->setNull      ( true );
        $obTxtNumLotePerecivel->setObrigatorioBarra( true );

        // data de fabricação do perecivel
        $obDtFabricacaoPerecivel = new Data();
        $obDtFabricacaoPerecivel->setRotulo    ( 'Data de Fabricação' );
        $obDtFabricacaoPerecivel->setName      ( "dtFabricacaoPerecivel" );
        $obDtFabricacaoPerecivel->setId        ( "dtFabricacaoPerecivel" );
        $obDtFabricacaoPerecivel->setTitle     ( "Informe a data de fabricação.");
        $obDtFabricacaoPerecivel->setNull      ( true );
        $obDtFabricacaoPerecivel->setObrigatorioBarra( true );

        // data de validade do perecivel
        $obDtValidadePerecivel = new Data();
        $obDtValidadePerecivel->setRotulo    ( 'Data de Validade' );
        $obDtValidadePerecivel->setName      ( "dtValidadePerecivel" );
        $obDtValidadePerecivel->setId        ( "dtValidadePerecivel" );
        $obDtValidadePerecivel->setTitle     ( "Informe a data de validade.");
        $obDtValidadePerecivel->setNull      ( true );
        $obDtValidadePerecivel->setObrigatorioBarra( true );

        // campo quantidade do perecível
        $obQtdePerecivel = new Quantidade();
        $obQtdePerecivel->setName( 'inQtdePerecivel' );
        $obQtdePerecivel->setId( 'inQtdePerecivel' );
        $obQtdePerecivel->setNull(false);
        $obQtdePerecivel->setObrigatorioBarra( false );

        $obHdnInNumLinhaListaPerecivel = new Hidden();
        $obHdnInNumLinhaListaPerecivel->setName( 'inNumLinhaListaPerecivel' );
        $obHdnInNumLinhaListaPerecivel->setId( 'inNumLinhaListaPerecivel' );
    }

    //Item do tipo Bem Patrimonial
    if ($rsCatalogoItem->getCampo('cod_tipo') == 4 ) {
        Sessao::write('boBemPatrimonial', true);

        //cria span para o número da placa do bem
        $obSpnNumeroPlaca = new Span();
        $obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

        //instancio o componente TextBoxSelect para a situacao do bem
        $obITextBoxSelectSituacao = new TextBoxSelect();
        $obITextBoxSelectSituacao->setRotulo( 'Situação' );
        $obITextBoxSelectSituacao->setTitle( 'Informe a situação do bem.' );
        $obITextBoxSelectSituacao->setName( 'inCodTxtSituacao' );
        $obITextBoxSelectSituacao->setNull( false );

        $obITextBoxSelectSituacao->obTextBox->setName                ( "inCodTxtSituacao"     );
        $obITextBoxSelectSituacao->obTextBox->setId                  ( "inCodTxtSituacao"     );
        $obITextBoxSelectSituacao->obTextBox->setSize                ( 6                      );
        $obITextBoxSelectSituacao->obTextBox->setMaxLength           ( 3                      );
        $obITextBoxSelectSituacao->obTextBox->setInteiro             ( true                   );
        $obITextBoxSelectSituacao->obTextBox->setValue 				 ( $arItensEntrada[$inNumLinhaEntrada]['inCodSituacao'] );

        $obITextBoxSelectSituacao->obSelect->setName                ( "inCodSituacao"                 );
        $obITextBoxSelectSituacao->obSelect->setId                  ( "inCodSituacao"                 );
        $obITextBoxSelectSituacao->obSelect->setStyle               ( "width: 200px"                  );
        $obITextBoxSelectSituacao->obSelect->setCampoID             ( "cod_situacao"                  );
        $obITextBoxSelectSituacao->obSelect->setCampoDesc           ( "nom_situacao"                  );
        $obITextBoxSelectSituacao->obSelect->addOption              ( "", "Selecione"                 );

        //recupero todos os registros da table patrimonio.situacao_bem e preencho o componenete ITextBoxSelect
        $obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
        $obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

        $obITextBoxSelectSituacao->obSelect->preencheCombo( $rsSituacaoBem );
        $obITextBoxSelectSituacao->obSelect->setValue( $arItensEntrada[$inNumLinhaEntrada]['inCodSituacao'] );

        $obRdPlacaIdentificacaoSim = new Radio();
        $obRdPlacaIdentificacaoSim->setRotulo( 'Placa de Identificação' );
        $obRdPlacaIdentificacaoSim->setTitle( 'Informe se o item possui placa de identificação.' );
        $obRdPlacaIdentificacaoSim->setName( 'stPlacaIdentificacao' );
        $obRdPlacaIdentificacaoSim->setValue( 'sim' );
        $obRdPlacaIdentificacaoSim->setLabel( 'Sim' );
        $obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );

        $obRdPlacaIdentificacaoNao = new Radio();
        $obRdPlacaIdentificacaoNao->setRotulo( 'Placa de Identificação' );
        $obRdPlacaIdentificacaoNao->setTitle( 'Informe se o item possui placa de identificação' );
        $obRdPlacaIdentificacaoNao->setName( 'stPlacaIdentificacao' );
        $obRdPlacaIdentificacaoNao->setValue( 'nao' );
        $obRdPlacaIdentificacaoNao->setLabel( 'Não' );
        $obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );

        if ( ($arItensEntrada[$inNumLinha]['stPlacaIdentificacao'] == 'sim') || ($arItensEntrada[$inNumLinhaEntrada]['stPlacaIdentificacao'] == '' )) {
            $obRdPlacaIdentificacaoSim->setChecked( true );
            $montaPlaca = true;
        } else {
            $obRdPlacaIdentificacaoNao->setChecked( true );
            $montaPlaca = false;
        }
    } else {
        Sessao::remove('boBemPatrimonial');
    }

    // ---------------------------------------
    // monta os campos obrigatórios do item
    // ---------------------------------------

    // cria o objeto do form
    $obForm = new Form();

    // Combo de Almoxarifado
    $obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife();
    $obSelectAlmoxarifado->setObrigatorioBarra(true);
    if ($boAlterarItem) {
        $obSelectAlmoxarifado->setCodAlmoxarifado( $arItensEntrada[$inNumLinhaEntrada]['inCodAlmoxarifado'] );
    }

    $obTComprasOrdemCompra = new TComprasOrdem();
    $arItens = Sessao::read('arItens');
    $obTComprasOrdemCompra->setDado( "cod_ordem", $arItens[$inNumLinha]["cod_ordem"] );
    $obTComprasOrdemCompra->setDado( "tipo", "C");
    $stFiltro = " \n and centro_custo.cod_item = ".$inCodItem;
    // Faz a busca dos possiveis centros de custo em relação a ordem de compra
    $obTComprasOrdemCompra->recuperaCentroCustoPorOrdemCompra($rsOrdemCompraCentroCusto, $stFiltro);

    if (!$boAlterarItem) {
        // Faz a busca da marca para 'forçar' o valor setado
        $obTComprasOrdemCompra->recuperaMarcaPorOrdemCompra( $rsOrdemCompraMarca, $stFiltro );
    }

    $obCentroCusto = new Select();
    $obCentroCusto->setTitle("Informe o centro de custo.");
    $obCentroCusto->setName('inCodCentroCusto');
    $obCentroCusto->setId('inCodCentroCusto');
    $obCentroCusto->setCampoId('cod_centro');
    $obCentroCusto->addOption('', 'Selecione');
    $obCentroCusto->setCampoDesc('[cod_centro] - [centro_custo]');
    $obCentroCusto->setRotulo( 'Centro de Custo' );
    $obCentroCusto->obEvento->setOnChange("");
    $obCentroCusto->preencheCombo( $rsOrdemCompraCentroCusto );
    $obCentroCusto->setNull(true);
    $obCentroCusto->setObrigatorioBarra(true);
    if ($boAlterarItem) {
        $obCentroCusto->setValue( $arItensEntrada[$inNumLinhaEntrada]['inCodCentroCusto'] );
    } else
        $obCentroCusto->setValue( $rsOrdemCompraCentroCusto->getCampo('cod_centro') );

    // Campo Marca
    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setTitle("Informe a marca do item.");
    $obMarca->obCampoCod->setId('inCodMarca');
    $obMarca->obCampoCod->setName('inCodMarca');
    $obMarca->setId('stMarca');
    $obMarca->setName('stMarca');
    $obMarca->setNull (true);
    $obMarca->setObrigatorioBarra(true);
    if ($boAlterarItem) {
        $obMarca->obCampoCod->setValue( $arItensEntrada[$inNumLinhaEntrada]['inCodMarca'] );
        $obMarca->setValue( $arItensEntrada[$inNumLinhaEntrada]['stMarca'] );
    } else {
        $obMarca->obCampoCod->setValue( $rsOrdemCompraMarca->getCampo('cod_marca') );
        $obMarca->setValue( $rsOrdemCompraMarca->getCampo('marca') );
    }

    // Campo Quantidade
    $obQtdeEntrada = new Quantidade();
    $obQtdeEntrada->setName( 'inQtdeEntrada' );
    $obQtdeEntrada->setId( 'inQtdeEntrada' );
    $obQtdeEntrada->setNull(false);
    if ($boAlterarItem) {
        $obQtdeEntrada->setValue( $arItensEntrada[$inNumLinhaEntrada]['inQtdeEntrada'] );
    } elseif ( !Sessao::read('boPerecivel') ) {
        $inQtdeItem = ($arItens[$inNumLinha]['qtde_disponivel_oc']);
        $obQtdeEntrada->setValue( number_format($inQtdeItem, 4, ",", "." ) );
    }

    if ( Sessao::read('boPerecivel') ) {
        $obQtdeEntrada->setDisabled( true );
    }

    // Campo Valor Total de Mercado
    $obVlTotalMercado = new TextBox();
    $obVlTotalMercado->setName( 'flValorTotalMercado' );
    $obVlTotalMercado->setId( 'flValorTotalMercado' );
    $obVlTotalMercado->setRotulo( 'Valor Unitário do Item' );
    $obVlTotalMercado->setTitle( 'Informe o valor empenhado do item.' );
    $obVlTotalMercado->setNull(true);
    $obVlTotalMercado->setReadOnly( true );
    $obVlTotalMercado->setObrigatorioBarra(true);

    if ($boAlterarItem) {
        $obVlTotalMercado->setValue( $arItensEntrada[$inNumLinhaEntrada]['flValorTotalMercado'] );
    } else {
        $obVlTotalMercado->setValue( $arItens[$inNumLinha]['vl_empenhado'] );
    }

    $obTxtComplemento = new TextBox;
    $obTxtComplemento->setName      ( "stComplemento" );
    $obTxtComplemento->setId        ( "stComplemento" );
    $obTxtComplemento->setRotulo    ( "Complemento" );
    $obTxtComplemento->setTitle     ( "Informe um complemento para o item.");
    $obTxtComplemento->setNull      ( true );
    $obTxtComplemento->setMaxLength ( 160 );
    $obTxtComplemento->setSize      ( 100 );
    if ($boAlterarItem) {
        $obTxtComplemento->setValue( $arItensEntrada[$inNumLinhaEntrada]['stComplemento'] );
    }

    // ---------------------------------------
    // monta os hiddens do formulario
    // ---------------------------------------

    // Campo Hidden com o código do item
    $obHdnCodItem = new Hidden();
    $obHdnCodItem->setId( 'inCodItem' );
    $obHdnCodItem->setName( 'inCodItem' );
    $obHdnCodItem->setValue( $inCodItem );

    // Hidden com o nro do checkbox do item.
    $obHdnCheckBoxId = new Hidden();
    $obHdnCheckBoxId->setId( 'inCheckBoxId' );
    $obHdnCheckBoxId->setName( 'inCheckBoxId' );
    $obHdnCheckBoxId->setValue( $inItemId );

    // Campo Hidden com o código do item
    $obHdnItem = new Hidden();
    $obHdnItem->setId( 'stItem' );
    $obHdnItem->setName( 'stItem' );
    $obHdnItem->setValue( $stItem );

    // Campo Hidden da unidade de medida do item
    $obHdnUnidadeMedida = new Hidden();
    $obHdnUnidadeMedida->setId( 'stUnidadeMedida' );
    $obHdnUnidadeMedida->setName( 'stUnidadeMedida' );
    $obHdnUnidadeMedida->setValue( $stUnidadeMedida );

    // hidden que recebe o id do item selecionado
    $obHdnNumItem = new Hidden();
    $obHdnNumItem->setId( 'inNumItem' );
    $obHdnNumItem->setName( 'inNumItem' );
    $obHdnNumItem->setValue( ($inNumLinha+1) );

    // hidden que recebe o codigo do tipo do item
    $obHdnCodTipoItem = new Hidden();
    $obHdnCodTipoItem->setId( 'inCodTipoItem' );
    $obHdnCodTipoItem->setName( 'inCodTipoItem' );
    $obHdnCodTipoItem->setValue( ($rsCatalogoItem->getCampo('cod_tipo')) );

    // hidden com a linha do array da lista de entrada
    if ($boAlterarItem) {
        $obHdnNumLinhaEntrada = new Hidden();
        $obHdnNumLinhaEntrada->setId( 'inNumLinhaEntrada' );
        $obHdnNumLinhaEntrada->setName( 'inNumLinhaEntrada' );
        $obHdnNumLinhaEntrada->setValue( $inNumLinhaEntrada );
    }

    // hidden com a diferença disponível de quantidade para o item selecionado
    $obHdnQtdeDisponivel = new Hidden();
    $obHdnQtdeDisponivel->setId( 'inQtdeDisponivel' );
    $obHdnQtdeDisponivel->setName( 'inQtdeDisponivel' );
    $obHdnQtdeDisponivel->setValue( number_format($arItens[$inNumLinha]["qtde_disponivel_oc"], 4, ",", "." ) );

    $obHdnQtdeUltimoPerecivel = new Hidden;
    $obHdnQtdeUltimoPerecivel->setId  ('inQtdeUltimoPerecivel');
    $obHdnQtdeUltimoPerecivel->setName('inQtdeUltimoPerecivel');

    $obHdnAcaoPerecivel = new Hidden;
    $obHdnAcaoPerecivel->setId   ('acaoPerecivel');
    $obHdnAcaoPerecivel->setName ('acaoPerecivel');
    $obHdnAcaoPerecivel->setValue('incluir');

    /**********************************************************
    *                       Monta o formulario
    **********************************************************/
    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );

    // se for um atributo, adiciona os atributos ao relatório
    if (Sessao::read('boAtributo')) $obMontaAtributos->geraFormulario ( $obFormulario );

    $obFormulario->addTitulo( 'Detalhes do Item '.$inCodItem.' - '.$stItem );

    // adiciona os campos hidden necessários no formulario
    $obFormulario->addHidden( $obHdnCodItem        );
    $obFormulario->addHidden( $obHdnItem           );
    $obFormulario->addHidden( $obHdnUnidadeMedida  );
    $obFormulario->addHidden( $obHdnQtdeDisponivel );
    $obFormulario->addHidden( $obHdnNumItem        );
    $obFormulario->addHidden( $obHdnCheckBoxId     );
    $obFormulario->addHidden( $obHdnCodTipoItem    );
    $obFormulario->addHidden( $obHdnAcaoPerecivel  );
    $obFormulario->addHidden( $obHdnQtdeUltimoPerecivel );

    if ( $boAlterarItem )
        $obFormulario->addHidden( $obHdnNumLinhaEntrada );

    // adiciona os campos obrigatórios do item
    $obFormulario->addTitulo    ( 'Dados do Item'       );
    $obFormulario->addComponente( $obSelectAlmoxarifado );
    $obFormulario->addComponente( $obCentroCusto        );
    $obFormulario->addComponente( $obMarca              );
    $obFormulario->addComponente( $obQtdeEntrada        );
    $obFormulario->addComponente( $obVlTotalMercado     );
    $obFormulario->addComponente( $obTxtComplemento     );

    if ( Sessao::read('boBemPatrimonial') ) {
        $obFormulario->addTitulo( 'Detalhes Bem Patrimonial' );
        $obFormulario->addComponente( $obITextBoxSelectSituacao );
        $obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao ) );
        $obFormulario->addSpan( $obSpnNumeroPlaca );
    }

    // se for perefivel, adiciona os campos de perecível
    if ( Sessao::read('boPerecivel') ) {
    $obFormulario->addTitulo    ( 'Perecível'                       );
        $obFormulario->addComponente( $obTxtNumLotePerecivel            );
        $obFormulario->addComponente( $obDtFabricacaoPerecivel          );
        $obFormulario->addComponente( $obDtValidadePerecivel            );
        $obFormulario->addComponente( $obQtdePerecivel                  );
        $obFormulario->addHidden    ( $obHdnInNumLinhaListaPerecivel    );

        $obIncluir = new Button;
        $obIncluir->setValue            ( 'Incluir Lotes');
        $obIncluir->setName             ( 'Incluir');
        $obIncluir->setId               ( 'Incluir');
        $obIncluir->obEvento->setOnClick( "montaParametrosGET('montaListaItensPerecivel', 'inCodItem, inNumLotePerecivel, dtFabricacaoPerecivel, dtValidadePerecivel, inQtdePerecivel, inNumLinhaListaPerecivel, inQtdeEntrada, inQtdeDisponivel, Incluir, acaoPerecivel, inQtdeUltimoPerecivel'); jQuery('#Incluir').attr('disabled', 'disabled');");

        $obLimpar = new Button;
        $obLimpar->setValue             ( 'Limpar');
        $obLimpar->setName              ( 'Limpar');
        $obLimpar->setId                ( 'Limpar');
        $obLimpar->obEvento->setOnClick ( "executaFuncaoAjax('limpaDadosPerecivel', '')");

        $obFormulario->defineBarra( array($obIncluir, $obLimpar) );
        $obFormulario->addSpan( $obSpnItensPereciveis );
    }

    $inNumeroTotalItens = count(Sessao::read('arItens'));

    $arInfoChecks = explode("_", $_REQUEST['item']);
    $inCheckBoxId = $arInfoChecks[1];

    $obCheckBoxProxProduto = new CheckBox;
    $obCheckBoxProxProduto->setRotulo('Ir para o próximo item');
    $obCheckBoxProxProduto->setValue('1');
    $obCheckBoxProxProduto->setChecked(Sessao::read('irProximoItem') == ""?false:true);
    $obCheckBoxProxProduto->setName('inProxItem');
    $obCheckBoxProxProduto->setId('inProxItem');
    $obCheckBoxProxProduto->obEvento->setOnClick('setarCheckBox();');

    if ($inNumeroTotalItens <= $inCheckBoxId) {
        $obCheckBoxProxProduto->setDisabled(true);
    }

    $obFormulario->addComponente( $obCheckBoxProxProduto );

    // monta os botões do formulario
    $obIncluir = new Button();
    $obIncluir->setValue            ( 'Salvar'                                  );
    $obIncluir->setName             ( 'incluirEntrada'                           );
    $obIncluir->setId               ( 'incluirEntrada'                           );
    $obIncluir->obEvento->setOnClick( "montaParametrosGET('incluirItemEntrada', 'stExercicio,inCodItem,stItem,inCodFornecedor,stDtOrdem,inCodEntidade,inOrdemCompra,inCodAlmoxarifado,inCodCentroCusto,inCodMarca,inQtdeDisponivel,inQtdeEntrada,flValorTotalMercado,inNumLinhaEntrada,stUnidadeMedida,stMarca,stComplemento,inNumItem,inCheckBoxId,stPlacaIdentificacao,inCodSituacao,stNumeroPlaca,inCodTipoItem,inProxItem')" );

    $obLimpar = new Button();
    $obLimpar->setValue             ( 'Limpar'                                     );
    $obLimpar->setName              ( 'limparEntrada'                              );
    $obLimpar->setId                ( 'limparEntrada'                              );
    $obLimpar->obEvento->setOnClick ( "montaParametrosGET('limparDadosItens', 'inOrdemCompra')" );

    $obFormulario->defineBarra( array($obIncluir, $obLimpar) );
    $obFormulario->montaInnerHTML();

    $stJs.= "$('spnDetalheItem').innerHTML = '".$obFormulario->getHTML()."';";

    //monta o text da placa de identificação por padrão
    if (Sessao::read('boBemPatrimonial') == true ) {
        $stJs.= montaPlacaIdentificacao($arItensEntrada[$inNumLinhaEntrada]['stNumeroPlaca'], $montaPlaca);
    }

    return $stJs;
}

function montaPlacaIdentificacao($numPlaca = "", $boMontaPlaca = false)
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
    $obTAdministracaoConfiguracao->pegaConfiguracao( $boPlacaAlfa, 'placa_alfanumerica' );

    if ($boMontaPlaca == true) {
        $obTxtNumeroPlaca = new TextBox();
        $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
        $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
        $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
        $obTxtNumeroPlaca->setId( 'stNumeroPlaca' );

        if ($boPlacaAlfa == 'false') {
            $obTxtNumeroPlaca->setInteiro (true);
        } else {
            $obTxtNumeroPlaca->setCaracteresAceitos( "[a-zA-Z0-9\-]" );
        }

        $obTxtNumeroPlaca->setNull( false );

        $obTPatrimonioBem = new TPatrimonioBem();

        if ($boPlacaAlfa == 'true') {
            $obTPatrimonioBem->recuperaMaxNumPlacaAlfanumerico($rsNumPlaca);
            $maxNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
        } else {
            $obTPatrimonioBem->recuperaMaxNumPlacaNumerico($rsNumPlaca);

            if ( $rsNumPlaca->getNumLinhas() <=0 ) {
                $inMaiorNumeroPlaca = 0;
            } else {
                $inMaiorNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
            }

            $maxNumeroPlaca = $inMaiorNumeroPlaca;
        }

        $arItensEntrada = Sessao::read('arItensEntrada');
        $vlTotalItens = 0;

        # Adiciona o total de quantidades na lista para sugerir a numeração da placa.
        if (is_array($arItensEntrada)) {
            foreach ($arItensEntrada as $key => $value) {
                $vlTotalItens += $value['inQtdeEntrada'];
            }
        }

        # Incrementa a sugestão do num_placa considerando as quantidades selecionadas no itens anteriores.
        for ($i = 0; $i <= $vlTotalItens; $i++)
            $maxNumeroPlaca++;

        $obTxtNumeroPlaca->setValue( $maxNumeroPlaca );

        $obTxtNumeroPlaca->obEvento->setOnChange( "montaParametrosGET( 'verificaIntervalo' );" );

        $obFormulario = new Formulario();
        $obFormulario->addComponente( $obTxtNumeroPlaca );
        $obFormulario->montaInnerHTML();

        $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
    } else {
        $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
    }

    return $stJs;
}

switch ($stCtrl) {

// monta o span com os itens da ordem de compra
case 'montaItens':

    include_once( TCOM.'TComprasNotaFiscalFornecedor.class.php' );
    $obTComprasNotaFiscalFornecedor = new TComprasNotaFiscalFornecedor();
    $obTComprasNotaFiscalFornecedor->setDado( 'exercicio', $_REQUEST['exercicio'] );
    $obTComprasNotaFiscalFornecedor->setDado( 'cod_entidade', $_REQUEST['cod_entidade'] );
    $obTComprasNotaFiscalFornecedor->setDado( 'cod_ordem', $_REQUEST['cod_ordem'] );
    $obTComprasNotaFiscalFornecedor->setDado( 'tipo'     , 'C' );
    $obTComprasNotaFiscalFornecedor->recuperaItensNotaOrdemCompra( $rsItens );

    $inCount = 0;
    $inCountAtendido = 0;
    $arItens = null;
    $arItensAtendido = null;

    while ( !$rsItens->eof() ) {
        if ($rsItens->getCampo('ativo') == true) {
            if ($rsItens->getCampo('qtde_disponivel_oc') > 0) {
                $arItens[$inCount]['cod_ordem'         ] = $_REQUEST['cod_ordem'];
                $arItens[$inCount]['num_item'          ] = $rsItens->getCampo('num_item');
                $arItens[$inCount]['nom_item'          ] = ( $rsItens->getCampo('cod_item') ) ? $rsItens->getCampo('cod_item').' - '.$rsItens->getCampo('nom_item') : $rsItens->getCampo('nom_item') ;
                $arItens[$inCount]['cod_pre_empenho'   ] = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['exercicio_empenho' ] = $rsItens->getCampo('exercicio_empenho');
                $arItens[$inCount]['nom_unidade'       ] = $rsItens->getCampo('nom_unidade');
                $arItens[$inCount]['centro_custo'      ] = ( $rsItens->getCampo('cod_centro') ) ? $rsItens->getCampo('cod_centro').' - '.$rsItens->getCampo('nom_centro') : null;
                $arItens[$inCount]['solicitado_oc'     ] = number_format($rsItens->getCampo('solicitado_oc'), 4, ',', '.');
                $arItens[$inCount]['atendido_oc'       ] = number_format($rsItens->getCampo('atendido_oc'), 4, ',', '.');
                $arItens[$inCount]['qtde_disponivel_oc'] = $rsItens->getCampo('qtde_disponivel_oc');
                $arItens[$inCount]['vl_empenhado'      ] = number_format($rsItens->getCampo('vl_empenhado'), 2, ',', '.');
                $inCount++;
            } else {
                $arItensAtendido[$inCountAtendido]['cod_ordem'         ] = $_REQUEST['cod_ordem'];
                $arItensAtendido[$inCountAtendido]['num_item'          ] = $rsItens->getCampo('num_item');
                $arItensAtendido[$inCountAtendido]['nom_item'          ] = ( $rsItens->getCampo('cod_item') ) ? $rsItens->getCampo('cod_item').' - '.$rsItens->getCampo('nom_item') : $rsItens->getCampo('nom_item') ;
                $arItensAtendido[$inCountAtendido]['cod_pre_empenho'   ] = $rsItens->getCampo('cod_pre_empenho');
                $arItensAtendido[$inCountAtendido]['exercicio_empenho' ] = $rsItens->getCampo('exercicio_empenho');
                $arItensAtendido[$inCountAtendido]['nom_unidade'       ] = $rsItens->getCampo('nom_unidade');
                $arItensAtendido[$inCountAtendido]['centro_custo'      ] = ( $rsItens->getCampo('cod_centro') ) ? $rsItens->getCampo('cod_centro').' - '.$rsItens->getCampo('nom_centro') : null;
                $arItensAtendido[$inCountAtendido]['solicitado_oc'     ] = number_format($rsItens->getCampo('solicitado_oc'), 4, ',', '.');
                $arItensAtendido[$inCountAtendido]['atendido_oc'       ] = number_format($rsItens->getCampo('atendido_oc'), 4, ',', '.');
                $arItensAtendido[$inCountAtendido]['qtde_disponivel_oc'] = $rsItens->getCampo('qtde_disponivel_oc');
                $arItensAtendido[$inCountAtendido]['vl_empenhado'      ] = number_format($rsItens->getCampo('vl_empenhado'), 2, ',', '.');
                $inCountAtendido++;
            }
        }
        $rsItens->proximo();
    }

    // Caso não tenha nenhum item a ser atendido, não monta a lista
    if (count($arItens) > 0)
        $stJs.= montaListaItens(  $arItens );

    // Caso não tenha nenhum item atendido, não monta a lista
    if (count($arItensAtendido) > 0)
        $stJs.= montaListaItensAtendidos(  $arItensAtendido );

    Sessao::write('arItens', $arItens);
    Sessao::write('arItensAtendido', $arItensAtendido);

break;

// monta os delalhes dos itens da listagem dos itens da ordem de compra
case 'detalharItem' :

    include_once( TCOM."TComprasOrdem.class.php" );
    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( 'exercicio'        , $_REQUEST['exercicio_empenho'] );
    $obTComprasOrdemCompra->setDado( 'cod_pre_empenho'  , $_REQUEST['cod_pre_empenho']   );
    $obTComprasOrdemCompra->setDado( 'num_item'         , $_REQUEST['num_item']          );
    $obTComprasOrdemCompra->setDado( 'tipo'             , 'C'          );
    $obTComprasOrdemCompra->recuperaDetalheItem( $rsDetalheItem );

    $obForm = new Form();
    $obForm->setName('detalharItem');
    $obForm->setId('detalharItem');

    if (!is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem'))) {
        $obLblCodItem = new Label();
        $obLblCodItem->setRotulo( 'Código do Item' );
        $codItem = (!is_null($rsDetalheItem->getCampo('cod_item'))) ? $rsDetalheItem->getCampo('cod_item') : $rsDetalheItem->getCampo('cod_item_ordem');
        $obLblCodItem->setValue( $codItem );
    }
    $obLblItem = new Label();
    $obLblItem->setRotulo( 'Descrição' );
    $obLblItem->setValue( $rsDetalheItem->getCampo('descricao') );

    $obLblGrandeza = new Label();
    $obLblGrandeza->setRotulo( 'Grandeza' );
    $obLblGrandeza->setValue( $rsDetalheItem->getCampo('nom_grandeza') );

    $obLblUnidade = new Label();
    $obLblUnidade->setRotulo( 'Unidade' );
    $obLblUnidade->setValue( $rsDetalheItem->getCampo('nom_unidade') );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( 'Detalhe do Item' );
    if (!is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem')))
        $obFormulario->addComponente( $obLblCodItem );
    $obFormulario->addComponente( $obLblItem );
    $obFormulario->addComponente( $obLblGrandeza );
    $obFormulario->addComponente( $obLblUnidade );
    $obFormulario->show();

    break;

// monta o span com os dados a serem preenchidos de cada item da ordem de compra
case 'montaDadosItem':

    // monta o formulario
    $stJs .= montaFormDadosItem( $_REQUEST['item'] );

    /* faz o mesmo processo que na montagem do formulario para poder pegar a linha
        pega item, que é o id do item, passado pelo onclick da listagem de itens*/
    $arItemId = explode("_", $_REQUEST['item'] );

    // pega a parte do numero da linha
    $inNumLinha = $arItemId[1]-1;

    $arItens = Sessao::read('arItens');
    $arItemLinha = Sessao::read('arItemLinha');
    $arItensEntrada = Sessao::read('arItensEntrada');
    $arItensPerecivel = Sessao::read('arItensPerecivel');

    // Faz um explode to nome do item, separando id da descrição
    $arItem = explode(" - ", $arItens[$inNumLinha]["nom_item"]);
    $inCodItem = $arItem[0];

    // atribui o valor a variável para poder fazer as verificações para saber se é para alterar o item ou não
    if ( isset( $arItemLinha[$inCodItem] ) ) {
        $inNumLinhaEntrada = $arItemLinha[$inCodItem];

        if (count($arItensEntrada[$inNumLinha]) > 0 )
            $stJs .= "$('incluirEntrada').value = 'Alterar';";
    }

    // pega o código do item para poder fazer a verificação no array de itens perecivel
    $arItem = explode(" - ", $arItens[$inNumLinha]["nom_item"]);
    if ( count($arItensPerecivel[$arItem[0]] ) > 0 ) {
        $stJs .= montaListaItensPerecivel( $arItensPerecivel[$arItem[0]] );
    }
    break;

// monta o span com a listagem dos dados dos itens que são perecíveis
case 'montaListaItensPerecivel':

    // valida os dados pereciveis do item, caso retorne algo é porque há algum erro para ser exibido na tela
    $retorno = validaListaPerecivel();

    // se não tiver erro entra e monta os arrays, caso contrario mostra o aviso na tela
    if ($retorno == "") {

        $arItensPerecivel = Sessao::read('arItensPerecivel');

        //verifica se existe algum valor no hidden, caso tenha, é porque está sendo alterado algum valor da listagem.
        if ($_REQUEST['inNumLinhaListaPerecivel'] != '') {
            $inCont = $_REQUEST['inNumLinhaListaPerecivel'];

        } else {
            $inCont = count($arItensPerecivel[$_REQUEST['inCodItem']]);
            $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['inNumLinhaListaPerecivel'] = $inCont;
            Sessao::write('arItensPerecivel', $arItensPerecivel);
        }

        // pega o valor para poder usar na alteração do item
        // pois usaria esse valor para diminuir da quantidade de entrada
        $inQtdePerecivelAnterior = $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['inQtdePerecivel'];
        $inQtdePerecivelAnterior = $inQtdePerecivelAnterior == '' ? 0 : $inQtdePerecivelAnterior;

        // atribui os valores dos campos do transf3 para a montagem da listagem
        $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['inNumLotePerecivel'] = $_REQUEST['inNumLotePerecivel'];
        $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['dtFabricacaoPerecivel'] = $_REQUEST['dtFabricacaoPerecivel'];
        $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['dtValidadePerecivel'] = $_REQUEST['dtValidadePerecivel'];
        $arItensPerecivel[$_REQUEST['inCodItem']][$inCont]['inQtdePerecivel'] = $_REQUEST['inQtdePerecivel'];

        Sessao::write('arItensPerecivel', $arItensPerecivel);
        // é acrescido a quantidade de entrada
        $inQtdeEntrada = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeEntrada']));
        $inQtdePerecivel = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdePerecivel']));
        $inQtdePerecivelAnterior = str_replace(",", ".", str_replace(".", "", $inQtdePerecivelAnterior ));

        $valor = $inQtdeEntrada + $inQtdePerecivel - $inQtdePerecivelAnterior;
        $valor = number_format($valor, 4, ",", ".");
        $stJs .=  "$('inQtdeEntrada').value = '".$valor."';";
        $stJs .=  "$('inNumLinhaListaPerecivel').value = '';";
        $stJs .=  "jQuery('#acaoPerecivel').val('incluir');";

        // remonta a lista perecivel do item
        $stJs .= montaListaItensPerecivel( $arItensPerecivel[$_REQUEST['inCodItem']] );

        // limpa os campos perecivel do item
        $stJs .= limpaDadosPerecivel();

    } else {
        $stJs .= $retorno;
    }

    # Habilita o botão de incluir/alterar.
    $stJs .= "jQuery('#Incluir').attr('disabled', '');";

    break;

// limpa os dados dos campos específicos dos itens perecível
case 'limpaDadosPerecivel':
    $stJs = limpaDadosPerecivel();
    break;

// exclui a linha da listagem dos itens
case 'excluirPerecivel':

    $arItensPerecivel = Sessao::read('arItensPerecivel');
    // é decrescido a quantidade de entrada
    $inQtdePerecivel = $arItensPerecivel[$_REQUEST['inCodItem']][$_REQUEST['inNumLinhaListaPerecivel']]['inQtdePerecivel'];

    $inQtdeEntrada = str_replace(",", ".", str_replace(".", "", $_REQUEST['inQtdeEntrada']));
    $valor = $inQtdeEntrada - number_format( $inQtdePerecivel, 4, ".", "," );
    $valor = number_format( $valor, 4, ",", "." );

    $stJs .=  "$('inQtdeEntrada').value = '".$valor."';";

    $arTemp = array();
    $inCont = 0;

    // remonta os itens na lista
    foreach ($arItensPerecivel[$_REQUEST['inCodItem']] as $chave => $valor) {
        if ($chave != $_REQUEST['inNumLinhaListaPerecivel']) {
            $arTemp[$inCont]['inNumLotePerecivel'] = $valor['inNumLotePerecivel'];
            $arTemp[$inCont]['dtValidadePerecivel'] = $valor['dtValidadePerecivel'];
            $arTemp[$inCont]['dtFabricacaoPerecivel'] = $valor['dtFabricacaoPerecivel'];
            $arTemp[$inCont]['inQtdePerecivel'] = $valor['inQtdePerecivel'];
            $arTemp[$inCont]['inNumLinhaListaPerecivel'] = $inCont;
            $inCont++;
        }
    }
    $arItensPerecivel[$_REQUEST['inCodItem']] = $arTemp;
    Sessao::write('arItensPerecivel' , $arItensPerecivel);

    // caso tenha algum item na listagem, mostra a lista, caso contrario limpa o span
    if ( count($arItensPerecivel[$_REQUEST['inCodItem']]) > 0 ) {
        $stJs .= montaListaItensPerecivel( $arItensPerecivel[$_REQUEST['inCodItem']] );
    } else {
        $stJs .= "$('spnItensPereciveis').innerHTML = '';";
    }
    break;

// monta a listagens dos itens de entrada
case 'incluirItemEntrada':

    $arInfoChecks = explode("_", $_REQUEST['inCheckBoxId']);
    $inCheckBoxId = $arInfoChecks[1];

    Sessao::write('irProximoItem',$_REQUEST['inProxItem']);

    $retorno = validaListaEntrada();
    if ($retorno == '') {

        // monta a lista de atributo e verifica os campos
        if ( Sessao::read('boAtributo') ) {
            require_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php");
            $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
            $obTAtributoCatalogoItem->recuperaAtributoCatalogoItem( $rsAtributos, ' AND cod_item = '.$_REQUEST['inCodItem'] );

            $inCont = 0;
            $boSair = false;
            // faz a montagem do array dos atributos do item. Caso algum dos atributos não nulos estejam em branco,
            // para o processo e acusa o erro na tela

            $arItensAtributo = Sessao::read('arItensAtributo');

            while ((!$rsAtributos->eof()) && (!$boSair)) {
                if (($_REQUEST['Atributos_'.$rsAtributos->getCampo('cod_atributo').'_2'] != '' ) && ( $rsAtributos->getCampo('nao_nulo') == 't')) {
                    $arItensAtributo[$_REQUEST['inCodItem']][$inCont]['stValor'] = $_REQUEST['Atributos_'.$rsAtributos->getCampo('cod_atributo').'_2'];
                    $arItensAtributo[$_REQUEST['inCodItem']][$inCont]['inCodAtributo'] = $rsAtributos->getCampo('cod_atributo');
                    $arItensAtributo[$_REQUEST['inCodItem']][$inCont]['inCodModulo']   = $rsAtributos->getCampo('cod_modulo');
                    $arItensAtributo[$_REQUEST['inCodItem']][$inCont]['inCodCadastro'] = $rsAtributos->getCampo('cod_cadastro');
                    $inCont++;
                    $rsAtributos->proximo();

                } else {
                    $stMensagem = 'O atributo '.$rsAtributos->getCampo('nom_atributo').' deve ser preenchido.';
                    $retorno = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

                    // destroi a criação do array para que não fique nada dela em sessão, caso não seja alteração do item, verificado
                    // pela variavel 'inNumLinhaEntrada' que só recebe valor caso esteja alterando os dados de determinado item
                    if ( !isset($_REQUEST['inNumLinhaEntrada']) )
                        unset($arItensAtributo[$_REQUEST['inCodItem']]);
                    $boSair = true;
                }
            }
            Sessao::write('arItensAtributo', $arItensAtributo);
        }

        // caso não tenha encontrado nenhum erro na crição do array dos atributos, vai para a criação do array dos itens de entrada
        if ($retorno == "") {

            $alteracao = false;

            require_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php" );
            $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();
            $obTAlmoxarifadoCentroCusto->setDado( "cod_centro",  $_REQUEST['inCodCentroCusto']);
            $obTAlmoxarifadoCentroCusto->recuperaPorChave( $rsCentroCusto );

            $arItensEntrada = Sessao::read('arItensEntrada');

            foreach ($arItensEntrada as $chave => $dados) {
                if ($_REQUEST['inCodItem'] == $dados['inCodItem']) {
                    unset($arItensEntrada[$chave]);
                    $alteracao = true;
                    $indiceItemAlterado = $chave;
                }
            }

            if ($alteracao == false) {
                if ( is_array($arItensEntrada) ) {
                    $inCont = count($arItensEntrada);
                } else {
                    $inCont = isset($_REQUEST['inNumLinhaEntrada']) ? $_REQUEST['inNumLinhaEntrada'] : 0;
                }
            } else {
                $inCont = $indiceItemAlterado;
            }

            $arItensEntrada[$inCont]['inCodItem'          ] = $_REQUEST['inCodItem'          ];
            $arItensEntrada[$inCont]['stItem'             ] = $_REQUEST['stItem'             ];
            $arItensEntrada[$inCont]['inCodFornecedor'    ] = $_REQUEST['inCodFornecedor'    ];
            $arItensEntrada[$inCont]['stDtOrdem'          ] = $_REQUEST['stDtOrdem'          ];
            $arItensEntrada[$inCont]['inCodEntidade'      ] = $_REQUEST['inCodEntidade'      ];
            $arItensEntrada[$inCont]['inOrdemCompra'      ] = $_REQUEST['inOrdemCompra'      ];
            $arItensEntrada[$inCont]['stExercicio'        ] = $_REQUEST['stExercicio'        ];
            $arItensEntrada[$inCont]['inCodAlmoxarifado'  ] = $_REQUEST['inCodAlmoxarifado'  ];
            $arItensEntrada[$inCont]['inCodCentroCusto'   ] = $_REQUEST['inCodCentroCusto'   ];
            $arItensEntrada[$inCont]['stUnidadeMedida'    ] = $_REQUEST['stUnidadeMedida'    ];
            $arItensEntrada[$inCont]['inCodMarca'         ] = $_REQUEST['inCodMarca'         ];
            $arItensEntrada[$inCont]['stMarca'            ] = $_REQUEST['stMarca'            ];
            $arItensEntrada[$inCont]['inQtdeEntrada'      ] = $_REQUEST['inQtdeEntrada'      ];
            $arItensEntrada[$inCont]['flValorTotalMercado'] = $_REQUEST['flValorTotalMercado'];

            //Informações de Bem Patrimonial
            $arItensEntrada[$inCont]['stPlacaIdentificacao'] = $_REQUEST['stPlacaIdentificacao'];
            $arItensEntrada[$inCont]['inCodSituacao'] = $_REQUEST['inCodSituacao'];
            $arItensEntrada[$inCont]['stNumeroPlaca'] = $_REQUEST['stNumeroPlaca'];
            $arItensEntrada[$inCont]['inCodTipoItem'] = $_REQUEST['inCodTipoItem'];

            // Utilizado para multiplicar e gerar os totais.
            $qtdItemFormatado = str_replace('.','',$_REQUEST['inQtdeEntrada']);
            $qtdItemFormatado = str_replace(',','.',$qtdItemFormatado);
            $inQtdeItem     = number_format($qtdItemFormatado, 2, ".", "");

            $vlUnitarioItem = str_replace('.','',$_REQUEST['flValorTotalMercado']);
            $vlUnitarioItem = str_replace(",", ".",$vlUnitarioItem );
            $vlUnitarioItem = number_format($vlUnitarioItem, 2, ".", "");

            $vlTotalItem    = number_format(($vlUnitarioItem * $inQtdeItem), 2, ",", ".");

            $arItensEntrada[$inCont]['vlTotalItem'        ] = $vlTotalItem;
            $arItensEntrada[$inCont]['stComplemento'      ] = $_REQUEST['stComplemento'      ];
            $arItensEntrada[$inCont]['inNumItem'          ] = $_REQUEST['inNumItem'          ];
            $arItensEntrada[$inCont]['stCentroCusto'      ] = $rsCentroCusto->getCampo( 'descricao' );
            $arItensEntrada[$inCont]['inCheckBoxId'       ] = $inCheckBoxId;

            if ($alteracao == false) {
                // adiciona ao item inserido a posição na lista, para poder fazer a alteração dos dados posteriormente
                $arItemLinha = Sessao::read('arItemLinha');
                Sessao::remove('arItemLinha');
                $arItemLinha[$_REQUEST['inCodItem']] = $inCont;
                Sessao::write('arItemLinha', $arItemLinha);
            }

            $arItensEntradaC = $arItensEntrada;
            Sessao::write('arItensEntrada', $arItensEntrada);

            if ( Sessao::read('boPerecivel') ) {
                $stJs .= limpaDadosPerecivel();
                $stJs .= "\n $('spnItensPereciveis').innerHTML = '';";
            }

            $stJs .= limpaDadosEntrada();

            // monta a lista de itens de entrada
            $stJs .= montaListaItensEntrada( $arItensEntradaC );

            $stJs .= "$('item_".$inCheckBoxId."').checked = false;";

            $inNumeroTotalItens = count(Sessao::read('arItens'));

            $inProxItem = $inCheckBoxId+1;
            if ( ($_REQUEST['inProxItem'] == '1') && ($inNumeroTotalItens >= $inProxItem)) {
                $stJs .= "$('item_".$inProxItem."').click();";
            }

        } else {
            $stJs .= $retorno;
        }

    } else {
        $stJs .= $retorno;
    }

    break;

case 'limparDadosItens':

    $obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife();

    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( "cod_ordem", Sessao::read('inOrdemCompra') );
    $obTComprasOrdemCompra->setDado( "tipo"     , "C" );
    $obTComprasOrdemCompra->recuperaMarcaPorOrdemCompra( $rsOrdemCompraMarca );

    $stJs .= "frm.inCodAlmoxarifado.value = '".$obSelectAlmoxarifado->getCodAlmoxarifado()."';";
    $stJs .= "$('inCodCentroCusto').value = '';";
    $stJs .= "$('inCodMarca').value = '".$rsOrdemCompraMarca->getCampo('cod_marca')."';";
    $stJs .= "$('stMarca').innerHTML = '".$rsOrdemCompraMarca->getCampo('marca')."';";
    $stJs .= "$('inQtdeEntrada').value = '".number_format(0, 4, ",", ".")."';";
    $stJs .= "$('flValorTotalMercado').value = '';";
    $stJs .= "$('stComplemento').value = '';";
    $stJs .= "if($('stNumeroPlaca'))$('stNumeroPlaca').value = '';";
    $stJs .= "if($('inCodSituacao'))$('inCodSituacao').value = '';";
    $stJs .= "if($('inCodTxtSituacao'))$('inCodTxtSituacao').value = '';";
    $stJs .= "if($('spnDetalheItem'))$('spnDetalheItem').innerHTML = '&nbsp;';";
    break;

case 'montaPlacaIdentificacao':

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $montaPlaca = true;
        } else {
            $montaPlaca = false;
        }

        $stJs = montaPlacaIdentificacao('',$montaPlaca);
    break;

case 'verificaIntervalo' :

        if ($_REQUEST['stNumeroPlaca'] != '' AND $_REQUEST['nuQuantidade'] != '') {
            $arNumPlaca = array();
            $numeroPlaca = $_REQUEST['stNumeroPlaca'];
            // monta um array com os números das placas possíveis de acordo com a
            // quantidade informada
            for ($i=0; $i < $_REQUEST['nuQuantidade']; $i++) $arNumPlaca[] = "'".($numeroPlaca++)."'";

            if ($_REQUEST['nuQuantidade'] != '' && $_REQUEST['nuQuantidade'] > 0) {
                $stFiltro = " WHERE num_placa IN (".implode("," ,$arNumPlaca).")";
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );

                if ( $rsBem->getNumLinhas() >= 0 ) {

                    $inQuantidade = str_replace('.','', $_REQUEST['nuQuantidade']);
                    $inQuantidade = str_replace(',','.', $inQuantidade  );

                    $inQuantidade = (int) $inQuantidade;

                    $intervalo = ($inQuantidade) + $_REQUEST['stNumeroPlaca'];

                    $stJs.= "alertaAviso('Já existem bens com placas no intervalo selecionado (".$_REQUEST['stNumeroPlaca']." - ".$intervalo.")!','form','erro','".Sessao::getId()."');";
                }
            }
        }
    break;

case 'excluirEntrada':

    // contador para a lista nova
    $inCont = 0;

    // copia o array da lista de entrada para um array novo
    $arItensEntrada = array();
    $arItensEntradaNovo = array();
    $arItensEntrada = Sessao::read('arItensEntrada');

    $inCodItem = $_REQUEST['inCodItem'];

    // limpa o array para remontá-lo sem o item excluido
    Sessao::remove('arItensEntrada');

    // reordena a o array da lista de entrada, agora sem o item excluído
    foreach ($arItensEntrada as $chave => $valor) {
        // Caso o item não for o excluido, adiciona no array dos itens de entrada.
        if ($valor['inCodItem'] != $inCodItem) {
            $arItensEntradaNovo[$inCont]['inCodItem'          ] =  $valor['inCodItem'          ];
            $arItensEntradaNovo[$inCont]['stItem'             ] =  $valor['stItem'             ];
            $arItensEntradaNovo[$inCont]['inCodFornecedor'    ] =  $valor['inCodFornecedor'    ];
            $arItensEntradaNovo[$inCont]['stDtOrdem'          ] =  $valor['stDtOrdem'          ];
            $arItensEntradaNovo[$inCont]['inCodEntidade'      ] =  $valor['inCodEntidade'      ];
            $arItensEntradaNovo[$inCont]['inOrdemCompra'      ] =  $valor['inOrdemCompra'      ];
            $arItensEntradaNovo[$inCont]['stExercicio'        ] =  $valor['stExercicio'        ];
            $arItensEntradaNovo[$inCont]['inCodAlmoxarifado'  ] =  $valor['inCodAlmoxarifado'  ];
            $arItensEntradaNovo[$inCont]['inCodCentroCusto'   ] =  $valor['inCodCentroCusto'   ];
            $arItensEntradaNovo[$inCont]['stUnidadeMedida'    ] =  $valor['stUnidadeMedida'    ];
            $arItensEntradaNovo[$inCont]['inCodMarca'         ] =  $valor['inCodMarca'         ];
            $arItensEntradaNovo[$inCont]['stMarca'            ] =  $valor['stMarca'            ];
            $arItensEntradaNovo[$inCont]['inQtdeEntrada'      ] =  $valor['inQtdeEntrada'      ];
            $arItensEntradaNovo[$inCont]['flValorTotalMercado'] =  $valor['flValorTotalMercado'];
            $arItensEntradaNovo[$inCont]['stComplemento'      ] =  $valor['stComplemento'      ];
            $arItensEntradaNovo[$inCont]['inNumItem'          ] =  $valor['inNumItem'          ];
            $arItensEntradaNovo[$inCont]['vlTotalItem'        ] =  $valor['vlTotalItem'        ];
            $arItensEntradaNovo[$inCont]['stCentroCusto'      ] =  $valor['stCentroCusto'      ];
            $arItensEntradaNovo[$inCont]['inCheckBoxId'       ] =  $valor['inCheckBoxId'      ];

            //Informações de Bem Patrimonial
            $arItensEntrada[$inCont]['stPlacaIdentificacao'] = $valor['stPlacaIdentificacao'];
            $arItensEntrada[$inCont]['inCodSituacao'] = $valor['inCodSituacao'];
            $arItensEntrada[$inCont]['stNumeroPlaca'] = $valor['stNumeroPlaca'];
            $arItensEntrada[$inCont]['inCodTipoItem'] = $valor['inCodTipoItem'];

            $inCont++;
        }
    }

    Sessao::write('arItensEntrada', $arItensEntradaNovo);

    $arItensPerecivel = Sessao::read('arItensPerecivel');
    $arItensAtributo  = Sessao::read('arItensAtributo');
    $arItemLinha	  = Sessao::read('arItemLinha');

    // destroi as listas relacionadas a esse item que está sendo excluído
    unset( $arItensPerecivel[$_REQUEST['inCodItem']] );
    unset( $arItensAtributo[$_REQUEST['inCodItem']] );
    unset( $arItemLinha[$_REQUEST['inCodItem']] );

    $inContItens = 0;
    foreach ($arItemLinha as $chave =>$dados) {
        $arItemLinha[$chave] = $inContItens;
        $inContItens++;
    }

    Sessao::write('arItensPerecivel', $arItensPerecivel);
    Sessao::write('arItensAtributo', $arItensAtributo);
    Sessao::write('arItemLinha', $arItemLinha);

    // se a lista não estiver vazia, monta a lista novamenete, caso contrario limpa o span
    if (count($arItensEntradaNovo) > 0) {
        $stJs .= montaListaItensEntrada( $arItensEntradaNovo );
        $stJs .= "$('spnDetalheItem').innerHTML = '';";
    } else {
        $stJs .= "$('spnItensEntrada').innerHTML = '';";
    }

    // destroi a variável que não é mais útil
    unset( $arItensEntrada );
    unset($arItensEntradaNovo);

    break;
}

echo $stJs;
?>
