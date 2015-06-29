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
  * Página de processamento para calculo
  * Data de criação : 02/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

* $Id: PREmitirCarneLancManual.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.21  2007/05/17 20:31:32  cercato
colocando exit para terminar lancamento manual.

Revision 1.20  2007/05/17 13:20:51  cercato
colocando exit para terminar lancamento manual.

Revision 1.19  2007/04/16 18:05:52  cercato
Bug #9132#

Revision 1.18  2007/02/23 20:26:21  cercato
alteracao para o grupo 6 exibir capa do iss estimativa

Revision 1.17  2007/02/15 16:58:55  dibueno
Alteração na ordem dos eventos.
Primeiro, chama a tela de download do PDF.
Depois, chama os procedimentos de finalização.

Revision 1.16  2007/01/26 17:00:19  fabio
atualizado para versao de MATA DE SÃO JOÃO

Revision 1.14  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
//sistemaLegado::mostravar( $_REQUEST );exit;

if (!$obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento) {
    $obCarne = $obRARRCarne;
} else {
    $obCarne = $obRARRLancamento->obRARRCarne;
}

$obCarne->listarEmissaoCarne( $rsEmissaoCarne );
//echo '<hr>'.$obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento;
//sistemaLegado::mostravar( $rsEmissaoCarne ); //exit;
if ( $rsEmissaoCarne->getNumLinhas() > 0 ) {
    $boExec = TRUE;
    $arEmissao = array();

    $arArqMod = explode( "§", $_REQUEST["stArquivo"] );
    $stArquivoModelo = $arArqMod[0];
    $inCodModelo = $arArqMod[1];
    if (!$stArquivoModelo) {
        sistemaLegado::exibeAviso("Nenhum modelo de carne foi configurado para a impressao.", "n_erro", "erro");
        exit;
    }

    $rsEmissaoCarne->setPrimeiroElemento();
    $stIdVinculo = $rsEmissaoCarne->getCampo('id_vinculo');
    $inCodGrupo = $rsEmissaoCarne->getCampo('cod_grupo');
    #echo 'COD GRUPOI: '.$inCodGrupo; exit;
    while ( !$rsEmissaoCarne->eof() ) {
        $arEmissao[$rsEmissaoCarne->getCampo('cod_lancamento')][] = array(
            "cod_parcela" => $rsEmissaoCarne->getCampo('cod_parcela'),
            "exercicio"   => $rsEmissaoCarne->getCampo('exercicio'),
            "inscricao"   => $rsEmissaoCarne->getCampo('inscricao'),
            "numeracao"   => $rsEmissaoCarne->getCampo('numeracao'),
            "numcgm"      => $rsEmissaoCarne->getCampo('numcgm'),
            "cod_modelo"  => $inCodModelo
        );

        $rsEmissaoCarne->proximo();
    }

    /**
    *   grava nome pdf e parametro para salvar em disco
    *   usado tambem no objeto pdf
    */

    Sessao::write( "stNomPdf", ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf" );
    Sessao::write( "stParamPdf", "F" );

    include_once( CAM_GT_ARR_NEGOCIO . "RARRConfiguracao.class.php");
    $obRARRConfiguracao = new RARRConfiguracao;
    $obRARRConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRARRConfiguracao->consultar();

    $arTemp = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
    $arTemp = explode( "/", $arTemp );
    $inCodEscrituracao = $arTemp[0];

    $arTemp = $obRARRConfiguracao->getCodigoGrupoCreditoITBI();
    $arTemp = explode( "/", $arTemp );
    $inCodLancamentoITBI = $arTemp[0];

    $arTmp = explode( ".", $stArquivoModelo );
    $stObjModelo = $arTmp[0];
    include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

    $obRModeloCarne = new $stObjModelo( $arEmissao );
    $obRModeloCarne->imprimirCarne();

    if ($boExec) {
        echo "<script type=\"text/javascript\">\r\n";
        echo "    var sAux = window.open('".CAM_GT_ARR_INSTANCIAS."documentos/OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
        echo "    eval(sAux)\r\n";
        echo "</script>\r\n";
    }

    //include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");

    include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
    $rsEmissaoCarne->setPrimeiroElemento();
    while ( !$rsEmissaoCarne->eof() ) {
        $obTARRCarne = new TARRCarne;
        $obTARRCarne->setDado ( "numeracao"     , $rsEmissaoCarne->getCampo('numeracao')        );
        $obTARRCarne->setDado ( "cod_convenio"  , $rsEmissaoCarne->getCampo('cod_convenio')     );
        $obTARRCarne->setDado ( "cod_parcela"   , $rsEmissaoCarne->getCampo('cod_parcela')      );
        $obTARRCarne->setDado ( "exercicio"     , $rsEmissaoCarne->getCampo('exercicio')        );
        $obTARRCarne->setDado ( "impresso"      , TRUE                                          );
        $obErro = $obTARRCarne->alteracao();

        $rsEmissaoCarne->proximo();
    }

    if ($obCarne->obRARRParcela->roRARRLancamento->inCodLancamento) {
        $pgFormRelatorioExecucaoLancamento = "FMRelatorioExecucaoLancamento.php";
        $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&inCodCredito=".$_REQUEST["inCodCredito"];
        if ($_REQUEST["inCodGrupo"]) {
            SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", $sessao->id, "../");
        } else {
            SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$_REQUEST["inCodCredito"],"incluir","aviso", $sessao->id, "../");
        }
    }

    if (!$_REQUEST["stRelatorio"]) {
        if (!$obErro->ocorreu() ) {
            if ($_REQUEST['FormLancamentoManual']) {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir&boTipoLancamentoManual=".$_REQUEST['FormLancamentoManual'], $descricao ,"incluir","aviso", Sessao::getId(), "../" );

            } else {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir","Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", Sessao::getId(), "../" );
            }
        } else {
            SistemaLegado::alertaAviso($pgForm."?stAcao=emitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(),"../");
        }
    }
//exit;
} else {
    if ($obCarne->obRARRParcela->roRARRLancamento->inCodLancamento) {
        $pgFormRelatorioExecucaoLancamento = "FMRelatorioExecucaoLancamento.php";
        $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&inCodCredito=".$_REQUEST["inCodCredito"];
        if ($_REQUEST["inCodGrupo"]) {
            SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", $sessao->id, "../");
        } else {
            SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$_REQUEST["inCodCredito"],"incluir","aviso", $sessao->id, "../");
        }
    } else {
        $stPag = $pgFormRelatorioExecucao."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"];
        $stPag .= "&inCodGrupo=".$_REQUEST["inCodGrupo"];
        $stPag .= "&inInscricaoImobiliariaInicial=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoInicial;
        $stPag .= "&inInscricaoImobiliariaFinal=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoFinal;
        $stPag .= "&inCodContribuinteInicial=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial;
        $stPag .= "&inCodContribuinteFinal=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal;
        $stPag .= "&inNumInscricaoEconomicaInicial=".$obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->getInscricaoEconomicaInicial();
        $stPag .= "&inNumInscricaoEconomicaFinal=".$obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->getInscricaoEconomicaFinal();

        SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", $sessao->id, "../");
    }
}
