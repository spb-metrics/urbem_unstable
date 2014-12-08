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
    * Página de lista do fornecedor
    * Data de Criação   : 10/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

     $Id: LSManterAutorizacao.php 60857 2014-11-19 14:54:43Z michel $

    * Casos de uso: uc-uc-03.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php"  );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php");

$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//filtros
$arFiltro = Sessao::read('filtro');

$pg  = $_GET['pg'] ? $_GET['pg']  : 0;
$pos = $_GET['pos']? $_GET['pos'] : 0;

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $pg);
    Sessao::write('pos', $pos);
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$pg);
    Sessao::write('pos',$pos);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

///////// montando filtros

$stFiltros .= " AND licitacao.exercicio = '" . Sessao::getExercicio() . "'";

if ($_REQUEST ['inCodEntidade']) {
    $stFiltros .= " AND entidade.cod_entidade = ". $_REQUEST ['inCodEntidade'] ;
}

if ($_REQUEST['inCodModalidade']) {
    $stFiltros .= " AND licitacao.cod_modalidade = " . $_REQUEST['inCodModalidade'];
}

if ($_REQUEST['inCodigoLicitacao']) {
    $stFiltros .= " AND licitacao.cod_licitacao = ".$_REQUEST['inCodigoLicitacao'];
}

if ($_REQUEST['stDtInicial']) {
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) >= to_date ( '".$_POST['stDtInicial']."' , 'dd/mm/yyyy' )     ";
}

if ($_REQUEST['stDtFinal']) {
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) <=  to_date ( '".$_POST['stDtFinal']."', 'dd/mm/yyyy' )   ";
}

if ($_REQUEST['inCodMapa']) {
    $stFiltros .= " AND mapa_cotacao.cod_mapa = ".$_REQUEST['inCodMapa'];
}

$stFiltros .= " AND NOT EXISTS
                            (
                                SELECT  1
                                  FROM  licitacao.homologacao
                                 WHERE  not homologacao.homologado
                                   AND  ( not exists ( select 1
                                           from licitacao.homologacao_anulada
                                          where homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                                            and homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                                            and homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                                            and homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                                            and homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                                            and homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                            and homologacao_anulada.lote                = homologacao.lote
                                            and homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                                            and homologacao_anulada.cod_item            = homologacao.cod_item
                                            and homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                                            and homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor ) )
                      and homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                      and homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao  )

            -- A Licitação não pode estar anulada.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.licitacao_anulada
                                 WHERE	licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                   AND  licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                   AND  licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                   AND  licitacao_anulada.exercicio      = licitacao.exercicio
                            )

            -- Validação para não existir cotação anulada.
            AND NOT EXISTS (
                                SELECT  1
                                  FROM  compras.cotacao_anulada
                                 WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                   AND  cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                              )

            AND NOT EXISTS (
                                SELECT 1
                                  FROM licitacao.edital_suspenso
                                  JOIN licitacao.edital
                                    ON edital_suspenso.num_edital = edital.num_edital
                                   AND edital_suspenso.exercicio = edital.exercicio
                                  JOIN licitacao.licitacao ll
                                    ON ll.cod_licitacao = edital.cod_licitacao
                                   AND ll.cod_modalidade = edital.cod_modalidade
                                   AND ll.cod_entidade = edital.cod_entidade
                                   AND ll.exercicio = edital.exercicio
                                 WHERE ll.cod_licitacao = licitacao.cod_licitacao
                                   AND ll.cod_modalidade = licitacao.cod_modalidade
                                   AND ll.cod_entidade = licitacao.cod_entidade
                                   AND ll.exercicio = licitacao.exercicio
                           )
            AND EXISTS     (
                                SELECT 1
                                  FROM licitacao.edital
                                 WHERE edital.cod_licitacao = licitacao.cod_licitacao
                                   AND edital.cod_modalidade = licitacao.cod_modalidade
                                   AND edital.cod_entidade = licitacao.cod_entidade
                                   AND edital.exercicio = licitacao.exercicio
                           )
" ;

$obTLicitacaoHomolocacao = new TLicitacaoHomologacao;
$obTLicitacaoHomolocacao->recuperaCotacoesParaEmpenho( $rsCotacoes, $stFiltros );

$obLista = new Lista();

$obLista->setRecordSet( $rsCotacoes );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Entidade');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Modalidade');
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Cod. Licitação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Licitação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Mapa');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [modalidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_licitacao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[data]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_mapa]/[exercicio_mapa]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'selecionar' );
$obLista->ultimaAcao->addCampo( "&inCodCotacao"       , "cod_cotacao"    );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"      , "cod_entidade"   );
$obLista->ultimaAcao->addCampo( "&inCodLicitacao"     , "cod_licitacao"  );
$obLista->ultimaAcao->addCampo( "&inCodModalidade"    , "cod_modalidade" );
$obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
