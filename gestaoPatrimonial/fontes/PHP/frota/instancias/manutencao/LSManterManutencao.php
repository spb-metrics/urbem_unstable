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
 * Data de Criação: 29/11/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: LSManterManutencao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.14

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php";

$stPrograma = "ManterManutencao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."manutencao/";

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg']  : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if ( Sessao::read('filtro') ) {
    foreach ( Sessao::read('filtro') as $key => $value ) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando' , true);

//seta os filtros
$stFiltro = "   NOT EXISTS  (
                                SELECT  1
                                  FROM  frota.manutencao_anulacao
                                 WHERE  manutencao_anulacao.cod_manutencao = manutencao.cod_manutencao
                                   AND  manutencao_anulacao.exercicio = manutencao.exercicio
                            ) AND ";

# Filtro para não listar Manutenções que tenham saída pelo Almoxarifado.
$stFiltro .= "  NOT EXISTS  (
                                SELECT  1
                                  FROM  almoxarifado.lancamento_manutencao_frota
                                 WHERE  lancamento_manutencao_frota.cod_manutencao = manutencao.cod_manutencao
                                   AND  lancamento_manutencao_frota.exercicio      = manutencao.exercicio
                            ) AND ";

$stFiltro .= "  NOT EXISTS (
                                SELECT  1
                                  FROM  almoxarifado.lancamento_autorizacao
                            INNER JOIN  frota.autorizacao
                                    ON  (     lancamento_autorizacao.cod_autorizacao = autorizacao.cod_autorizacao
                                         AND lancamento_autorizacao.exercicio = autorizacao.exercicio
                                        )

                            INNER JOIN  frota.efetivacao
                                    ON  (     efetivacao.cod_autorizacao = autorizacao.cod_autorizacao
                                          AND efetivacao.exercicio_autorizacao = autorizacao.exercicio
                                          AND efetivacao.exercicio_manutencao = manutencao.exercicio
                                          AND efetivacao.cod_manutencao = manutencao.cod_manutencao
                                        )
                          ) AND ";

//seta o cod_autorizacao
if ($_REQUEST['inCodAutorizacao'] != '') {
    $arAutorizacao = explode( '/', $_REQUEST['inCodAutorizacao'] );
    $arAutorizacao[1] = ( $arAutorizacao[1] == '' ) ? Sessao::getExercicio() : $arAutorizacao[1];
    $stFiltro .= " EXISTS( SELECT 1
                             FROM frota.efetivacao
                            WHERE efetivacao.cod_autorizacao = ".$arAutorizacao[0]."
                              AND efetivacao.exercicio_autorizacao = '".$arAutorizacao[1]."'
                              AND efetivacao.cod_manutencao = manutencao.cod_manutencao
                              AND efetivacao.exercicio_manutencao = manutencao.exercicio) AND ";
}

//seta o cod_manutencao
if ($_REQUEST['inCodManutencao'] != '') {
    $arManutencao = explode( '/', $_REQUEST['inCodManutencao'] );
    $arManutencao[1] = ( $arManutencao[1] == '' ) ? Sessao::getExercicio() : $arManutencao[1];
    $stFiltro .= " manutencao.cod_manutencao = ".$arManutencao[0]." AND
                   manutencao.exercicio = '".$arManutencao[1]."' AND ";
}

//seta o prefixo
if ($_REQUEST['stPrefixo'] != '') {
    $stFiltro .= " veiculo.prefixo = '".$_REQUEST['stPrefixo']."' AND ";
}

//seta a placa
if ($_REQUEST['stNumPlaca'] != '') {
    $stFiltro .= " veiculo.placa = '".str_replace('-','',$_REQUEST['stNumPlaca'])."' AND ";
}

//seta o cod_item
if ($_REQUEST['inCodItem'] != '') {
    $stFiltro .= " EXISTS ( SELECT 1
                              FROM frota.manutencao_item
                             WHERE manutencao_item.cod_manutencao = manutencao.cod_manutencao
                               AND manutencao_item.exercicio = manutencao.exercicio
                               AND manutencao_item.cod_item = ".$_REQUEST['inCodItem']."
                          ) AND ";
}

if ($_REQUEST['inCodEntidade'] != '') {
    $stFiltro .= "  EXISTS ( SELECT veiculo_propriedade.cod_veiculo
                                 , MAX(veiculo_propriedade.timestamp) AS timestamp
                              FROM frota.veiculo_propriedade
                        INNER JOIN frota.proprio
                                ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                               AND proprio.timestamp = veiculo_propriedade.timestamp
                        INNER JOIN patrimonio.bem_comprado
                                ON bem_comprado.cod_bem = proprio.cod_bem
                             WHERE veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                               AND bem_comprado.cod_entidade IN ( ".implode(',',$_REQUEST['inCodEntidade'])." )
                          GROUP BY veiculo_propriedade.cod_veiculo)       ";
}

if ($stFiltro) {
    $stFiltro = ' WHERE '.substr($stFiltro,0,-4);
}

//recupera os dados do banco de acordo com o filtro
$obTFrotaManutencao = new TFrotaManutencao();
$obTFrotaManutencao->recuperaManutencaoSintetico( $rsManutencao, $stFiltro, " ORDER BY TO_DATE(dt_manutencao::varchar, 'yyyy-mm-dd'), cod_manutencao DESC" );

//instancia uma nova lista
$obLista = new Lista;
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsManutencao );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Manutenção" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Veículo" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_manutencao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "dt_manutencao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_veiculo] - [placa_masc] - [nom_marca] - [nom_modelo]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodManutencao", "cod_manutencao" );
$obLista->ultimaAcao->addCampo( "&stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "&inCodVeiculo", "cod_veiculo" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[cod_manutencao]/[exercicio]" );

$obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

?>
