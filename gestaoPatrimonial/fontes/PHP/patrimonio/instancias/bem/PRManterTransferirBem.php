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
    * Data de Criação: 21/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: PRManterTransferirBem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioHistoricoBem.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php" );

$stPrograma = "ManterTransferirBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioHistoricoBem = new TPatrimonioHistoricoBem();
$obTPatrimonioBem = new TPatrimonioBem();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioHistoricoBem );;

switch ($stAcao) {
    case 'transferir' :

        //verifica se foi incluido pelo menos 1 item
        $boTransferir = false;
        foreach ($_POST as $stKey=>$stValue) {
            if ( strstr( $stKey, 'boTransferir_' ) ) {
                $boTransferir = true;
                break;
            }
        }
        if (!$boTransferir) {
            $stMensagem = 'É necessário informar pelo menos 1 item.';
        }
        //transfere todos os itens
        if (!$stMensagem) {
            $arLocalizacao = explode('/',$_REQUEST['stCodLocalizacao']);

            $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inLocalDestino'] );
            $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
            //$obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );

            foreach ($_POST as $stKey=>$stValue) {
                if ( strstr( $stKey, 'boTransferir_' ) ) {
                    $arBem = explode('_', $stKey);
                    $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $arBem[1] );
                    $obTPatrimonioHistoricoBem->recuperaUltimaLocalizacao( $rsLocalizacao );

                    $boMesmoOrgao = $rsLocalizacao->getCampo('cod_orgao') == $_REQUEST['hdnUltimoOrgaoSelecionado'];
                    //$boMesmoAnoExercico = $rsLocalizacao->getCampo('ano_exercicio') ==  $arCodOrgao[1];
                    $boMesmoLocal = $_REQUEST['inLocalOrigem'] == $_REQUEST['inLocalDestino'];

                    $boMesmaLocalizacao = ($boMesmoOrgao && $boMesmoLocal);

                    if (!$boMesmaLocalizacao) {
                        $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['slSituacao_'.$arBem[1].'_'.$arBem[2]] );
                        $obTPatrimonioHistoricoBem->setDado( 'descricao', $_REQUEST['stNomBem'] );

                        $obTPatrimonioHistoricoBem->inclusao();

                        $inCodResponsavelNovo = $_REQUEST['inCodResponsavel'];
                        $inCodBem = $arBem[1];

                        $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                        $obTPatrimonioBemResponsavel->setDado('cod_bem', $inCodBem);
                        $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel($rsUltimoResponsavel);
                        $inCodResponsavelAtual = $rsUltimoResponsavel->getCampo('numcgm');

                        if ($inCodResponsavelNovo && ($inCodResponsavelAtual != $inCodResponsavelNovo)) {
                           if ($inCodResponsavelAtual) {
                              $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                              $obTPatrimonioBemResponsavel->setDado('cod_bem'  , $inCodBem );
                              $obTPatrimonioBemResponsavel->setDado('timestamp', $rsUltimoResponsavel->getCampo('timestamp') );
                              $obTPatrimonioBemResponsavel->setDado('numcgm'   , $rsUltimoResponsavel->getCampo('numcgm') );
                              $obTPatrimonioBemResponsavel->setDado('dt_fim'   , date('d/m/y') );
                              $obTPatrimonioBemResponsavel->alteracao();
                           }

                           $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                           $obTPatrimonioBemResponsavel->setDado('cod_bem'  , $inCodBem );
                           $obTPatrimonioBemResponsavel->setDado('numcgm'   , $inCodResponsavelNovo );
                           $obTPatrimonioBemResponsavel->inclusao();
                        }
                        $arBemTransferido[] = $arBem[1];
                    } else {
                        $arBemNaoTransferido[] = $arBem[1];
                    }
                }
            }
            if ( count( $arBemNaoTransferido ) > 0 ) {
                $stMensagem = 'Os bens '.implode(',',$arBemNaoTransferido).' já estavam na localização de destino.';
            } else {
                $stMensagem = 'Bens '.implode(',',$arBemTransferido);
            }

            $stListaBens = implode(',',$arBemTransferido);

            if ($_REQUEST['boEmitirTermo'] == 'true') {
                $stCaminho = CAM_GP_PAT_INSTANCIAS."relatorio/OCGeratermoResponsabilidade.php";
                //$stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."'"."&inNumResponsavel=".$_REQUEST['inCodResponsavel']."&stNomResponsavel=".$_REQUEST['stNomResponsavel']."&setPDF=true"."&local_origem=".$_REQUEST['inLocalOrigem']."&local_destino=".$_REQUEST['inLocalDestino']."&lista_bens=".$stListaBens;
                $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inNumResponsavel=".$_REQUEST['inCodResponsavel']."&stNomResponsavel=".$_REQUEST['stNomResponsavel']."&setPDF=true"."&local_origem=".$_REQUEST['inLocalOrigem']."&local_destino=".$_REQUEST['inLocalDestino']."&lista_bens=".$stListaBens;
                if (isset($_REQUEST['demo_valor'])) {
                    $stCampos .= "&demo_valor=1";
                }
                $pgRel=$stCaminho.$stCampos;
                $stJS = " window.parent.frames['oculto'].location ='".$pgRel."'";
                SistemaLegado::executaFrameOculto($stJS);
            }
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,$stMensagem,"incluir","aviso", Sessao::getId(), "../");

        } else {
            SistemaLegado::exibeAviso(urlencode( $stMensagem ).'!',"n_incluir","erro");
        }

        break;
}

Sessao::encerraExcecao();
