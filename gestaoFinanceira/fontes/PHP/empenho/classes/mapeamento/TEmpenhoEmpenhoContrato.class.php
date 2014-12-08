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
    * Classe de mapeamento da tabela empenho.empenho_contrato
    * Data de Criação: 28/02/2008

    * @author Analista: Tonismar
    * @author Desenvolvedor: Alexandre Melo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.37
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoEmpenhoContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoContrato()
{
    parent::Persistente();
    $this->setTabela("empenho".Sessao::getEntidade().".empenho_contrato");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_empenho');

    $this->AddCampo('exercicio'   ,'char'   ,true  ,'4'  ,true,'TLicitacaoContrato');
    $this->AddCampo('cod_entidade','integer',true  ,''   ,true,'TLicitacaoContrato');
    $this->AddCampo('cod_empenho' ,'integer',true  ,''   ,true,'TEmpenhoEmpenho');
    $this->AddCampo('num_contrato','integer',true  ,''   ,false,'TLicitacaoContrato');

}

function montaRecuperaTodos()
{
    $stSql  = " SELECT exercicio                        \n";
    $stSql .= "      , cod_empenho                      \n";
    $stSql .= "      , cod_entidade                     \n";
    $stSql .= "      , num_contrato                     \n";
    $stSql .= "   FROM empenho.empenho_contrato         \n";
    $stSql .= "   WHERE true                            \n";
    if ($this->getDado('exercicio')) {
        $stSql .= " AND exercicio = '".$this->getDado('exercicio')."' \n";
    }
    if ($this->getDado('cod_empenho')) {
        $stSql .= " AND cod_empenho = ".$this->getDado('cod_empenho')." \n";
    }
    if ($this->getDado('cod_entidade')) {
        $stSql .= " AND cod_entidade = ".$this->getDado('cod_entidade')." \n";
    }
    if ($this->getDado('num_contrato')) {
        $stSql .= " AND num_contrato = ".$this->getDado('num_contrato')." \n";
    }

    return $stSql;
}

function recuperaRelacionamentoEmpenhoContrato(&$rsRecordSet, $stFiltro = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoEmpenhoContrato().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql);

    return $obErro;
}

function montaRecuperaRelacionamentoEmpenhoContrato()
{
    $stSql  = "SELECT ec.num_contrato   			   \n";
    $stSql  .= "		, e.cod_empenho      			   \n";
    $stSql  .= "		, e.exercicio       			   \n";
    $stSql  .= "		, e.cod_entidade      			   \n";
    $stSql  .= "		, e.cod_pre_empenho   			   \n";
    $stSql  .= "		, to_char(e.dt_empenho, 'dd/mm/yyyy') as dt_empenho \n";
    $stSql  .= "		, e.dt_vencimento     			   \n";
    $stSql  .= "		, ie.vl_total as vl_saldo_anterior		\n";
    $stSql  .= "		, e.hora              			   \n";
    $stSql  .= "		, e.cod_categoria     			   \n";
    $stSql  .= "  FROM								  	   \n";
    $stSql  .= "		  empenho.empenho_contrato as ec   \n";
    $stSql  .= "	    , empenho.empenho		   as e    \n";
    $stSql  .= "	    , empenho.pre_empenho	   as pe   \n";
    $stSql  .= "      	, empenho.item_pre_empenho as ie   \n";
    $stSql  .= " WHERE								       \n";
    $stSql  .= "       ec.exercicio    = e.exercicio	   \n";
    $stSql  .= "   AND ec.cod_entidade = e.cod_entidade    \n";
    $stSql  .= "   AND ec.cod_empenho  = e.cod_empenho     \n";
    $stSql .= "    AND e.exercicio     = pe.exercicio	   \n";
    $stSql .= "    AND e.cod_pre_empenho = pe.cod_pre_empenho  \n";
    $stSql .= "	   AND ie.cod_pre_empenho = pe.cod_pre_empenho \n";
    $stSql .= "	   AND ie.exercicio       = pe.exercicio   \n";

    return $stSql;
}

function recuperaProximoContrato(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaProximoContrato().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql);

    return $obErro;
}

function montaRecuperaProximoContrato()
{
    $stSql  = "SELECT ( coalesce(max(num_contrato), 0)   +1 ) as num_contrato     \n";
    $stSql .= "  FROM empenho.empenho_contrato                    \n";

    return $stSql;
}

}
?>
