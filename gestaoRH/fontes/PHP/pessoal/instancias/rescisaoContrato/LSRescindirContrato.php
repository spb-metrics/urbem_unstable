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
    * Página de Listagem de Rescindir Contrato
    * Data de Criação   : 17/10/2005

    * @author Analista: Vandr? Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.04.44

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."rescisaoContrato/";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obRPessoalRescisaoContrato = new RPessoalRescisaoContrato;

switch ($stAcao) {
    case 'incluir': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    default       : $pgProx = $pgForm;
}

//Mantem filtro e paginacao
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $slink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

//Monta o filtro
if ( strlen($_REQUEST["inNumCGM"]) > 0 ) {
    $obRPessoalRescisaoContrato->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->setNumCGM($_REQUEST["inNumCGM"]);
}
if ( (strlen($_REQUEST["inContrato"]) > 0) && (strtolower($_REQUEST['inContrato']) != "todos"  ) ) {
    $obRPessoalRescisaoContrato->obRPessoalContratoServidor->setRegistro($_REQUEST["inContrato"]);
}

if ($stAcao == "incluir") {

    $obRPessoalRescisaoContrato->listarRescisaoContrato( $rsRescisaoContrato );

    $obLista = new Lista;
    $obLista->setRecordSet( $rsRescisaoContrato );

    $obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Lotação" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[orgao] - [descricao]" );
    $obLista->commitDado();

    $stAcao = "RESCINDIR";
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo( "&inRegistro"      , "registro"        );
    $obLista->ultimaAcao->addCampo( "&inNumCGM"        , "numcgm"          );
    $obLista->ultimaAcao->addCampo( "&stNomCGM"        , "nom_cgm"         );
    $obLista->ultimaAcao->addCampo( "&dtPosse"         , "dt_posse"        );
    $obLista->ultimaAcao->addCampo( "&dtNomeacao"      , "dt_nomeacao"     );
    $obLista->ultimaAcao->addCampo( "&dtAdmissao"      , "dt_admissao"     );
    $obLista->ultimaAcao->addCampo( "&inCodSubDivisao" , "cod_sub_divisao" );
    $obLista->ultimaAcao->addCampo( "&inCodContrato"   , "cod_contrato"    );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

    $obLista->commitAcao();
    $obLista->show();

} elseif ($stAcao == "excluir") {

    $obRPessoalRescisaoContrato->listarRescisaoContratoRescindidos( $rsRescisaoContrato );

    $obLista = new Lista;
    $obLista->setRecordSet( $rsRescisaoContrato );

    $obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da Rescisão" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_rescisao" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo( "&inCodContrato" , "cod_contrato"         );
    $obLista->ultimaAcao->addCampo( "&inRegistro"    , "registro"             );
    $obLista->ultimaAcao->addCampo( "&stDescQuestao" , "registro" 	  	   	  );
    $obLista->ultimaAcao->addCampo( "&stNomCGM"      , "nom_cgm"              );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
    $obLista->show();
}
?>
