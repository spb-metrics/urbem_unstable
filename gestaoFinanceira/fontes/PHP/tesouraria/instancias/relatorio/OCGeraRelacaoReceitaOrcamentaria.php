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
    * Página de filtro do relatório
    * Data de Criação   : 31/17/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-02.04.36
*/

/*
$Log$
Revision 1.5  2007/09/10 15:03:10  hboaventura
Ticket#10067#

Revision 1.4  2007/08/30 19:38:58  hboaventura
Bug#9931#, Bug#10042#

Revision 1.3  2007/08/23 12:48:36  hboaventura
Bug#9928#

Revision 1.2  2007/08/20 15:03:16  hboaventura
Bug#9936#

Revision 1.1  2007/08/08 14:07:41  hboaventura
uc_02-04-36

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$preview = new PreviewBirt(2,30,1);
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt('2.5.0');

if (count($_REQUEST['inCodigoEntidadesSelecionadas'])>0) {
    $preview->addParametro( "cod_entidade",implode(',',$_REQUEST['inCodigoEntidadesSelecionadas']) );
} else {
    $preview->addParametro( "cod_entidade","" );
}

//seta a entidade
if (count($_REQUEST['inCodigoEntidadesSelecionadas'])>0) {
    foreach ($_REQUEST['inCodigoEntidadesSelecionadas'] as $array) {
        $arEntidades.=  $array.", ";
    }
    $arEntidades = substr( $arEntidades, 0, strlen($arEntidades)-2 );
    $preview->addParametro( "cod_entidade",$arEntidades );
} else {
    $preview->addParametro( "cod_entidade","" );
}

//seta a data
$preview->addParametro( "data_ini",$_REQUEST['stDataInicial']);
$preview->addParametro( "data_fim",$_REQUEST['stDataFinal']);

//seta as o código estrutural das receitas
if ($_REQUEST['stCodEstruturalInicial'] != '' AND $_REQUEST['stCodEstruturalFinal'] != '') {
    $preview->addParametro( "estrutural", " BETWEEN '".$_REQUEST['stCodEstruturalInicial']."' AND '".$_REQUEST['stCodEstruturalFinal']."' " );
} elseif ($_REQUEST['stCodEstruturalInicial'] == '' AND $_REQUEST['stCodEstruturaFinal'] != '') {
    $preview->addParametro( "estrutural", " <= '".$_REQUEST['stCodEstruturalFinal']."' " );
} elseif ($_REQUEST['stCodEstruturalInicial'] != '' AND $_REQUEST['stCodEstruturalFinal'] == '') {
    $preview->addParametro( "estrutural", " >= '".$_REQUEST['stCodEstruturalInicial']."' " );
} else {
    $preview->addParametro( "estrutural", "" );
}

//seta o cod_reduzido
if ($_REQUEST['inReceitaInicial'] != '' AND $_REQUEST['inReceitaFinal'] != '') {
    $preview->addParametro("cod_reduzido", " BETWEEN ".$_REQUEST['inReceitaInicial']." AND ".$_REQUEST['inReceitaFinal']);
} elseif ($_REQUEST['inReceitaInicial'] == '' AND $_REQUEST['inReceitaFinal'] != '') {
    $preview->addParametro("cod_reduzido", " <= ".$_REQUEST['inReceitaFinal']);
} elseif ($_REQUEST['inReceitaInicial'] != '' AND $_REQUEST['inReceitaFinal'] == '') {
    $preview->addParametro("cod_reduzido", " >= ".$_REQUEST['inReceitaInicial']);
} else {
    $preview->addParametro("cod_reduzido", "");
}

//seta o cod_plano
if ($_REQUEST['inCodContaBancoInicial'] != '' AND $_REQUEST['inCodContaBancoFinal'] != '') {
    $preview->addParametro( "conta_banco", " BETWEEN ".$_REQUEST['inCodContaBancoInicial']." AND ".$_REQUEST['inCodContaBancoFinal'] );
} elseif ($_REQUEST['inCodContaBancoInicial'] == '' AND $_REQUEST['inCodContaBancoFinal'] != '') {
    $preview->addParametro( "conta_banco", " <= ".$_REQUEST['inCodContaBancoFinal'] );
} elseif ($_REQUEST['inCodContaBancoInicial'] != '' AND $_REQUEST['inCodContaBancoFinal'] == '') {
    $preview->addParametro( "conta_banco", " >= ".$_REQUEST['inCodContaBancoInicial'] );
} else {
    $preview->addParametro( "conta_banco", "" );
}

if ($_REQUEST['inCodRecurso'] != '') {
    $preview->addParametro( 'recurso', $_REQUEST['inCodRecurso'] );
} else {
    $preview->addParametro( 'recurso', '' );
}

if( $_REQUEST['inCodUso']<>NULL && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'] )
     $preview->addParametro( 'destinacaorecurso', $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );
else $preview->addParametro( 'destinacaorecurso', '');

if ( $_REQUEST['inCodDetalhamento'] )
     $preview->addParametro( 'cod_detalhamento', $_REQUEST['inCodDetalhamento'] );
else $preview->addParametro( 'cod_detalhamento', '' );

if ($_REQUEST['stTipoRelatorio'] != '') {
    $preview->addParametro( 'tipo_relatorio', $_REQUEST['stTipoRelatorio'] );
} else {
    $preview->addParametro( 'tipo_relatorio', '' );
    $preview->addParametro( 'ordenacao', ' arrecadacao.timestamp_arrecadacao ASC ');
}

$preview->preview();
?>
