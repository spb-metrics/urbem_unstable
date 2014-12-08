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
    * Oculto da PopUp de Localizacao
    * Data de Criação   : 14/02/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou;

    * @ignore

    * $Id: OCBuscaLocalizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.7  2006/09/15 15:04:13  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacaoCombos.class.php" );
if ($_REQUEST["stCtrl"]) {
    $obMontaLocalizacao = new MontaLocalizacaoCombos;
    $obMontaLocalizacao->setCadastroLocalizacao( false );
    $obMontaLocalizacao->setPopup( true );
    switch ($_REQUEST["stCtrl"]) {
        case "preencheProxCombo":
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"];
            if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
                $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
                $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
                $inPosicao = $_REQUEST["inPosicao"] - 1;
            }
            $arChaveLocal = explode("-" , $stChaveLocal );
            $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
            $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
            $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
            $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
        break;
        case "preencheCombos":
            $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
            $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
            $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
            $obMontaLocalizacao->preencheCombos();
        break;
    }
}
    if ($_GET['stTipoBusca'] == "nomLocalizacao") {
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacao'] );
        if ( $_REQUEST['stChaveLocalizacaoLoteamento'] )
            $obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacaoLoteamento'] );
        $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
        $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
        $stCodigo = $rsLocalizacao->getCampo("cod_localizacao");
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
    } elseif ($_GET['stTipoBusca'] == "buscaReduzido") {
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorReduzido( $_REQUEST['stChaveLocalizacao'] );
        $obRCIMLocalizacao->setCodigoNivel  ( $_REQUEST["inCodigoNivel"]-1);
        $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
        $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
    }