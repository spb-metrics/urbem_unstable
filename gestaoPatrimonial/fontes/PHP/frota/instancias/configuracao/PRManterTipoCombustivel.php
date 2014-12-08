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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterTipoCombustivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaCombustivel.class.php" );

$stPrograma = "ManterTipoCombustivel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaCombustivel = new TFrotaCombustivel();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaCombustivel );

switch ($stAcao) {
    case 'incluir':
        //verifica se nao existe ja no cadastro o nome da marca
        $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, " WHERE nom_combustivel ILIKE '".$_REQUEST['stCombustivel']."' " );

        if ( $rsCombustivel->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um combustível com esta descrição';
        }

        if (!$stMensagem) {
            //recupera o cod_combustivel
            $obTFrotaCombustivel->ProximoCod( $inCodCombustivel );

            //seta os dados e cadastra no sistema
            $obTFrotaCombustivel->setDado( 'cod_combustivel', $inCodCombustivel );
            $obTFrotaCombustivel->setDado( 'nom_combustivel', $_REQUEST['stCombustivel'] );
            $obTFrotaCombustivel->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$inCodCombustivel.' - '.$_REQUEST['stCombustivel'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar':
        //verifica se nao existe ja no cadastro o nome da marca
        $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, " WHERE nom_combustivel ILIKE '".$_REQUEST['stCombustivel']."' AND cod_combustivel <> ".$_REQUEST['inCodCombustivel']." " );

        if ( $rsCombustivel->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um combustível com esta descrição';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTFrotaCombustivel->setDado( 'cod_combustivel', $_REQUEST['inCodCombustivel'] );
            $obTFrotaCombustivel->setDado( 'nom_combustivel', $_REQUEST['stCombustivel'] );
            $obTFrotaCombustivel->alteracao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$_REQUEST['inCodCombustivel'].' - '.$_REQUEST['stCombustivel'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    CASE 'excluir':
        //seta os dados e exclui da base
        $obTFrotaCombustivel->setDado( 'cod_combustivel', $_REQUEST['inCodCombustivel'] );
        $obTFrotaCombustivel->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$_REQUEST['inCodCombustivel'].' - '.$_REQUEST['stNomCombustivel'],"excluir","aviso", Sessao::getId(), "../");

        break;

}

Sessao::encerraExcecao();
