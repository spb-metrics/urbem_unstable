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
    * Classe de mapeamento da tabela FN_ORCAMENTO_SOMATORIO_DESPESA
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoAnexo1Despesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoAnexo1Despesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_orcamento_somatorio_despesa');

    $this->AddCampo('cod_conta'             ,'integer',false,''    ,false,false);
    $this->AddCampo('nivel'                 ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('classificacao'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('classificacao_reduzida','varchar',false,''    ,false,false);
    $this->AddCampo('valor'                 ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                               \n";
    $stSql .= "     * ,                                                                              \n";
    $stSql .= "     CASE WHEN nivel > 4 THEN                                                         \n";
    $stSql .= "         4                                                                            \n";
    $stSql .= "     ELSE                                                                             \n";
    $stSql .= "         nivel                                                                        \n";
    $stSql .= "     END as alinhamento,                                                              \n";
    $stSql .= "     fnorcamentoanexo2despesa (nivel) as coluna,                                      \n";
    $stSql .= "     fnorcamentoanexo2despesaconta () as conta                                        \n";
    $stSql .= " FROM                                                                                 \n";
    $stSql .= "               ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("inCodEntidade")."','".$this->getDado("inNumOrgao")."','".$this->getDado("inNumUnidade")."') \n";
    $stSql .= "     as retorno(cod_conta integer, nivel integer, descricao varchar,                  \n";
    $stSql .= "               classificacao varchar,classificacao_reduzida varchar,                  \n";
    $stSql .= "               valor numeric)                                                         \n";

    return $stSql;
}

function recuperaTodosBalanco(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $this->setTabela('orcamento.fn_orcamento_somatorio_despesa_balanco');

    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTodosBalanco().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodosBalanco()
{
    $stSql  = " SELECT                                                                               \n";
    $stSql .= "     * ,                                                                              \n";
    $stSql .= "     CASE WHEN nivel > 4 THEN                                                         \n";
    $stSql .= "         4                                                                            \n";
    $stSql .= "     ELSE                                                                             \n";
    $stSql .= "         nivel                                                                        \n";
    $stSql .= "     END as alinhamento,                                                              \n";
    $stSql .= "     fnorcamentoanexo2despesa (nivel) as coluna,                                      \n";
    $stSql .= "     fnorcamentoanexo2despesaconta () as conta                                        \n";
    $stSql .= " FROM                                                                                 \n";
    $stSql .= "               ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("inCodEntidade")."','".$this->getDado("inNumOrgao")."','".$this->getDado("inNumUnidade")."','".$this->getDado("stDataInicial")."','".$this->getDado("stDataFinal")."','".$this->getDado("inCodDemDespesa")."') \n";
    $stSql .= "     as retorno(cod_conta integer, nivel integer, descricao varchar,                  \n";
    $stSql .= "               classificacao varchar,classificacao_reduzida varchar,                  \n";
    $stSql .= "               valor numeric)                                                         \n";

    return $stSql;
}

}
