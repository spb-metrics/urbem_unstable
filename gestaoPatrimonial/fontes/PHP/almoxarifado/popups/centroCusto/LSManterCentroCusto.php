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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.07

    $Id: LSManterCentroCusto.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php");

$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

  $stFncJavaScript  = " function insereCentroDeCustos(num,nom) {  \n";
  $stFncJavaScript .= " var sNum;                  \n";
  $stFncJavaScript .= " var sNom;                  \n";
  $stFncJavaScript .= " sNum = num;                \n";
  $stFncJavaScript .= " sNom = nom;                \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"  ].".value = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
  $stFncJavaScript .= " window.close();            \n";
  $stFncJavaScript .= " }                          \n";

$stCaminho = CAM_GP_ALM_INSTANCIAS."centroCusto/";

$stAcao = $request->get("stAcao");

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ( $request->get('inCodigo')) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    if ( Sessao::read('filtro') ) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

$obRegra = new RAlmoxarifadoCentroDeCustos;

if ($request->get('stHdnDescricao')) {
   $obRegra->setDescricao($_REQUEST['stHdnDescricao']);
}
if ( $request->get('inCodEntidade') ) {
   foreach ($_REQUEST['inCodEntidade'] as $inCodEntidade) {
      $obRegra->addEntidade();
      $obRegra->roUltimaEntidade->setCodigoEntidade($inCodEntidade);
   }
}

$stFiltro = "";
$stLink   = "";

$stLink .= '&inCodigo='.$request->get('inCodigo');
$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST["nomForm"]."&campoNom=".$_REQUEST["campoNom"]."&campoNum=".$_REQUEST["campoNum"];

$rsLista = new RecordSet;

$stOrder = " ORDER BY centro_custo.descricao ";

if (isset($usuario))
  $obRegra->listarPermissaoUsuario( $rsLista, $stOrder );
else
  $obRegra->listar( $rsLista, $stOrder );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 38 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Responsável" );
$obLista->ultimoCabecalho->setWidth( 28 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Vigência" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_centro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_entidade] - [desc_entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_vigencia" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCentroDeCustos();" );
$obLista->ultimaAcao->addCampo("1","cod_centro");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
