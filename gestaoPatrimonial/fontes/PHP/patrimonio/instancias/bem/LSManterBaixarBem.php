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
  * Data de Criação: 27/09/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * @package URBEM
  * @subpackage

  * Casos de uso: uc-03.01.06

  $Id: LSManterBaixarBem.php 64184 2015-12-11 14:09:44Z arthur $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php";

$stPrograma = "ManterBaixarBem";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

# Seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."bem/";

$arFiltro = Sessao::read('filtro');

# Seta o filtro na sessao e vice-versa
if (!Sessao::read('paginando')) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($request->get('pg') ? $request->get('pg') : 0));
    Sessao::write('pos',($request->get('pos') ? $request->get('pos') : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg' ,$request->get('pg'));
    Sessao::write('pos',$request->get('pos'));
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

//seta os filtros

if ($request->get('inCodBemInicio') != '' AND $request->get('inCodBemFinal') == '') {
    $stFiltro .= " bem.cod_bem = ".$request->get('inCodBemInicio')." AND ";
}

if ($request->get('inCodBemInicio') != '' AND $request->get('inCodBemFinal') != '') {
    $stFiltro .= " bem.cod_bem BETWEEN ".$request->get('inCodBemInicio')." AND ".$request->get('inCodBemFinal')." AND ";
}

if ($request->get('stDataInicial') != '' AND $request->get('stDataFinal')) {
    $stFiltro .= " bem_baixado.dt_baixa BETWEEN TO_DATE('".$request->get('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$request->get('stDataFinal')."','dd/mm/yyyy') AND ";
}

if ($stAcao == 'excluir') {
    $stFiltro .= " EXISTS
                   (
                        SELECT  1
                          FROM  patrimonio.bem_baixado
                         WHERE  bem_baixado.cod_bem = bem.cod_bem
                   ) AND ";
}

if ($stFiltro != '') {
    $stFiltro = " WHERE ".substr($stFiltro,0,-4);
}

$stOrder = ' ORDER BY  bem.cod_bem ';

$obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();
$obTPatrimonioBemBaixado->recuperaRelacionamento( $rsBem, $stFiltro, $stOrder );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('UC-03.01.06');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsBem );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Bem" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Baixa" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Motivo" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_bem] - [descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_baixa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "motivo" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodBem", "cod_bem" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "cod_bem" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink($stCaminho.$pgForm."?".Sessao::getId().$stLink);
} else {
    $obLista->ultimaAcao->setLink($stCaminho.$pgProc.'?'.Sessao::getId().$stLink);
}

$obLista->commitAcao();
$obLista->show();

?>