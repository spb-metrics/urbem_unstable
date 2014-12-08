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
    * Página de Lista para Definir Vencimentos
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: LSManterVencimentos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.10  2006/10/23 10:26:31  cercato
formatando cod_grupo.

Revision 1.9  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php"  );
include_once( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVencimentos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OCManterCalendario.php";
$pgJS   = "JSManterCalendario.js";

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/instancias/calendarioFiscal/";

//$obRARRCalendarioFiscal = new RARRCalendarioFiscal;
$obRARRGrupo  = new RARRGrupoVencimento( new RARRCalendarioFiscal );

$stAcao = $request->get('stAcao');
if ($stAcao == "") {
    $stAcao = "incluir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"] = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( "link", $link );
//MONTA FILTRO
if ($_REQUEST["inCodGrupo"]) {
    $arDadosGrupoCredito = explode( "/", $_REQUEST["inCodGrupo"] );
    $obRARRGrupo->roRARRCalendarioFiscal->setCodigoGrupo( $arDadosGrupoCredito[0] );
    $obRARRGrupo->roRARRCalendarioFiscal->setAnoExercicio( $arDadosGrupoCredito[1] );
}

//if ($_REQUEST['inCodigoCredito']) {
//    $obRARRGrupo->roRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoCredito'] );
//}

$stMascara = "";
$obRARRGrupoMascara = new RARRGrupo;
$obRARRGrupoMascara->RecuperaMascaraGrupoCredito( $stMascara );
$inTamanhoMascara = strlen( $stMascara );

$obRARRGrupo->roRARRCalendarioFiscal->listarGrupoVencimentos( $rsGrupoVencimento );

if ($rsGrupoVencimento->getNumLinhas() > 0) {
    $arDados = $rsGrupoVencimento->getElementos();
    for ( $inX=0; $inX<$rsGrupoVencimento->getNumLinhas(); $inX++ ) {
        $arDados[$inX]["cod_grupo_lst"] = sprintf("%0".$inTamanhoMascara."d", $arDados[$inX]["cod_grupo"]);
    }

    $rsGrupoVencimento->preenche( $arDados );
    $rsGrupoVencimento->setPrimeiroElemento();
}

$obLista = new Lista;
$obLista->setRecordSet( $rsGrupoVencimento );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo de Créditos" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercício" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo de Vencimentos" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_grupo_lst" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "ano_exercicio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_vencimento" );
$obLista->commitDado();
$obLista->addAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigoGrupo"        , "cod_grupo"         );
$obLista->ultimaAcao->addCampo("&inCodigoGrupoLst"        , "cod_grupo_lst"         );
$obLista->ultimaAcao->addCampo("&stDescricaoCredito"   , "descricao_credito" );
$obLista->ultimaAcao->addCampo("&stExercicio"          , "ano_exercicio"     );
$obLista->ultimaAcao->addCampo("&stDescricaoVencimento", "descricao_vencimento" );
$obLista->ultimaAcao->addCampo("&dtValorIntegral"      , "data_vencimento_parcela_unica" );
$obLista->ultimaAcao->addCampo("&inCodigoVencimento"   , "cod_vencimento" );

$obLista->ultimaAcao->addCampo("&inLimiteInicial"      , "limite_inicial" );
$obLista->ultimaAcao->addCampo("&inLimiteFinal"        , "limite_final" );
$obLista->ultimaAcao->addCampo("&boUtilizarUnica"      , "utilizar_unica" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
