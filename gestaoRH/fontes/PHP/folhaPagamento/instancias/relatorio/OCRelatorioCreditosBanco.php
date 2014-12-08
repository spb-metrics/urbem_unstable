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
    * Oculto para geração do recordset do relatório de Creditos por banco
    * Data de Criação: 14/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 27974 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-02-12 11:57:24 -0200 (Ter, 12 Fev 2008) $

    * Casos de uso: uc-04.05.38
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_GRH_FOL_NEGOCIO.'RRelatorioCreditosBanco.class.php'                              );

function dropTmpTable()
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;

    $stSql = " DROP TABLE ".$_REQUEST['stNomeTabela'];
    $obErro = $obConexao->__executaDML( $stSql, $boTransacao );

    if ($obErro->ocorreu()) {
        $stJs = "alertaAviso('@Erro ao excluir tabela temporária!','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

$arDados = array();
$obRRelatorio = new RRelatorio;
$obRRelatorioCreditosBanco = new RRelatorioCreditosBanco;
$obRRelatorioCreditosBanco->geraRecordSet( $rsRecordset );
$sessao->transf5 = $rsRecordset;

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCreditosBanco.php" );
?>
