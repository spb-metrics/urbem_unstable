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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 13/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: zank $
    $Date: 2007-06-20 15:01:37 -0300 (Qua, 20 Jun 2007) $

    * Casos de uso: uc-02.02.02,uc-02.04.09
*/

/*
$Log$
Revision 1.20  2007/06/20 18:01:37  bruce
Bug#9101#

Revision 1.19  2007/05/29 14:13:39  domluc
Correção na Msg de Erro

Revision 1.18  2007/05/09 01:33:41  diego
Bug #8914#

Revision 1.17  2006/09/19 09:00:01  jose.eduardo
Bug #6993#

Revision 1.16  2006/07/05 20:51:41  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");

$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;

switch ($_GET['stCtrl']) {

    case 'buscaPopup':

 if ($_POST[$_GET['stNomCampoCod']] != "") {

        if ($_GET['stTipoBusca'] == "banco") {
             $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

             $obRContabilidadePlanoContaBanco->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
             $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
             if ($_REQUEST['inCodEntidade']) {
                $obRContabilidadePlanoContaBanco->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
             }
             $obErro = $obRContabilidadePlanoContaBanco->consultar();

             $codAgencia =     $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
             if ($codAgencia <> "") {
                if($_REQUEST['inCodEntidade'] AND ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade() <> $_REQUEST['inCodEntidade']))
                    SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
                else
                    $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
             } else {
                 SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");
             }

        } elseif ($_GET['stTipoBusca'] == 'tes_pagamento') {

            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n(( pb.cod_banco is not null AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  ";
                $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.4.%' ))  ";
                $stFiltro .= "\n   ) AND ";
            } else {
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' )  ";
                $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
            $stFiltro .= "\n   pa.cod_plano = " .$_POST[$_GET['stNomCampoCod']] . " AND ";

            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }

            $stFiltro = " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4);
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';

            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

            # Quando não encontra nenhum valor no campo.
            if ( $rsLista->getNumLinhas() <= 0 ) {
                 SistemaLegado::executaFrameOculto("jQuery('#inCodPlano', window.parent.frames['telaPrincipal'].document.frm).val('');");
                 SistemaLegado::executaFrameOculto("jQuery('#stNomConta', window.parent.frames['telaPrincipal'].document.frm).html('&nbsp;');");
                 SistemaLegado::exibeAviso(urlencode( $_POST[$_GET['stNomCampoCod']] )." - Não é uma Conta de Banco","n_incluir","erro");
            } else {
                $stDescricao = $rsLista->getCampo ( 'nom_conta' );
            }
        } elseif ($_GET['stTipoBusca'] == 'tes_pagamento_arrecadacao') {

            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n(( pb.cod_banco is not null AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  ";
                $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.4.%' ))  ";
                $stFiltro .= "\n   ) AND ";
            } else {
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' )  ";
                $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
            $stFiltro .= "\n   pa.cod_plano = " .$_POST[$_GET['stNomCampoCod']] . " AND ";

            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }

            $stFiltro = " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4);
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';

            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

            # Quando não encontra nenhum valor no campo.
            if ( $rsLista->getNumLinhas() <= 0 ) {
                SistemaLegado::executaFrameOculto("jQuery('#inCodPlano', window.parent.frames['telaPrincipal'].document.frm).val('');");
                SistemaLegado::executaFrameOculto("jQuery('#stNomConta', window.parent.frames['telaPrincipal'].document.frm).html('&nbsp;');");
                SistemaLegado::exibeAviso(urlencode( $_POST[$_GET['stNomCampoCod']] )." - Não é uma Conta de Banco","n_incluir","erro");
            } else {
                $stDescricao = $rsLista->getCampo ( 'nom_conta' );
            }
        } elseif ($_GET['stTipoBusca'] == "bordero_transf") {

             if ($_REQUEST['stTipoTransacao'] == "6") {

                $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

                $obRContabilidadePlanoContaBanco->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
                $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
                if ($_REQUEST['inCodEntidade']) {
                   $obRContabilidadePlanoContaBanco->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                }
                $obErro = $obRContabilidadePlanoContaBanco->consultar();

                $codAgencia =     $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
                if ($codAgencia <> "") {
                   if($_REQUEST['inCodEntidade'] AND ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade() <> $_REQUEST['inCodEntidade']))
                       SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
                   else
                       $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
                } else {
                       SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");
                }
             } elseif ($_REQUEST['stTipoTransacao'] == "7") {

                 if ($_REQUEST['inCodEntidade']) {
                    $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                 }
                 $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
                 $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                 $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaTransferenciaEntidadeDiferente($rsRecordSet);
                 $stDescricao = $rsRecordSet->getCampo("nom_conta");
             } elseif ($_REQUEST['stTipoTransacao'] == "8") {

                 if ($_REQUEST['inCodEntidade']) {
                    $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                 }
                 $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
                 $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                 $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaConsignacao($rsRecordSet);
                 $stDescricao = $rsRecordSet->getCampo("nom_conta");
            }

        } elseif ($_GET['stTipoBusca'] == 'orcamento_extra') {
            if ($_POST['stTipoReceita'] == 'orcamentaria') {
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '4' );
            } elseif ($_POST['stTipoReceita'] == 'extra') {
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '1.1.2' );
            }
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );

            if ($_REQUEST['tipoBusca2'] == 'receitas_primarias') {
                $obRContabilidadePlanoContaAnalitica->boFiltraReceitasPrimarias=true;
                $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnalitica( $rsRecordSet );
            } else {
                $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoConta( $rsRecordSet );
            }

            if ( substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 1 ) == '4' or substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 5 ) == '1.1.2' ) {
                $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            } else {
                SistemaLegado::exibeAviso( "Esta conta não é Extra-Orçamentaria.","","erro" );
            }

        } elseif ($_GET['stTipoBusca'] == 'tes_transf') {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaTransferencia( $rsRecordSet );
            if (!$obErro->ocorreu()) {
                $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            }
        } elseif ($_GET['stTipoBusca'] == 'tes_pag') {
            $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();
            $obRContabilidadePlanoBanco->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obErro = $obRContabilidadePlanoBanco->listarPlanoContaPagamento( $rsRecordSet );
            if (!$obErro->ocorreu()) {
                $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            }
        } elseif ($_GET['stTipoBusca'] == 'tes_arrec') {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaArrecadacao( $rsRecordSet );
            if (!$obErro->ocorreu()) {
                $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            }
        } elseif ($_GET['stTipoBusca'] == 'estrutural') {
            $obRContabilidadePlanoContaAnalitica->setCodEstrutural( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
        } elseif ($_GET['stTipoBusca'] == 'contaSinteticaAtivoPermanente') {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaAtivoPermanente);

            if ($rsSinteticaAtivoPermanente->getNumLinhas() > 0) {
                $stDescricao = $rsSinteticaAtivoPermanente->getCampo('nom_conta');
            }
        } elseif ($_GET['stTipoBusca'] == 'contaContabilDepreciacaoAcumulada') {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaDepreciacaoAcumulada);
            if ($rsSinteticaDepreciacaoAcumulada->getNumLinhas() > 0) {
                $stDescricao = $rsSinteticaDepreciacaoAcumulada->getCampo('nom_conta');
            }
        } elseif ($_GET['stTipoBusca'] == 'contaSinteticaDepreciacao') {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaDepreciacao($rsSinteticaDepreciacao);

            if ($rsSinteticaDepreciacao->getNumLinhas() > 0) {
                $stDescricao = $rsSinteticaDepreciacao->getCampo('nom_conta');
            }
        } else {
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
        }
    if (empty($stDescricao)) {
       SistemaLegado::exibeAviso("Esta conta não é válida!","n_incluir","aviso");
     } else {
       SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
    }
 } else {
        $stDescricao = "";
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");    
 }
 break;

}
