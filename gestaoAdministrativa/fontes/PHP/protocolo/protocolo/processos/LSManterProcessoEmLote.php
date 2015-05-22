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
    * Página de Listagem para Arquivar Processo em Lote.
    * Data de Criação: 23/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: LSManterProcessoEmLote.php 62418 2015-05-06 17:45:05Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoEmLote";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$arFiltro = Sessao::read("filtro");
if (count($arFiltro) > 0) {
    $_REQUEST = $arFiltro;
} else {
    foreach ($_REQUEST as $stChave => $stValor) {
        $arFiltro[$stChave] = $stValor;
    }
}

Sessao::write("filtro", $arFiltro);

//FILTROS
$inCodProcesso		= $_REQUEST['inCodProcesso'];
$codClassifAssunto	= $_REQUEST['codClassifAssunto'];
$codClassificacao	= $_REQUEST['codClassificacao'];
$codAssunto			= $_REQUEST['codAssunto'];
$stAssuntoReduzido	= $_REQUEST['stAssuntoReduzido'];
$numCgm				= $_REQUEST['numCgm'];
$HdnnumCgm			= $_REQUEST['HdnnumCgm'];
$stNomCGM			= $_REQUEST['stNomCGM'];
$dataInicio			= $_REQUEST['dataInicio'];
$dataTermino		= $_REQUEST['dataTermino'];
$stOrdenacao		= $_REQUEST['stOrdenacao'];

//VERIFICACAO DOS FILTROS UTILIZADOS
if ($inCodProcesso) {
    $inCodProcesso = preg_split( "/[^a-zA-Z0-9]/", $inCodProcesso);
    $stFiltro .= " AND SW_PROCESSO.cod_processo  = ".(int) $inCodProcesso[0];
    $stFiltro .= " AND SW_PROCESSO.ano_exercicio = '".$inCodProcesso[1]."' ";
}

if ($codClassificacao) {
    $stFiltro .= " AND SW_CLASSIFICACAO.cod_classificacao = ".$codClassificacao."\n";
}

if ($codAssunto) {
    $stFiltro .= " AND SW_ASSUNTO.cod_assunto = ".$codAssunto."\n";
}

if ($stAssuntoReduzido) {
    $stFiltro .= " AND SW_PROCESSO.resumo_assunto ILIKE ('%".$stAssuntoReduzido."%') \n";
}

if ($numCgm) {
    $stFiltro .= " AND SW_CGM.numcgm = ".$numCgm;
}

if (!empty($dataInicio) && !empty($dataTermino)) {
    $arrData     = explode("/", $dataInicio);
    $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
    $arrData     = explode("/", $dataTermino);
    $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
    $stFiltro .= " AND substr((sw_processo.timestamp::varchar),1,10) >= '".$dataInicio."'";
    $stFiltro .= " AND substr((sw_processo.timestamp::varchar),1,10) <= '".$dataTermino."'";
    $vet["dataInicio"]  = $dataInicio;
    $vet["dataTermino"] = $dataTermino;
}

if ($_REQUEST['valorAtributoTxt']) {
    foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
        if ($_REQUEST['valorAtributoTxt'][$key]) {
            $stFiltro .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
            $stFiltro .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST['valorAtributoNum']) {
    foreach ($_REQUEST['valorAtributoNum'] as $key => $value) {
        if ($_REQUEST['valorAtributoNum'][$key]) {
            $stFiltro .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoNum'][$key]."' \n";
            $stFiltro .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST['valorAtributoCmb']) {
    foreach ($_REQUEST['valorAtributoCmb'] as $key => $value) {
        if ($_REQUEST['valorAtributoCmb'][$key]) {
            $stFiltro .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoCmb'][$key]."' \n";
            $stFiltro .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
        }
    }
}

?>

<script language="javascript">

function marcarTodos(componente)
{
    var i = 0;
    for (i = 0; i < document.frm.elements.length; i++) {
        if (document.frm.elements[i].type == "checkbox") {
            document.frm.elements[i].checked = componente.checked;
        }
    }
}

function limpar()
{
    document.frm.stTipo.value = '';
    document.frm.stHistorico.value = '';
    document.frm.txtComplementar.value = '';
    document.frm.chkMarcarTodos.checked = false;
    var i = 0;
    for (i = 0; i < document.frm.elements.length; i++) {
        if (document.frm.elements[i].type == "checkbox") {
            document.frm.elements[i].checked = false;
        }
    }
}

</script>

<?php

//CONSULTA PROCESSOS
$obTProcesso = new TProcesso();
$obTProcesso->recuperaProcessoAlteracao($rsProcessos, $stFiltro, $stOrdem, "");

//CONSULTA TIPOS DE PROCESSOS
$obTProcesso->recuperaSituacaoArquivamentoProcesso($rsSituacaoProcesso, "", "", "");

//CONSULTA TIPOS DE ARQUIVAMENTO
$obTProcesso->recuperaHistoricoArquivamentoProcesso($rsHistorico, "", "", "");

//DEFINICAO DO FORM
$obForm = new Form();
$obForm->setAction    ( $pgProc );
$obForm->setTarget    ( "oculto" );

//GERA LISTA
$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Listagem de Processos" );
//Preenche recordSet
$obLista->setRecordSet( $rsProcessos );
//Cabeçalho
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Interessado");
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Classificação");
$obLista->ultimoCabecalho->setWidth( 21 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Assunto");
$obLista->ultimoCabecalho->setWidth( 22 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
//Campos
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_processo]"."/"."[ano_exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_classificacao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_assunto" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
/* Combo para Selecionar os Tipos */
$obChkArquivar = new CheckBox;
$obChkArquivar->setName       ( "boArquivar" );
$obChkArquivar->setValue	  ( "[cod_processo]"."/"."[ano_exercicio]" );
/**/
$obLista->addDadoComponente   ( $obChkArquivar    );
$obLista->ultimoDado->setCampo( ""  );
$obLista->commitDadoComponente();
$obLista->montaHTML();

$stHTML = $obLista->getHTML();
$stHTML = str_replace( "\n" ,"" ,$stHTML );
$stHTML = str_replace( "  " ,"" ,$stHTML );
$stHTML = str_replace( "'","\\'",$stHTML );
$stHTML = str_replace( "\\\'","\\'",$stHTML );

//Define objeto span com os itens selecionados
$obSpanLancamentos = new Span;
$obSpanLancamentos->setId( "spnLancamentos" );
$obSpanLancamentos->setValue( $stHTML );

$stEval = "
            var valida = false;
            for (i=0 ; i<document.frm.elements.length ; i++) {
                if (document.frm.elements[i].type == 'checkbox') {
                    if (document.frm.elements[i].checked == true) {
                        valida = true;
                    }
                }
            }
            if (valida == false) {
                mensagem += '@Selecione ao menos um processo!';
                erro = true;
            }";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval  );

//Ordenacao da listagem
$obCmbTipoArquivamento = new Select;
$obCmbTipoArquivamento->setName         ( "stTipo"                      		);
$obCmbTipoArquivamento->setValue        ( $stTipo                   			);
$obCmbTipoArquivamento->setNull			( false									);
$obCmbTipoArquivamento->setRotulo       ( "Arquivamento"                        );
$obCmbTipoArquivamento->setTitle        ( "Selecione a forma de arquivamento" 	);
$obCmbTipoArquivamento->addOption       ( "", "Selecione"    			        );
$obCmbTipoArquivamento->setCampoId		( "cod_situacao"						);
$obCmbTipoArquivamento->setCampoDesc	( "nom_situacao" 						);
$obCmbTipoArquivamento->preencheCombo	( $rsSituacaoProcesso					);

$obCmbTipoHistorico = new Select;
$obCmbTipoHistorico->setName            ( "stHistorico"                      	  );
$obCmbTipoHistorico->setValue           ( $stHistorico                   		  );
$obCmbTipoHistorico->setNull			( false									  );
$obCmbTipoHistorico->setRotulo          ( "Motivo do Arquivamento"                );
$obCmbTipoHistorico->setTitle           ( "Selecione o Motivo do arquivamento"    );
$obCmbTipoHistorico->addOption          ( "", "Selecione"    			          );
$obCmbTipoHistorico->setCampoId		    ( "cod_historico"						  );
$obCmbTipoHistorico->setCampoDesc	    ( "nom_historico" 						  );
$obCmbTipoHistorico->preencheCombo	    ( $rsHistorico					 		  );

$obTxtLocalizacaoFisica = new TextBox();
$obTxtLocalizacaoFisica->setId('stLocalizacaoFisica');
$obTxtLocalizacaoFisica->setName('stLocalizacaoFisica');
$obTxtLocalizacaoFisica->setRotulo('Localização Física do Arquivamento');
$obTxtLocalizacaoFisica->setSize(80);
$obTxtLocalizacaoFisica->setMaxLength(80);

$obChkMarcarTodos = new CheckBox;
$obChkMarcarTodos->setName				( "chkMarcarTodos"						   );
$obChkMarcarTodos->setValue				( $chkMarcarTodos						   );
$obChkMarcarTodos->setRotulo			( "Marcar Todos"                 		   );
$obChkMarcarTodos->setTitle             ( "Marcar ou desmarcar todos os processos" );
$obChkMarcarTodos->setChecked			( false									   );
$obChkMarcarTodos->obEvento->setOnChange( "marcarTodos(this)"                      );

$obTxtComplementar = new TextArea;
$obTxtComplementar->setName				( "txtComplementar"						);
$obTxtComplementar->setValue			( $txtComplementar  					);
$obTxtComplementar->setNull				( true 									);
$obTxtComplementar->setRotulo			( "Texto Complementar"					);

//ADICIONANDO OS COMPONENTES AO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm		 ( $obForm				  );
$obFormulario->addSpan   	 ( $obSpanLancamentos 	  );
$obFormulario->addHidden     ( $obHdnEval, true        );
$obFormulario->addComponente ( $obChkMarcarTodos		  );
$obFormulario->addComponente ( $obCmbTipoArquivamento  );
$obFormulario->addComponente ( $obCmbTipoHistorico	  );
$obFormulario->addComponente ( $obTxtLocalizacaoFisica );
$obFormulario->addComponente ( $obTxtComplementar	  );

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->obEvento->setOnClick( "limpar();" );

$obFormulario->defineBarra( array($obBtnOk, $obBtnLimpar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
