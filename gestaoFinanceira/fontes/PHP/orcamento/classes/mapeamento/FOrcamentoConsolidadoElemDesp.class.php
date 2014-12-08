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
    * Classe de mapeamento da tabela FN_ORCAMENTO_CONSOLIDADO_ELEM_DESP
    * Data de Criação: 02/02/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.23
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoConsolidadoElemDesp extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoConsolidadoElemDesp()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_consolidado_elem_despesa');

    $this->AddCampo('classificacao'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_reduzido'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('descricao'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_orgao'            ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_orgao'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_unidade'          ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_unidade'          ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_funcao'           ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_funcao'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_subfuncao'        ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_subfuncao'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('saldo_inicial'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('suplementacoes'       ,'numeric',false,'14.2',false,false);
    $this->AddCampo('reducoes'             ,'numeric',false,'14.2',false,false);
    $this->AddCampo('empenhado_mes'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('empenhado_ano'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('anulado_mes'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('anulado_ano'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('pago_mes'             ,'numeric',false,'14.2',false,false);
    $this->AddCampo('pago_ano'             ,'numeric',false,'14.2',false,false);
    $this->AddCampo('liquidado_mes'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('liquidado_ano'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('tipo_conta '          ,'varchar',false,''    ,false,false);
    $this->AddCampo('nivel'                ,'integer',false,''    ,false,false);

}

function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                                                                    \n";
    $stSql .= " FROM ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("stDataInicial")."','".$this->getDado("stDataFinal")."','".$this->getDado("stEntidade")."','".$this->getDado("stCodOrgaoInicial")."','".$this->getDado("stCodOrgaoFinal")."','".$this->getDado("stCodUnidadeInicial")."','".$this->getDado("stCodUnidadeFinal")."','".$this->getDado('stDestinacaoRecurso')."','".$this->getDado('inCodDetalhamento')."', ".$this->getDado('inCodFuncao').", ".$this->getDado('inCodSubFuncao').") as retorno( \n";
    $stSql .= "     classificacao   varchar,        \n";
    $stSql .= "     cod_reduzido    varchar,        \n";
    $stSql .= "     descricao       varchar,        \n";
    $stSql .= "     num_orgao       integer,        \n";
    $stSql .= "     nom_orgao       varchar,        \n";
    $stSql .= "     num_unidade     integer,        \n";
    $stSql .= "     nom_unidade     varchar,        \n";
    $stSql .= "     saldo_inicial   numeric,        \n";
    $stSql .= "     suplementacoes  numeric,        \n";
    $stSql .= "     reducoes        numeric,        \n";
    $stSql .= "     empenhado_mes   numeric,        \n";
    $stSql .= "     empenhado_ano   numeric,        \n";
    $stSql .= "     anulado_mes     numeric,        \n";
    $stSql .= "     anulado_ano     numeric,        \n";
    $stSql .= "     pago_mes        numeric,        \n";
    $stSql .= "     pago_ano        numeric,        \n";
    $stSql .= "     liquidado_mes   numeric,        \n";
    $stSql .= "     liquidado_ano   numeric,        \n";
    $stSql .= "     tipo_conta      varchar,        \n";
    $stSql .= "     nivel           integer         \n";
   $stSql .= "     )                                                                                                       \n";

    return $stSql;
}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";
    $stSql .= " SELECT SUM(func.vl_original) FROM                           ".$stQuebra;
    $stSql .= " ( ".$this->montaRecuperaTodos()." ) as func                 ".$stQuebra;
    $stSql .= " WHERE                                                       ".$stQuebra;
    $stSql .= "     cod_despesa IS NOT NULL                                 ".$stQuebra;

    return $stSql;
}

function recuperaTodosSinteticos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $this->setTabela('orcamento.fn_consolidado_elem_despesa_sintetica');

    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaTodos().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
